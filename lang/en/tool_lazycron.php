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
 * Strings for component 'tool_lazycron', language 'en'
 *
 * @package    tool_lazycron
 * @copyright  2024 onwards Darko Miletic
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['configoverride'] = 'Scheduled tasks advanced configuration';
$string['configoverride_desc'] = 'This setting permits user to execute specific scheduled tasks even with Lazy cron
activated. Applies ONLY to Moodle 3.11+ . If you already have <code>$CFG->scheduled_tasks</code> set in your
<code>config.php</code> it will be ignored.';
$string['enabled'] = 'Enabled';
$string['enabled_desc'] = 'When checked plugin functionality is enabled.';
$string['introduction'] = 'Introduction';
$string['introduction_desc'] =
    "This plugin offers new functionality to Moodle cron.
When enabled and properly configured it could lower site usage costs.
The idea is to prevent execution for low usage site. If few users are logging and performing tasks within platform there
is usually no reason to run cron at all.";
$string['lastcronrun'] = 'Last cron execution';
$string['lastcronrun_desc'] = 'If time since last cron execution is lesser than what is defined in this setting do not execute cron.';
$string['lastuserlogin'] = 'Last user login';
$string['lastuserlogin_desc'] = 'If time since last user login is larger than what is defined in this setting do not execute cron.';
$string['override'] = 'Tasks override';
$string['override_desc'] = 'When lazy cron is active, by default, cron will not be executed (ie none of the enabled
scheduled tasks) when skipping conditions are met. Select scheduled tasks to execute even with lazy cron enabled.';
$string['pluginname'] = 'Lazy cron';
$string['privacy:metadata'] = 'The Lazy cron plugin does not store any personal data.';
