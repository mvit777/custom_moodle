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
 * @package     block_custom_courselist
 * @category    admin
 * @copyright   2017 Your Name <you@example.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
   $link ='<a href="'.$CFG->wwwroot.'/blocks/custom_courselist/docs/index.php">Documentation</a>';
    $settings->add(new admin_setting_heading('block_custom_courselist_addheading', '', $link));
    $options = array('all'=>get_string('allcourses', 'block_course_list'), 'own'=>get_string('owncourses', 'block_course_list'));

    $settings->add(new admin_setting_configselect('block_custom_courselist_adminview', get_string('adminview', 'block_course_list'),
                       get_string('configadminview', 'block_course_list'), 'all', $options));
	$settings->add(new admin_setting_configcheckbox('block_custom_courselist_hideallcourseslink', get_string('hideallcourseslink', 'block_course_list'),
                       get_string('confighideallcourseslink', 'block_course_list'), 0));

}
