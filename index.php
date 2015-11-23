<?php

class Calendar
{

    protected $year;
    protected $month;
    protected $startDay;
    protected $rowsToProcess;
    protected $display = '';
    protected $firstDayOfTheMonth;
    protected $standart = array(1 => "Mon", 2 => "Tue", 3 => "Wed", 4 => "Thu", 5 => "Fri", 6 => "Sat", 7 => "Sun");
    protected $calendarLines = array();


    public function __construct($year, $month, $startDay)
    {
        $this->prepareInput($year, $month, $startDay);
        $this->standart = $this->reasembleDays($this->standart);
        $this->calculateCalendar();
    }

    protected function prepareInput($year, $month, $startDay)
    {

        $this->year = $year;
        $this->month = $month;
        if ($startDay <= 7 && $startDay >= 1)
            $this->startDay = $startDay;
        else
            $this->startDay = 1;
    }

    protected function reasembleDays($arrayToReposition)
    {

        $rearanged = array();
        $j = 1;
        for ($i = 0; $i < count($arrayToReposition); $i++) {
            if ($i + $this->startDay <= count($this->standart))
                $rearanged[$i + 1] = $arrayToReposition[$i + $this->startDay];
            else {
                $rearanged[$i + 1] = $arrayToReposition[$j];
                $j++;
            }
        }

        return $rearanged;
    }


    protected function calculateRowsForTablle()
    {

        $daysOfTheMonth = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);

        $this->firstDayOfTheMonth = date('w', strtotime("$this->year-$this->month-01"));
        if ($this->firstDayOfTheMonth == 0)
            $this->firstDayOfTheMonth = 7;
        if ($this->startDay > 7)
            $this->startDay = 7;
        $this->rowsToProcess = $daysOfTheMonth + $this->firstDayOfTheMonth - 1;
    }

    protected function calculateCalendar()
    {
        $this->calculateRowsForTablle();
        $this->calendar();
    }

    protected function prepareDisplay()
    {

        $this->display = '<table border = "1">';
        $this->display .= "<tr>";
        foreach ($this->standart as $day) {
            $this->display .= '<td>' . $day . '</td>';
        }
        $this->display .= "</tr>";
    }

    protected function calendar()
    {

        $this->prepareDisplay();
        $dayofweekConter = 1;
        $lineNumber = 0;
        for ($i = 1; $i < $this->rowsToProcess + 1; $i++) {

            $dayofweekConter = $this->modifyDisplayOnWeekLogic($dayofweekConter);
            if ($dayofweekConter == 1)
                $lineNumber++;
            $this->modifyDisplayBasedOnParametersLogic($i, $lineNumber);

            $dayofweekConter++;
        }
        $this->prepareLines();
        $this->diplayLines();
        $this->display .= '</table>';
    }


    protected function diplayLines()
    {
        foreach ($this->calendarLines as $line) {
            $this->display .= "<tr>";
            foreach ($line as $day)
                $this->display .= "<td>$day</td>";
            $this->display .= "</tr>";
        }
    }

    protected function fillLines($line)
    {
        $i = 0;
        while ($i < 7) {
            if (!isset($line[$i]))
                $line[$i] = "";
            $i++;
        }
        return $line;
    }

    protected function prepareLines()
    {

        foreach ($this->calendarLines as &$line) {
            $line = $this->fillLines($line);
            $line = array_combine(range(1, count($line)), array_values($line));
            $line = $this->reasembleDays($line);
        }
        $this->fillGaps();
    }

    protected function fillGaps()
    {
        foreach ($this->calendarLines as $lineNumber => &$line) {
            foreach ($line as $key => &$day) {
                if ($day != "" && isset($line[$key + 1]) && $line[$key + 1] == "") {
                    for ($i = $key + 1; $i <= count($line); $i++) {
                        if (isset($this->calendarLines[$lineNumber][$i]) && isset($this->calendarLines[$lineNumber + 1][$i])) {
                            $this->calendarLines[$lineNumber][$i] = $this->calendarLines[$lineNumber + 1][$i];
                            $this->calendarLines[$lineNumber + 1][$i] = "";
                        }
                    }
                }
            }
            if (array_sum($line) < 1)
                unset($this->calendarLines[$lineNumber]);
        }
    }

    protected function modifyDisplayOnWeekLogic($dayofweekConter)
    {

        if ($dayofweekConter == 8) {
            $dayofweekConter = 1;

        }

        return $dayofweekConter;
    }

    protected function modifyDisplayBasedOnParametersLogic($i, $lineNumber)
    {

        if ($i >= $this->firstDayOfTheMonth && $i >= $this->startDay) {
            $day = $i - $this->firstDayOfTheMonth + 1;
            $this->calendarLines[$lineNumber][] = $day;
        } else {
            $this->calendarLines[$lineNumber][] = "";
        }
    }

    public function outputCalendar()
    {
        echo $this->display;
    }

}

$calc = new Calendar(2015, 11, 1);
$calc->outputCalendar();
/*
$calc = new Calendar(2015, 10, 1);
$calc->outputCalendar();

$calc = new Calendar(2015, 9, 4);
$calc->outputCalendar();

$calc = new Calendar(2015, 8, 1);
$calc->outputCalendar();

*/
