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
use Httpful\Request;
use Httpful\Http;
use Httpful\Mime;


/**
 * Service
 *
 * Service object for MSU Course SEarch API
 *
 * @category MSUCourseSearch
 * @package  Service
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL V3
 * @version  Release: @package_version@
 * @link     http://jakejohns.net
 *
 * @see      ServiceInterface
 */
class Service implements ServiceInterface
{

    const SEMESTER_URI = 'https://schedule.msu.edu/';
    const QUERY_URI = 'https://schedule.msu.edu/searchResults.asp';

    protected $semesterCodes = null;

    protected $parser;

    /**
    * __construct
    *
    * inits request template
    *
    * @access public
    */
    public function __construct()
    {
        $template = Request::init()
            ->method(Http::POST)
            ->expectsHtml()
            ->addHeader('X-Powered-By', 'php-MSUCourseSearch')
            ->addHeader('User-Agent', 'php-MSUCourseSearch')
            ->sendsType(Mime::FORM);
        Request::ini($template);
        $this->parser = new Parser();
    }

    /**
    * getCurrentSemesters
    *
    * calculate current semesters
    *
    * @return array
    *
    * @access public
    */
    public function getCurrentSemesters()
    {
        $year = date('Y');
        $month = date('n');

        if ($month > 1 && $month < 5) {
            $seasons = array(
                'Summer ' . $year,
                'Fall ' . $year
            );

        } elseif ($month > 4 && $month < 10) {
            $seasons = array(
                'Fall ' . $year,
                'Spring ' . ($year + 1)
            );

        } else {
            $seasons = array(
                'Spring ' . ($month == 1? $year:($year+1)),
                'Summer ' . ($month == 1? $year:($year+1))
            );
        }
        return $seasons;
    }

    /**
    * getSemesterCode
    *
    * translate a semester to code
    *
    * @param mixed $semester DESCRIPTION
    *
    * @return mixed
    * @throws exceptionclass [description]
    *
    * @access public
    */
    public function getSemesterCode($semester)
    {
        if (is_array($semester)) {
            $semester = implode(' ', $semester);
        }

        preg_match('/\d+/', $semester, $year);
        preg_match('/[a-zA-Z]+/', $semester, $season);

        if (!count($year)) {
            throw new Exception('Could not parse a year!');
        }

        if (!count($season)) {
            throw new Exception('Could not parse a season!');
        }

        $year = $year[0];
        $season = strtolower($season[0]);

        if (strlen($year) == 2) {
            $year = '20' . $year;
        }

        $codes = $this->getSemesterCodes();

        if (!in_array($year, array_keys($codes))) {
            throw new Exception(
                sprintf('Invalid year "%s". No option in form!', $year)
            );
        }

        $season = $this->parseSeason($season);

        if (!isset($codes[$year][$season])) {
            return null;
        }

        $code = $codes[$year][$season];
        return $code;
    }

    /**
    * parseSeason
    *
    * @param mixed $season DESCRIPTION
    *
    * @return mixed
    * @throws exceptionclass [description]
    *
    * @access protected
    */
    protected function parseSeason($season)
    {
        if (in_array($season, array('fall', 'summer', 'spring'))) {
            return $season;
        }

        $abbrs = array(
            'fall' => array('f', 'fa', 'fal'),
            'summer' => array('su', 'sum', 'us'),
            'spring' => array('sp', 'spr', 'ss')
        );

        foreach ($abbrs as $name => $abbr) {
            if (in_array($season, $abbr)) {
                return $name;
            }
        }

        throw new Exception('Invalid season "'.$season.'"');
    }

    /**
    * getSemesterCodes
    *
    * @return mixed
    * @throws exceptionclass [description]
    *
    * @access protected
    */
    protected function getSemesterCodes()
    {
        if (null === $this->semesterCodes) {
            $response = Request::get(self::SEMESTER_URI)
                ->parseWith($this->parser->getSemesterParser())
                ->send();
            $this->semesterCodes = $response->body;
        }
        return $this->semesterCodes;
    }


    /**
    * fetch
    *
    * Fetch courses
    *
    * @param mixed $query DESCRIPTION
    *
    * @return mixed
    * @throws exceptionclass [description]
    *
    * @access public
    */
    public function fetch($query)
    {
        if (!$query instanceof Query) {
            $query = new Query($query);
        }

        $response = Request::post(self::QUERY_URI, $query->getParamArray())
            ->parseWith($this->parser->getCourseParser())
            ->send();

        return $response->body;
    }

}

