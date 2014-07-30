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
 * Query
 *
 * MSU Course Search Query Object
 *
 * @category MSUCourseSearch
 * @package  Query
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL V3
 * @version  Release: @package_version@
 * @link     http://jakejohns.net
 *
 */
class Query
{

    /**
     * semester
     *
     * @var string
     * @access public
     */
    public $semester;

    /**
     * subject
     *
     * @var string
     * @access public
     */
    public $subject;

    /**
     * courseNumber
     *
     * @var string
     * @access public
     */
    public $courseNumber;

    /**
     * instructor
     *
     * @var string
     * @access public
     */
    public $instructor = 'ANY';

    /**
     * startTime
     *
     * @var string
     * @access public
     */
    public $startTime = '0600';

    /**
     * endTime
     *
     * @var string
     * @access public
     */
    public $endTime = '2350';

    /**
     * onBeforeDate
     *
     * @var string
     * @access public
     */
    public $onBeforeDate;

    /**
     * onAfterDate
     *
     * @var string
     * @access public
     */
    public $onAfterDate;

    /**
     * days
     *
     * @var array
     * @access public
     */
    public $days = array(
        'sunday'    => true,
        'monday'    => true,
        'tuesday'   => true,
        'wednesday' => true,
        'thursday'  => true,
        'friday'    => true,
        'saturday'  => true
    );

    /**
     * location
     *
     * @var array
     * @access public
     */
    public $locations = array(
        'onCampus'      => true,
        'offCampus'     => true,
        'onlineCourses' => true,
        'studyAbroad'   => true,
        'msuDubai'      => true
    );


    /**
     * availability
     * availability is [A]ll, [O]pen, [H]onors
     *
     * @var string
     * @access public
     */
    public $availability = 'A';


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
    public function __construct(array $data = array())
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
    * getParamArray
    *
    * @return mixed
    * @throws exceptionclass [description]
    *
    * @access public
    */
    public function getParamArray()
    {
        $data = array(
                'Semester'       => $this->semester,
                'Subject'        => $this->subject,
                'CourseNumber'   => $this->courseNumber,
                'Instructor'     => $this->instructor,
                'StartTime'      => $this->startTime,
                'EndTime'        => $this->endTime,
                'OnBeforeDate'   => $this->onBeforeDate,
                'OnAfterDate'    => $this->onAfterDate,
            );

        $days = array(
            'sunday'    => 'Su',
            'monday'    => 'M',
            'tuesday'   => 'Tu',
            'wednesday' => 'W',
            'thursday'  => 'Th',
            'friday'    => 'F',
            'saturday'  => 'Sa'
        );

        $locations = array(
            'On Campus'      => 'onCampus',
            'Off Campus'     => 'offCampus',
            'Online Courses' => 'onlineCourses',
            'Study Abroad'   => 'studyAbroad',
            'MSU Dubai'      => 'msuDubai',
        );

        foreach ($days as $name => $code) {
            $data[ucfirst($name)] = ($this->days[$name] ? $code : '');
        }

        foreach ($locations as $name => $key) {
            $data[$name] = ($this->locations[$key] ? 'Y':'');
        }

        $data['OpenCourses'] = (
            in_array(strtolower($this->availability), array('a', 'o', 'h'))
            ? strtoupper($this->availability)
            : 'A'
        );

        return $data;
    }

    /**
    * getQueryString
    *
    * @return string
    * @throws exceptionclass [description]
    *
    * @access public
    */
    public function getQueryString()
    {
        return http_build_query($this->getParamArray());
    }

}

