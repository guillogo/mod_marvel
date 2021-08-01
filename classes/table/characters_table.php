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
 * Table to display the Marvel data.
 *
 * @package     mod_marvel
 * @copyright   2021 Guillermo Gomez <guigomar@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_marvel\table;

use mod_marvel\helper;
use table_sql;
use stdClass;

class characters_table extends table_sql implements \renderable {
    /**
     * @var int A current page number.
     */
    protected $page;

    /**
     * @var StdClass The Marvel list to display in the table.
     */
    protected $marvellist;

    /**
     * Constructor.
     */
    public function __construct($uniqueid, \moodle_url $url, $marvellist, $download = '', $page = 0, $perpage = 100) {
        parent::__construct($uniqueid);

        $this->pagesize = $perpage;
        $this->page = $page;
        $this->marvellist = $marvellist;

        // Define columns in the table.
        $this->define_table_columns();

        // Define configs.
        $this->define_table_configs($url);

        // Set download status.
        $this->is_downloading($download, 'marvel_list');

    }

    /**
     * Setup the headers for the html table.
     */
    protected function define_table_columns() {
        $columns =
            [
                'thumbnail',
                'id',
                'name',
                'description',
                'comics',
            ];

        $headers =
            [
                get_string('table:thumbnail', 'mod_marvel'),
                get_string('table:id', 'mod_marvel'),
                get_string('table:name', 'mod_marvel'),
                get_string('table:description', 'mod_marvel'),
                get_string('table:viewcomics', 'mod_marvel'),
            ];

        $this->define_columns($columns);
        $this->define_headers($headers);
    }

    /**
     * Define table configs.
     *
     * @param \moodle_url $url url of the page where this table would be displayed.
     */
    protected function define_table_configs(\moodle_url $url) {
        $this->define_baseurl($url);

        // Set table configs.
        $this->collapsible(true);
        $this->sortable(false);
        $this->pageable(false);

        $this->is_downloadable(true);
        $this->show_download_buttons_at([TABLE_P_BOTTOM]);
    }

    /**
     * Query the reader. Store results in the object for use by build_table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        $offset = $pagesize * $this->page;
        $limit = $pagesize;

        // Get the Marvel data for each record.
        $listdata = [];
        //var_dump($this->marvellist->data->results);
        foreach ($this->marvellist->data->results as $marvelitem) {
            $item = new stdClass();
            $item->thumbnail = $marvelitem->thumbnail;
            $item->id = $marvelitem->id;
            $item->name = $marvelitem->name;
            $item->description = $marvelitem->description;
            $item->viewcomicurl = null;//helper::get_comicurl_by_character($marvelitem->id);
            $listdata[] = $item;
        }

        $total = count($listdata);

        if ($this->is_downloading()) {
            $this->rawdata = $listdata;
        } else {
            $this->rawdata = array_slice($listdata, $offset, $limit);
        }

        $this->pagesize($pagesize, $total);

        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars($total > $pagesize);
        }
    }

    /**
     * This function is called for each data row to allow processing of the
     * thumbnail value.
     *
     * @param object $values Contains object with all the values of record.
     * @return string Return thumbnail template rendered.
     */
    public function col_thumbnail($values) {
        global $OUTPUT;

        $thumbnailurl = helper::get_thumbnail_url($values->thumbnail);
        if ($this->is_downloading()) {
            return $thumbnailurl;
        }

        $args = [
            'id' => $values->id,
            'url' => $thumbnailurl,
        ];
        return $OUTPUT->render_from_template('mod_marvel/thumbnail', $args);
    }

    /**
     * This function is called for each data row to allow processing of the
     * comics value.
     *
     * @param object $values Contains object with all the values of record.
     * @return string Return view more icon template rendered.
     */
    public function col_comics($values) {
        global $OUTPUT;

        if ($this->is_downloading()) {
            return $values->viewcomicurl;
        }

        $args = [
            'url' => $values->viewcomicurl,
        ];
        return $OUTPUT->render_from_template('mod_marvel/viewmore', $args);
    }
}
