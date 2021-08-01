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
 * Table to display the Marvel comic data.
 *
 * @package     mod_marvel
 * @copyright   2021 Guillermo Gomez <guigomar@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_marvel\table;

use mod_marvel\helper;
use table_sql;
use stdClass;

class marvel_table extends table_sql implements \renderable {
    /**
     * @var int A current page number.
     */
    protected $page;

    /**
     * @var StdClass The Marvel list to display in the table.
     */
    protected $marvellist;

    /**
     * @var string The Marvel type of list.
     */
    protected $listtype;

    /**
     * Constructor.
     */
    public function __construct($uniqueid, \moodle_url $url, $marvellist, $listtype, $download = '', $page = 0, $perpage = 100) {
        parent::__construct($uniqueid);

        $this->pagesize = $perpage;
        $this->page = $page;
        $this->marvellist = $marvellist;
        $this->listtype = $listtype;

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
            ];

        $headers =
            [
                get_string('table:thumbnail', 'mod_marvel'),
                get_string('table:id', 'mod_marvel'),
                get_string('table:name', 'mod_marvel'),
                get_string('table:description', 'mod_marvel'),
            ];

        // Get the custom columns depending on the type of list.
        if ($this->listtype === 'comics') {
            $extracolumns =
                [
                    'variantDescription',
                    'details'
                ];
            $extraheaders =
                [
                    get_string('table:variantdescription', 'mod_marvel'),
                    get_string('table:details', 'mod_marvel'),
                ];
        } else {
            $extracolumns = ['details'];
            $extraheaders = [get_string('table:details', 'mod_marvel')];
        }

        $columns = array_merge($columns, $extracolumns);
        $headers = array_merge($headers, $extraheaders);

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
        foreach ($this->marvellist->data->results as $marvelitem) {
            $item = new stdClass();
            $item->thumbnail = $marvelitem->thumbnail;
            $item->id = $marvelitem->id;
            if ($this->listtype === 'characters') {
                $item->name = $marvelitem->name;
            } else if ($this->listtype === 'creators') {
                $item->name = $marvelitem->fullName;
            } else {
                $item->name = $marvelitem->title;
            }
            $item->description = $marvelitem->description;
            $item->variantDescription = $marvelitem->variantDescription;
            $item->viewmore = [];
            if (isset($marvelitem->comics)) {
                $item->viewmore[] = ['name' => 'Comics', 'count' => $marvelitem->comics->available];
            }
            if (isset($marvelitem->characters)) {
                $item->viewmore[] = ['name' => 'Characters', 'count' => $marvelitem->characters->available];
            }
            if (isset($marvelitem->events)) {
                $item->viewmore[] = ['name' => 'Events', 'count' => $marvelitem->events->available];
            }
            if (isset($marvelitem->creators)) {
                $item->viewmore[] = ['name' => 'Creators', 'count' => $marvelitem->creators->available];
            }
            if (isset($marvelitem->stories)) {
                $item->viewmore[] = ['name' => 'Stories', 'count' => $marvelitem->stories->available];
            }
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

        if ($values->thumbnail) {
            $thumbnailurl = helper::get_thumbnail_url($values->thumbnail);
        } else {
            $thumbnailurl = 'http://i.annihil.us/u/prod/marvel/i/mg/b/40/image_not_available.jpg';
        }
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
    public function col_details($values) {
        global $OUTPUT;

        if ($this->is_downloading()) {
            return $values->viewcomicurl;
        }

        if ($values->thumbnail) {
            $thumbnailurl = helper::get_thumbnail_url($values->thumbnail);
        } else {
            $thumbnailurl = 'http://i.annihil.us/u/prod/marvel/i/mg/b/40/image_not_available.jpg';
        }

        $args = [
            'id' => $values->id,
            'url' => $thumbnailurl,
            'name' => $values->name,
            'description' => $values->description,
            'moreinfo' => $values->viewmore,
        ];
        return $OUTPUT->render_from_template('mod_marvel/viewmore_modal', $args);
    }
}
