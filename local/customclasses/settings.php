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
 * Plugin administration pages are defined here.
 *
 * @package     local_customclasses
 * @category    admin
 * @copyright   2017 Your Name <you@example.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if($hassiteconfig){
	$settings = new admin_settingpage('local_customclasses', 'Custom Classes');
    $ADMIN->add('localplugins', $settings);
    $settings->add(new admin_setting_configcheckbox(
						'enable_customclasses', 
						'enable custom classes',
	                    'When installing/upgrading new blocks/plugins/themes/moodle version and before purging cache it is advisable
	                    to disable Custom Classes. See the docs for more info in the '.html_writer::link($CFG->wwwroot.'/local/customclasses/docs/#gotchas', 'Todo and gotchas').' section', 
	                    0)
					);
	
}

/*if ($ADMIN->fulltree) {
	require_once($CFG->dirroot.'/local/customclasses/lib.php');
	 $settings = new admin_settingpage('local_customclasses', 'Custom classes');
	 $settings->add(new admin_setting_configcheckbox(
						'enable_customclasses', 
						'enable custom classes',
	                    '', 
	                    0)
					);
}*/
