<?php

date_default_timezone_set('Europe/London');

// Debug tool
if (file_exists('../dumpr.php')) {
    include '../dumpr.php';
}

// Calendar Class
include '../classes/MyCalendar.php';

$calendar = new MyCalendar();
$cal = $calendar->asObject();

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Calendar</title>
        <link rel="stylesheet" href="css/bootstrap.3.2.0.min.css">
        <style type='text/css'>
            :root {
                --header-background-color: #ddd;
                --inactive-background-color: #F2F2F2;
                --today-background-color: lightskyblue;
            }
        </style>
        <link rel="stylesheet" href="css/calendar.css">
    </head>
    <body>
        <div class='container-fluid'>

            <p><a href="<?=$calendar->rootPath?>">Today</a></p>

            <table class="table table-bordered" id="calendar">
                <tr>
                    <th class="left"><a href="<?=$cal->month->previous->link?>">&laquo;&nbsp;<?=$cal->month->previous->label?></a></th>
                    <th class="center heading" colspan="5"><?=$cal->month->current->label?></th>
                    <th class="right"><a href="<?=$cal->month->next->link?>"><?=$cal->month->next->label?>&nbsp;&raquo;</a></th>
                </tr>
                <tr>
                    <?php foreach ($cal->week->labels->full as $weekLabel) { ?>
                    <th class="center"><?=$weekLabel?></th>
                    <?php } ?>
                </tr>
                <?php foreach ($cal->weeks as $week) { ?>
                <tr>
                    <?php foreach ($week->days as $day) { ?>
                    <td class="day <?=$day->dayclass?> <?=$day->todayclass?>">
                        <div class="daynumber right"><?=$day->day->number?><sup><?=$day->day->ordinal?></sup></div>
                    </td>
                    <?php } ?>
                </tr>
                <?php } ?>
            </table>

        </div>
    </body>
</html>
<?php
dump($cal);
