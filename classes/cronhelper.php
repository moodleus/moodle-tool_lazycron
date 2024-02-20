<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Cron helper
 *
 * @package    tool_lazycron
 * @copyright  2024 Darko Miletic
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lazycron;

use core\task\manager;
use dml_exception;
use Throwable;

/**
 * Helper class for cron functions.
 *
 * @package   tool_lazycron
 * @copyright 2024 Darko Miletic
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class cronhelper {

    /**
     * @var string last error
     */
    public static $lasterror = '';

    /**
     * Get las user access time and last cron execution if any
     *
     * @return array<string, string>
     */
    public static function lastaccessdata(): array {
        global $DB;
        $result = [];
        try {
            $result = $DB->get_records_sql_menu(
                "(SELECT 'user' AS lasttype, MAX(lastaccess) AS last FROM {user} WHERE deleted=0)
                UNION ALL
                (SELECT 'cron' AS lasttype, MAX(lastruntime) AS last FROM {task_scheduled})"
            );
        } catch (Throwable $exception) {
            static::$lasterror = $exception->getMessage();
        }
        return $result;
    }

    /**
     * Should we execute cron or not?
     *
     * @return bool
     */
    public static function executecron(): bool {
        $result = true;
        try {
            $options = get_config(plugininfo::COMPONENT);
            $optionsok = (
                is_object($options) &&
                property_exists($options, 'lastuserlogin') &&
                property_exists($options, 'lastcronrun') &&
                property_exists($options, 'enabled')
            );
            if ($optionsok && ($options->enabled == plugininfo::CHECKBOXYES)) {
                $lastdata = static::lastaccessdata();
                if (isset($lastdata['user']) && isset($lastdata['cron'])) {
                    $now = time();
                    $lastuseraccessdelay = $now - intval($lastdata['user']);
                    $lastcronexecutiondelay = $now - intval($lastdata['cron']);
                    if (($lastuseraccessdelay > intval($options->lastuserlogin)) &&
                        ($lastcronexecutiondelay < intval($options->lastcronrun))) {
                        $result = false;
                    }
                }
            }
        } catch (Throwable $exception) {
            static::$lasterror = $exception->getMessage();
        }

        return $result;
    }

    /**
     * Get task scheduler overrides
     *
     * @return array
     * @throws dml_exception
     */
    public static function get_overrides(): array {
        $result = [];
        $options = get_config(plugininfo::COMPONENT);
        $optionsok = (
            is_object($options) &&
            property_exists($options, 'lastuserlogin') &&
            property_exists($options, 'lastcronrun') &&
            property_exists($options, 'enabled') &&
            property_exists($options, 'override')
        );
        if ($optionsok) {
            $overridetasks = trim($options->override);
            if (!empty($overridetasks)) {
                $otasks = explode(',', $overridetasks);
                $systasks = manager::get_all_scheduled_tasks();
                $systacklist = [];
                foreach ($systasks as $systask) {
                    if (!empty($systask->get_disabled())) {
                        continue;
                    }
                    $classname = sprintf('\%s', get_class($systask));
                    if (!in_array($classname, $otasks, true)) {
                        continue;
                    }
                    $systacklist[$classname] = [
                        'schedule' => sprintf(
                            '%s %s %s %s %s',
                            $systask->get_minute(),
                            $systask->get_hour(),
                            $systask->get_day(),
                            $systask->get_month(),
                            $systask->get_day_of_week()
                        ),
                        'disabled' => 0,
                    ];
                }
                $systacklist['*'] = [
                    'schedule' => '* * * * *',
                    'disabled' => 1,
                ];
                $result = $systacklist;
            }
        }
        return $result;
    }

    /**
     * Execute cron
     *
     * @return void
     * @throws dml_exception
     */
    public static function run() {
        global $CFG;
        require_once($CFG->libdir.'/clilib.php');

        $executecron = static::executecron();
        // Older Moodle.
        if (version_compare($CFG->version, 2021051700.00, '<')) {
            if (!$executecron) {
                cli_writeln('Skipping cron execution!');
                return;
            }
        } else {
            if (!$executecron) {
                $overrides = static::get_overrides();
                if (empty($overrides)) {
                    cli_writeln('Skipping cron execution!');
                    return;
                }
                cli_writeln('Executing lazy cron overrides.');
                $CFG->scheduled_tasks = $overrides;
            }
        }

        cli_writeln('Executing cron.');

        if (class_exists('\core\local\cli\shutdown') &&
            is_callable(['\core\local\cli\shutdown', 'script_supports_graceful_exit'])) {
            \core\local\cli\shutdown::script_supports_graceful_exit();
        }

        if (class_exists('\core\cron') && is_callable(['\core\cron', 'run_main_process'])) {
            \core\cron::run_main_process();
        } else {
            require_once($CFG->libdir.'/cronlib.php');
            cron_run();
        }
    }
}
