<?php
/**
* MSUCourseSearch
*
* PHP version 5
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
* @category  MSUCourseSearch
* @package   Query
* @author    Jake Johns <jake@jakejohns.net>
* @copyright 2014 Jake Johns
* @license   http://www.gnu.org/licenses/gpl-3.0.txt GPL V3
* @link      http://jakejohns.net
 */

namespace MSUCourseSearch;

/**
 * Course
 *
 * @category MSUCourseSearch
 * @package  Course
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL V3
 * @version  Release: @package_version@
 * @link     http://jakejohns.net
 *
 */
class Course
{
    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';
    const STATUS_RESTRICTED = 'restricted';


    /**
     * _action
     *
     * @var mixed
     * @access public
     */
    public $action;

    /**
     * _status
     *
     * @var mixed
     * @access public
     */
    public $status;

    /**
     * _datesLink
     *
     * @var mixed
     * @access public
     */
    public $datesLink;

    /**
     * _subject
     *
     * @var string
     * @access public
     */
    public $subject;

    /**
     * courseType
     *
     * @var string
     * @access public
     */
    public $courseType;

    /**
     * uid
     *
     * @var mixed
     * @access public
     */
    public $uid;

    /**
     * uuid
     *
     * @var string
     * @access public
     */
    public $uuid;

    /**
     * _courseNumber
     *
     * @var int
     * @access public
     */
    public $courseNumber;

    /**
     * courseTitle
     *
     * @var string
     * @access public
     */
    public $courseTitle;

    /**
     * semester
     *
     * @var string
     * @access public
     */
    public $semester;

    /**
     * _courseId
     *
     * @var mixed
     * @access public
     */
    public $courseId;

    /**
     * _section
     *
     * @var int
     * @access public
     */
    public $section;

    /**
     * _credits
     *
     * @var int
     * @access public
     */
    public $credits;

    /**
     * _hoursArranged
     *
     * @var mixed
     * @access public
     */
    public $hoursArranged;

    /**
     * _days
     *
     * @var array
     * @access public
     */
    public $days = array();

    /**
     * _times
     *
     * @var string
     * @access public
     */
    public $times;

    /**
     * _building
     *
     * @var string
     * @access public
     */
    public $building;

    /**
     * _instructor
     *
     * @var string
     * @access public
     */
    public $instructor;

    /**
     * _enrolled
     *
     * @var int
     * @access public
     */
    public $enrolled;

    /**
     * _limit
     *
     * @var int
     * @access public
     */
    public $limit;

    /**
     * _roomSize
     *
     * @var int
     * @access public
     */
    public $roomSize;

    /**
     * _rawEntry
     *
     * @var string
     * @access public
     */
    public $rawEntry;

    /**
     * _dtStart
     *
     * @var Zend_Date
     * @access public
     */
    public $dtStart;

    /**
     * _dtEnd
     *
     * @var Zend_Date
     * @access public
     */
    public $dtEnd;

    /**
     * _title
     *
     * @var string
     * @access public
     */
    public $sectionTitle;

    /**
     * _description
     *
     * @var string
     * @access public
     */
    public $description;

    /**
     * _descriptionLink
     *
     * @var string
     * @access public
     */
    public $descriptionLink;

    /**
     * _notes
     *
     * @var mixed
     * @access public
     */
    public $notes;

    /**
    * __construct
    *
    * @param array $data DESCRIPTION
    *
    * @return mixed
    * @throws exceptionclass [description]
    *
    * @access public
    */
    public function __construct($data = array())
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        $this->courseType = sprintf(
            '%s%s',
            $this->subject,
            $this->courseNumber
        );

        $this->uid = sprintf(
            '%s_%s',
            $this->courseType,
            $this->section
        );

        $this->uuid = sprintf(
            '%s_%s',
            str_replace(' ', '-', $this->semester),
            $this->uid
        );
    }
}

