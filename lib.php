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

require_once($CFG->dirroot . '/repository/lib.php');
/**
 * repository_pixabay class
 * This is a class used to browse images from wikimedia
 */

class repository_pixabay extends repository {

    /**
     * Get Listing function
     *
     *
     * @return array
     */
    public function get_listing($path = '', $page = '') {
        return array('list' => array());
    }

    public function search($searchtext, $page = 0) {
        $key = get_config('pixabay','key');
        $q = $searchtext;
        $json = file_get_contents("https://pixabay.com/api/?key=" . $key . "&q=" . $q);
        $results = json_decode($json);

        foreach ($results->hits as $key => $value) {
            $title = str_replace("https://pixabay.com/en/", "", $value->pageURL);
            $title = preg_replace("/-(\d+)\//", ".jpg", $title);
            $list[] = array(
            'shorttitle' => $title,
            'thumbnail_title' => $title,
            'title' => $title,
            'description' => $title,
            'thumbnail' => $value->webformatURL,
            'thumbnail_width' => 150,
            'thumbnail_height' => 100,
            'size' => $value->imageSize,
            'date' => 0,
            'author' => $value->user,
            'source' => $value->webformatURL,
            );
        }

        $ret  = array();
        $ret['nologin'] = true;
        $ret['page'] = (int)$page;
        if ($ret['page'] < 1) {
            $ret['page'] = 1;
        }
        $start = 1;
        $max = 9;
        $ret['list'] = $list;
        $ret['norefresh'] = true;
        $ret['nosearch'] = false;
        // If the number of results is smaller than $max, it means we reached the last page.
        $ret['pages'] = (count($ret['list']) < $max) ? $ret['page'] : -1;
        return $ret;
    }

    public static function get_type_option_names() {
        return array_merge(parent::get_type_option_names(), array('key'));
    }

    public static function type_config_form($mform, $classname = 'repository') {
        parent::type_config_form($mform);
        $key = get_config('repository_pixabay', 'key');
        $mform->addElement('text', 'key', get_string('key', 'repository_pixabay') . " ("
                            . get_string('key_description', 'repository_pixabay') . ")" , array('size' => '40'));
        $mform->setDefault('key', $key);
        $mform->setType('key', PARAM_RAW_TRIMMED);
    }

}

