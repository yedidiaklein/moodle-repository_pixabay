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
 * Main repository_pixabay class.
 *
 * @package    repository_pixabay
 * @copyright  2018
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_pixabay extends repository {

    /**
     * Get Listing function
     *
     * This function is for init of repository.
     * @param string $path
     * @param int $page
     *
     * @return array
     */
    public function get_listing($path = '', $page = '') {
        return array('list' => array());
    }

    /**
     * Search function
     *
     * This is the function that do the search in pixabay and return an array of images.
     * @param string $searchtext
     * @param int $page
     *
     * @return array
     */
    public function search($searchtext, $page = 0) {
        global $SESSION;
        $perpage = 21;
        $key = get_config('pixabay', 'key');
        $sort = optional_param('pixabay_sort', 'popular', PARAM_TEXT);
        $safesearch = optional_param('pixabay_safesearch', 'true', PARAM_TEXT);
        if (($searchtext == "") && (isset($SESSION->pixabaysearch))) {
            $q = $SESSION->pixabaysearch;
            $sort = $SESSION->pixabaysort;
            $safesearch = $SESSION->pixabaysafesearch;
        } else {
            $q = $searchtext;
            $SESSION->pixabaysearch = $q;
            $SESSION->pixabaysort = $sort;
            $SESSION->pixabaysafesearch = $safesearch;
        }
        if (!$page) {
            $page = 1;
        }
        $url = "https://pixabay.com/api/?key=" . $key . "&q=" . $q . "&order=" . $sort . "&safesearch=" . $safesearch;
        $url .= "&per_page=" . $perpage . "&page=" . $page;
        $json = file_get_contents($url);
        $list = [];

        if (isset($json) && empty($json)) {
            print_error('queryfailed', 'repository_pixabay', '', null,
                get_string('queryfailed_help', 'repository_pixabay'));
        } else {
            $results = json_decode($json);

            foreach ($results->hits as $key => $value) {
                $title = str_replace("https://pixabay.com/", "", $value->pageURL);
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
                    'author' => $value->user,
                    'source' => $value->webformatURL,
                    'license' => 'Creative Commons CC0'
                );
            }
        }

        $ret  = array();
        $ret['nologin'] = false;
        $ret['page'] = (int)$page;
        if ($ret['page'] < 1) {
            $ret['page'] = 1;
        }
        $max = ceil(($results->totalHits) / $perpage);
        $ret['list'] = $list;
        $ret['norefresh'] = true;
        $ret['nosearch'] = false;
        // If the number of results is smaller than $max, it means we reached the last page.
        $ret['pages'] = (empty($list) || count($list) < $max) ? $ret['page'] : -1;
        return $ret;
    }

    /**
     * get type option name function
     *
     * This function is for module settings.
     * @return array
     */
    public static function get_type_option_names() {
        return array_merge(parent::get_type_option_names(), array('key'));
    }

    /**
     * get type config form function
     *
     * This function is the form of module settings.
     *
     * @param object $mform
     * @param string $classname
     *
     * @return none
     */
    public static function type_config_form($mform, $classname = 'repository') {
        parent::type_config_form($mform);
        $key = get_config('repository_pixabay', 'key');
        $mform->addElement('text', 'key', get_string('key', 'repository_pixabay') . " ("
                            . get_string('key_description', 'repository_pixabay') . ")" , array('size' => '40'));
        $mform->setDefault('key', $key);
        $mform->setType('key', PARAM_RAW_TRIMMED);
    }

    /**
     * check login function
     *
     * This function help showing the search form.
     * @return bool
     */
    public function check_login() {
        return !empty($this->keyword);
    }

    /**
     * print login function
     *
     * This function generates the search form.
     * @param bool $ajax
     *
     * @return array
     */
    public function print_login($ajax = true) {
        $ret = array();
        $key = get_config('pixabay', 'key');
        if (trim($key) == "") {
            $warning = "<p class='errorbox'>" . get_string('warning', 'repository_pixabay') . "</p>";
        } else {
            $warning = "";
        }
        $logo = '<a href="https://pixabay.com/" target="_new">
                    <img src="https://pixabay.com/static/img/public/leaderboard_a.png" alt="Pixabay" style="width:100%">
                </a><br>';
        $search = new stdClass();
        $search->type = 'text';
        $search->id   = 'pixabay_search';
        $search->name = 's';
        $search->label = $warning . $logo . get_string('search', 'repository_pixabay').': ';

        $sort = new stdClass();
        $sort->type = 'select';
        $sort->options = array(
            (object)array(
                'value' => 'popular',
                'label' => get_string('popular', 'repository_pixabay')
            ),
            (object)array(
                'value' => 'latest',
                'label' => get_string('latest', 'repository_pixabay')
            )
        );
        $sort->id = 'pixabay_sort';
        $sort->name = 'pixabay_sort';
        $sort->label = get_string('sortby', 'repository_pixabay').': ';

        $safesearch = new stdClass();
        $safesearch->type = 'select';
        $safesearch->options = array(
            (object)array(
                'value' => 'true',
                'label' => get_string('safe', 'repository_pixabay')
            ),
            (object)array(
                'value' => 'false',
                'label' => get_string('unsafe', 'repository_pixabay')
            )
        );

        $safesearch->id = 'pixabay_safesearch';
        $safesearch->name = 'pixabay_safesearch';
        $safesearch->label = get_string('safesearch', 'repository_pixabay').': ';

        $ret['login'] = array($search, $sort, $safesearch);
        $ret['login_btn_label'] = get_string('search');
        $ret['login_btn_action'] = 'search';
        $ret['allowcaching'] = true; // Indicates that login form can be cached in filepicker.js.
        return $ret;
    }

    /**
     * supported returntype function
     *
     * pixaby plugin only return internal links, according to pixabay term of use.
     * @return int
     */
    public function supported_returntypes() {
        return FILE_INTERNAL;
    }

    /**
     * Is this repository accessing private data?
     *
     * @return bool
     */
    public function contains_private_data() {
        return false;
    }

}
