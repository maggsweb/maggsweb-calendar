<?php


class MyCalendar {


    /**
     * Month (without leading zeros)
     * @var int
     */
    public $currentMonth;

    /**
     * 4-digit Year
     * @var int
     */
    public $currentYear;

    /**
     * Default page URL
     * @var string
     */
    public $rootPath = '/';

    /**
     * Private variables
     * @var mixed
     */
    private $currentWeek,
            $previousYear,
            $previousMonth,
            $nextYear,
            $nextMonth,
            $today;

    public function __construct() {

        // Set default Month, Year
        $this->currentYear  = isset($_GET['y']) && preg_match('/^[0-9]{4}$/',       $_GET['y']) ? (int)$_GET['y'] : (int)date('Y');
        $this->currentMonth = isset($_GET['m']) && preg_match('/^([1-9]|1[012])$/', $_GET['m']) ? (int)$_GET['m'] : (int)date('n');
        $this->currentWeek  = date('W', mktime(0,0,0,$this->currentMonth,1,$this->currentYear));

        $this->previousYear  = $this->currentMonth == 1 ? $this->currentYear - 1 : $this->currentYear;
        $this->previousMonth = $this->currentMonth == 1 ? 12                     : $this->currentMonth -1;

        $this->nextYear = $this->currentMonth == 12 ? $this->currentYear + 1 : $this->currentYear;
        $this->nextMonth = $this->currentMonth == 12 ? 1                      : $this->currentMonth + 1;

        $this->today = date('Y-m-d');
    }

    /**
     * @param int|string $month
     */
    public function setMonth($month)
    {
        $this->currentMonth = preg_match('/^([1-9]|1[012])$/', $month) ? (int)$month : (int)date('n');
    }

    /**
     * @param int|string $year
     */
    public function setYear($year)
    {
        $this->currentYear  = preg_match('/^[0-9]{4}$/', $year) ? (int)$year : (int)date('Y');
    }

    /**
     * @param string $path
     */
    public function setRootPath($path)
    {
        $this->rootPath = $path;
    }

    public function asArray()
    {
        return $this->_buildCalendar();
    }

    public function asObject()
    {
        return json_decode(json_encode($this->_buildCalendar()));
    }

    public function asJson()
    {
        return json_encode($this->_buildCalendar());
    }

    // -----------------------------------------------------------------------------------------------------------------

    private function _buildCalendar()
    {

        $month = [
            'previous' => [
                'label' => date('F', mktime(0,0,0,$this->currentMonth-1, 1, $this->currentYear)),
                'link' => sprintf('%s?y=%d&m=%d', $this->rootPath, $this->previousYear, $this->previousMonth)
            ],
            'current' => [
                'label' => date('F Y', mktime(0,0,0,$this->currentMonth, 1, $this->currentYear)),
                'link' => $this->rootPath
            ],
            'next' => [
                'label' => date('F', mktime(0,0,0,$this->currentMonth+1, 1, $this->currentYear)),
                'link' => sprintf('%s?y=%d&m=%d', $this->rootPath, $this->nextYear, $this->nextMonth)
            ]
        ];

        $week = [
            'labels' => [
                'full' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                'short' => ['Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun']
            ]
        ];

        // Starting Monday
        $theDate = $this->_startDate();

        // Final Sunday
        $toDate = $this->_endDate();

        $weeks = [];

        while($theDate <= $toDate){

            $w = $theDate->format('W');     // week number
            $d = $theDate->format('d');     // day, with zeros
            $j = $theDate->format('j');     // day, without zeros
            $S = $theDate->format('S');     // ordinal suffix
            $l = $theDate->format('l');     // full day
            $D = $theDate->format('D');     // short day
            $m = $theDate->format('m');     // month, with zeros
            $n = $theDate->format('n');     // month, without zeros
            $M = $theDate->format('M');     // month, short
            $F = $theDate->format('F');     // month, long
            $y = $theDate->format('y');     // year, short
            $Y = $theDate->format('Y');     // year, long
            $N = $theDate->format('N');     // day number 1-7

            $weeks[$w]['weeknumber'] = $w;
            $weeks[$w]['days'][$d] = [
                'day' => [
                    'number' => $j,
                    'fullname' => $l,
                    'shortname' => $D,
                    'string' => $d,
                    'ordinal' => $S,
                ],
                'month' => [
                    'number' => $m,
                    'string' => $n,
                    'short'  => $M,
                    'long'   => $F
                ],
                'year' => [
                    'short' => $y,
                    'long' => $Y
                ],
                'daynumber' => $N,
                'dayclass' => $n == $this->currentMonth ? '' : 'inactive',
                'todayclass' => "$Y-$m-$d" == $this->today ? 'today' : '',
                'insert' => "$Y-$m-$d",
                'link' => "#",
                'events' => ''
            ];

            $theDate->modify('+ 1 day');
        }

        return [
          'month' => $month,
          'week' => $week,
          'weeks' => $weeks
        ];
    }


    private function _hasEvents($dateObject)
    {
        return false;
    }

    /**
     * Get 1st of current month, then track backwards to a Monday
     * to find the date the calendar should start.
     * @return DateTime
     * @throws Exception
     */
    private function _startDate()
    {
        $dt = new DateTime();
        $dt->setTimestamp(mktime(0,0,0,$this->currentMonth,1,$this->currentYear));
        return $this->_findDay($dt,1, '- 1 day');
    }

    /**
     * Get current month, then last day of month, then track forwards
     * until the next Sunday, to find the date that the calendar should end.
     * @return DateTime
     * @throws Exception
     */
    private function _endDate()
    {
        $dt = new DateTime();
        $dt->setDate(
            $this->currentYear,
            $this->currentMonth,
            1
        );
        $dt2 = clone $dt;
        $dt2->setDate(
            $this->currentYear,
            $this->currentMonth,
            $dt->format('t')
        );
        return $this->_findDay($dt2,7, '+ 1 day');
    }


    /**
     * Recursive function to lookup a day number in reverse
     * @param DateTime $dateTime
     * @param $dayNum
     * @param $modify
     * @return DateTime
     */
    private function _findDay(DateTime $dateTime, $dayNum, $modify)
    {
        if($dateTime->format('N') == $dayNum){
            return $dateTime;
        }
        return $this->_findDay($dateTime->modify($modify), $dayNum, $modify);
    }



}