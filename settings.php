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
 * Lazy cron settings.
 *
 * @package    tool_lazycron
 * @copyright  2024 Darko Miletic
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core\task\manager;
use tool_lazycron\plugininfo;

global $CFG;

if ($hassiteconfig) {
    $settings = new admin_settingpage('tool_lazycron', new lang_string('pluginname', plugininfo::COMPONENT));
    $settings->add(
        new admin_setting_heading(
            plugininfo::settingname('heading'),
            new lang_string('introduction', plugininfo::COMPONENT),
            new lang_string('introduction_desc', plugininfo::COMPONENT)
        )
    );
    $settings->add(
        new admin_setting_configcheckbox(
            plugininfo::settingname('enabled'),
            new lang_string('enabled', plugininfo::COMPONENT),
            new lang_string('enabled_desc', plugininfo::COMPONENT),
            plugininfo::CHECKBOXNO,
            plugininfo::CHECKBOXYES,
            plugininfo::CHECKBOXNO
        )
    );
    $settings->add(
        new admin_setting_configduration(
            plugininfo::settingname('lastuserlogin'),
            new lang_string('lastuserlogin', plugininfo::COMPONENT),
            new lang_string('lastuserlogin_desc', plugininfo::COMPONENT),
            600,
            60
        )
    );
    $settings->add(
        new admin_setting_configduration(
            plugininfo::settingname('lastcronrun'),
            new lang_string('lastcronrun', plugininfo::COMPONENT),
            new lang_string('lastcronrun_desc', plugininfo::COMPONENT),
            7200,
            3600
        )
    );

    // Add these settings only on Moodle 3.11+ .
    if (version_compare($CFG->version, 2021051700.00, '>=')) {
        $settings->add(
            new admin_setting_heading(
                plugininfo::settingname('configoverride'),
                new lang_string('configoverride', plugininfo::COMPONENT),
                new lang_string('configoverride_desc', plugininfo::COMPONENT)
            )
        );

        $options = [];
        if (!during_initial_install()) {
            foreach (manager::get_all_scheduled_tasks() as $task) {
                // We take into account only enabled scheduled tasks.
                if (!empty($task->get_disabled())) {
                    continue;
                }
                $classname = sprintf('\%s', get_class($task));
                $options[$classname] = sprintf('%s (%s)', $task->get_name(), $task->get_component());
            }
        }
        $settings->add(
            new admin_setting_configmultiselect(
                plugininfo::settingname('override'),
                new lang_string('override', plugininfo::COMPONENT),
                new lang_string('override_desc', plugininfo::COMPONENT),
                [],
                $options
            )
        );
    }

    /** @var admin_root $ADMIN */
    $ADMIN->add('server', $settings);
}
