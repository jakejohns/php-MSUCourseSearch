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
* @package   Parser
* @author    Jake Johns <jake@jakejohns.net>
* @copyright 2014 Jake Johns
* @license   http://www.gnu.org/licenses/gpl-3.0.txt GPL V3
* @link      http://jakejohns.net
 */

namespace MSUCourseSearch;

use \Zend\Dom\Query as DomQuery;
use \Zend\Filter\Word\SeparatorToCamelCase as Filter;

/**
 * Parser
 *
 * @category MSUCourseSearch
 * @package  Parser
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL V3
 * @version  Release: @package_version@
 * @link     http://jakejohns.net
 *
 */
class Parser
{

    protected $filter;

    /**
    * __construct
    *
    * @return mixed
    * @throws exceptionclass [description]
    *
    * @access public
    */
    public function __construct()
    {
        $this->filter = new Filter();
    }

    /**
    * trim
    *
    * @param mixed $str DESCRIPTION
    *
    * @return mixed
    * @throws exceptionclass [description]
    *
    * @access protected
    */
    protected function trim($str)
    {
        $str = preg_replace('/\s+/', ' ', $str);
        $str = trim($str);
        $str = trim($str, chr(194) . chr(160) . chr(32));
        return $str;
    }

    /**
    * getSemesterParser
    *
    * @return mixed
    * @throws exceptionclass [description]
    *
    * @access public
    */
    public function getSemesterParser()
    {
        return function ($body) {
            $dom = new DomQuery($body);
            $data = array();
            foreach ($dom->execute('#Semester option') as $option) {
                $txt = explode(' ', $option->textContent);
                $code = trim($option->getAttribute('value'));
                $key = preg_replace('/\D/', '', $txt[1]);
                $data[$key][$txt[0]] = $code;
            }
            return $data;
        };
    }

    /**
    * getCourseParser
    *
    * @return mixed
    * @throws exceptionclass [description]
    *
    * @access public
    */
    public function getCourseParser()
    {
        return function ($body) {
            $courses = array();
            $dom = new DomQuery($body);
            $tableRows  = $dom->execute('table[summary="subject"] tr');
            foreach ($tableRows as $row) {
                if ($course = $this->parseRow($row) ) {
                    $courses[] = new Course($course);
                }
            }
            return $courses;
        };
    }

    /**
    * parseRow
    *
    * @param mixed $row DESCRIPTION
    *
    * @return mixed
    * @throws exceptionclass [description]
    *
    * @access protected
    */
    protected function parseRow($row)
    {
        if ('row_Section' == $row->getAttribute('class')) {
            $courseTable = $row->parentNode;
            $courseXPath = new \DOMXPath($courseTable->ownerDocument);
            $course = array();

            $course = array_merge(
                $course,
                $this->parseHeaders($row, $courseXPath),
                $this->parseMeta($courseTable, $courseXPath),
                $this->parseSibling($row->nextSibling)
            );

            return $course;
        }

    }

    /**
    * parseHeaders
    *
    * @param mixed $row         DESCRIPTION
    * @param mixed $courseXPath DESCRIPTION
    *
    * @return mixed
    * @throws exceptionclass [description]
    *
    * @access protected
    */
    protected function parseHeaders($row, $courseXPath)
    {
        $course = array();
        foreach ($row->getElementsByTagName('td') as $cell) {

            $field = $this->filter->filter(
                $this->trim($cell->getAttribute('headers'))
            );
            $field = lcfirst($field);

            $value = $this->trim($cell->textContent);

            if ($field == 'days') {
                preg_match_all('/[A-Z][^A-Z]*/', $value, $days);
                $value = $days[0];
            } elseif ($field == 'action') {
                $aLink = $courseXPath->query(".//a", $cell)->item(0);
                $value = 'http://schedule.msu.edu/' . $aLink->getAttribute('href');
                $status = explode(
                    ' ',
                    strtolower(trim($aLink->getAttribute('title')))
                );
                $course['status'] = $status[0];
            } elseIf ($field == 'section') {
                $aLink = $courseXPath->query(".//a", $cell)->item(0);
                $course['datesLink']
                    = 'http://schedule.msu.edu/SctnDates.asp?SctnID='
                    . substr($aLink->getAttribute('id'), 1);
            }
            $course[$field] = $value;
        }
        return $course;
    }


    /**
    * parseMeta
    *
    * @param mixed $courseTable DESCRIPTION
    * @param mixed $courseXPath DESCRIPTION
    *
    * @return mixed
    * @throws exceptionclass [description]
    *
    * @access protected
    */
    protected function parseMeta($courseTable, $courseXPath)
    {
        $course = array();
        $headers = $courseXPath->query(
            ".//td[@class='row_Header']",
            $courseTable
        );

        $headerLink = $courseXPath->query(
            ".//a",
            $headers->item(0)
        )->item(0);

        preg_match(
            '/([a-zA-Z]+)[^A-Z0-9]*([0-9]+)/',
            trim($headerLink->textContent),
            $courseNameParts
        );

        $course['descriptionLink'] = $headerLink->getAttribute('href');
        $course['courseId'] = $headerLink->getAttribute('id');
        $course['subject'] = $courseNameParts[1];
        $course['courseNumber'] = $courseNameParts[2];
        $course['courseTitle'] = $this->trim(
            $headers->item(1)->textContent
        );

        $courseNotes = $courseXPath->query(
            ".//td[@class='row_Desc']",
            $courseTable
        );

        $course['notes'] = (
            $courseNotes->length
            ? $this->trim($courseNotes->item(0)->textContent)
            : null
        );
        return $course;
    }

    /**
    * parseSibling
    *
    * @param mixed $nextRow DESCRIPTION
    *
    * @return mixed
    * @throws exceptionclass [description]
    *
    * @access protected
    */
    protected function parseSibling($nextRow)
    {
        $course = array();
        if ($nextRow && $nextRow->getAttribute('class') !== 'row_Section') {

            $course['rawEntry'] = $this->trim($nextRow->textContent);

            preg_match(
                '#(\d{1,2}/\d{1,2}/\d{4})\s*-\s*(\d{1,2}/\d{1,2}/\d{4})#',
                $course['rawEntry'],
                $dates
            );

            if (count($dates) == 3) {
                $course['dtStart'] = $dates[1];
                $course['dtEnd'] = $dates[2];
            }

            if (preg_match(
                '#Topic:\s*([^.-]*)[.-]\s*[A-Z][a-z]*#',
                $course['rawEntry'],
                $title
            )) {
                $course['sectionTitle'] = trim($title[1]);

                // special case for course ending in a question mark
            } elseif (preg_match(
                '#([A-Z][^a-z]+)\s*\?\s*[A-Z]\s*[a-z]#',
                $this->trim($course['rawEntry']),
                $title
            )) {
                $course['sectionTitle'] = $title[1] . '?';

            } elseif (preg_match(
                '#([A-Z][^a-z]+)\s*[[:punct:]]\s*[A-Z]\s*[a-z]#',
                $this->trim($course['rawEntry']),
                $title
            )) {
                $course['sectionTitle'] = $title[1];
            } elseif (preg_match(
                '#Sec \d{3}\s*-?\s*([^.]+)#',
                $course['rawEntry'],
                $title
            )) {
                $course['sectionTitle'] = $title[1];
            }

            if (isset($course['sectionTitle'])) {
                $descParts = explode($course['sectionTitle'], $course['rawEntry']);
                $course['description'] = trim(
                    $this->trim(
                        preg_replace('/^[^a-zA-Z]/', '', $descParts[1])
                    ), '- '
                );
            }
        }
        return $course;
    }


}


