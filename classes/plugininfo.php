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
 * Plugin class.
 *
 * @package    tool_lazycron
 * @copyright  2024 Darko Miletic
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lazycron;

/**
 * Plugin info helper for tool_lazycron.
 *
 * @copyright  2024 Darko Miletic
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class plugininfo {
    /**
     * Plugin component
     */
    const COMPONENT = 'tool_lazycron';
    /**
     * Checkbox checked
     */
    const CHECKBOXYES = '1';
    /**
     * Checkbox unchecked
     */
    const CHECKBOXNO = '0';

    /**
     * Returns setting name for a plugin configuration
     *
     * @param string $name
     * @return string
     */
    public static function settingname(string $name): string {
        return sprintf('%s/%s', static::COMPONENT, $name);
    }
}
