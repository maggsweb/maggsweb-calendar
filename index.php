<?php

date_default_timezone_set('Europe/London');

include 'dumpr.php';
include 'MyCalendar.php';

$calendar = new MyCalendar;
$cal = $calendar->asObject();

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Portals</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
        <style type='text/css'>
            .container-fluid {
                margin: 30px;
            }
            #calendar th {
                background-color: #F2F2F2;
            }
            #calendar td.inactive {
                background-color: #eee;
            }
            #calendar td.day {
                height: 70px;
            }
            #calendar td.today {
                background-color: lightskyblue;
            }
            #calendar td div.daynumber {
                font-size: 0.9em;
            }
            #calendar .center {
                text-align: center;
            }
            #calendar .left {
                text-align: left;
            }
            #calendar .right {
                text-align: right;
            }
            #calendar .heading {
                font-size: 2em;
            }
        </style>
    </head>
    <body>
        <div class='container-fluid'>

            <table class="table table-bordered" id="calendar">
                <tr>
                    <th class="left"><a href="<?=$cal->month->previous->link?>">&laquo;&nbsp;<?=$cal->month->previous->label?></a></th>
                    <th class="center heading" colspan="5"><?=$cal->month->current->label?></th>
                    <th class="right"><a href="<?=$cal->month->next->link?>"><?=$cal->month->next->label?>&nbsp;&raquo;</a></th>
                </tr>
                <tr>
                    <?php foreach ($cal->week->labels as $weekLabel){ ?>
                    <th class="center"><?=$weekLabel?></th>
                    <?php } ?>
                </tr>
                <?php foreach ($cal->weeks as $week){ ?>
                <tr>
                    <?php foreach ($week->days as $day){ ?>
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