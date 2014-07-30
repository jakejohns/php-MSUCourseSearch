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
* @package   Service
* @author    Jake Johns <jake@jakejohns.net>
* @copyright 2014 Jake Johns
* @license   http://www.gnu.org/licenses/gpl-3.0.txt GPL V3
* @link      http://jakejohns.net
 */

namespace MSUCourseSearch;

/**
 * ServiceInterface
 *
 * Public interface for MSU Course Search
 *
 * @category MSUCourseSearch
 * @package  Service
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL V3
 * @version  Release: @package_version@
 * @link     http://jakejohns.net
 *
 */
interface ServiceInterface
{

    /**
    * getCurrentSemesters
    *
    * calculate current semesters
    *
    * @return mixed
    * @throws exceptionclass [description]
    *
    * @access public
    */
    public function getCurrentSemesters();


    /**
    * fetch
    *
    * fetches results
    *
    * @param Query|Array $query query object or array of params
    *
    * @return Result
    * @access public
    */
    public function fetch($query);


}

