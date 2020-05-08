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
 * Version details.
 *
 * @package   repository_pixabay
 * @copyright 2018 OpenApp By Yedidia Klein http://openapp.co.il
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = "Pixabay Media";
$string['configplugin'] = "Pixabay Repository Settings";
$string['key'] = "Pixabay API Key";
$string['key_description'] = 'Find your API key at <a href="https://pixabay.com/api/docs/">https://pixabay.com/api/docs/</a>';
$string['search'] = "Search";
$string['safe'] = "Safe";
$string['unsafe'] = "Unsafe";
$string['sortby'] = "Sort By";
$string['popular'] = "Popular";
$string['latest'] = "Latest";
$string['safesearch'] = "Safe Search<br>(only images suitable for all ages should be returned)";
$string['warning'] = "Key isn't set !! You must set it in Pixabay Repository settings.";
$string['queryfailed'] = "Pixabay search query failed, please try again.";
$string['queryfailed_help'] = "Query failed due to search string being greater than 100 character limit imposed by Pixabay, or API hourly request threshold being reached.";
