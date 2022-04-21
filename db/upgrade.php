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
 * Mass Actions block capabilities data.
 *
 * @package    block_massaction
 * @copyright  2022 Rose Hulman
 * @author     Matt Davidson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Handles upgrades that add new supported formats.
 *
 * @param string $addformat
 */
function add_supported_format($addformat) {
    global $DB;

    // Get current settings to update.
    $selectedformats = get_config('block_massaction', 'applicablecourseformats');
    $selectedformats = explode(',', $selectedformats);

    // Gather all possible course formats.
    $pluginmanager = \core_plugin_manager::instance();
    $plugins = [];
    foreach (array_keys($pluginmanager->get_installed_plugins('format')) as $plugin) {
        $plugins[$plugin] = new lang_string('pluginname', 'format_' . $plugin);
    }

    $supportedformats = [];
    foreach ($plugins as $format => $name) {
        if (isset($plugins[$format]) &&
            (in_array($format, $selectedformats) ||
            $format === $addformat)) {
            $supportedformats[$format] = 1;
        }
    }

    // Update settings.
    $params = array("plugin" => "block_massaction",
                    "name" => "applicablecourseformats");
    $setting = $DB->get_record("config_plugins", $params);
    $setting->value = implode(',', array_keys($supportedformats));
    $DB->update_record("config_plugins", $setting);
}

/**
 * Handles upgrades that remove previously supported formats
 *
 * @param string $removeformat
 */
function remove_supported_format($removeformat) {
    global $DB;

    // Get current settings to update.
    $selectedformats = get_config('block_massaction', 'applicablecourseformats');
    $selectedformats = explode(',', $selectedformats);

    // Gather all possible course formats.
    $pluginmanager = \core_plugin_manager::instance();
    $plugins = [];
    foreach (array_keys($pluginmanager->get_installed_plugins('format')) as $plugin) {
        $plugins[$plugin] = new lang_string('pluginname', 'format_' . $plugin);
    }

    $supportedformats = [];
    foreach ($plugins as $format => $name) {
        if (isset($plugins[$format]) &&
            in_array($format, $selectedformats) &&
            $format !== $removeformat) {
            $supportedformats[$format] = 1;
        }
    }

    // Update settings.
    $params = array("plugin" => "block_massaction",
                    "name" => "applicablecourseformats");
    $setting = $DB->get_record("config_plugins", $params);
    $setting->value = implode(',', array_keys($supportedformats));
    $DB->update_record("config_plugins", $setting);
}
