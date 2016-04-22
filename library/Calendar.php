<?
class Calendar 
{
    
    private function __construct() 
    {
        
    }
    public static function getCalendar($month = false,$year = false) {
        $month = $month ? $month : date('n');
        $year = $year ? $year : date('Y');
        
      
        if( FIRST_DAY == 'Monday') {
            $firstday_num = date('N' ,mktime(0, 0, 0, $month, 1, $year)) - 1;    
        } else {
            $firstday_num = date('w' ,mktime(0, 0, 0, $month, 1, $year));
        }
        
        $lastday = date('d' ,mktime(0, 0, 0, $month+1, 0, $year));
        
        $month_days = array();
        
        for($day = 1,$week = 1,$day_num = 1; ; $day_num++) {
            if(($day_num > $firstday_num or $week > 1) && $day <= $lastday) {
                $month_days[$week][$day_num] = $day; $day++;   
            } else {
                $month_days[$week][$day_num] = '';
            }
            if($day > $lastday AND $day_num == 7) {
                break;
            }
            if($day_num == 7) {
                $week++; $day_num=0;   
            }
        }
        $calendar = array();
        $calendar['month_days'] = $month_days;
        $calendar['month_name'] = date('F',mktime(0, 0, 0, $month, 1, $year));
        $calendar['month'] = date('n',mktime(0, 0, 0, $month, 1, $year));
        $calendar['next_month'] = date('n',mktime(0, 0, 0, $month+1, 1, $year));
        $calendar['prev_month'] = date('n',mktime(0, 0, 0, $month-1, 1, $year));
        $calendar['year'] = $year ;
        return $calendar;
    }
    public static function getMonths() 
    {
        $months = array(
            1 => "January",
            2 => "February",
            3 => "March",
            4 => "April",
            5 => "May",
            6 => "June",
            7 => "July",
            8 => "August",
            9 => "September",
            10 => "October",
            11 => "November",
            12 => "December", 
        );
        return $months;
    }
    public static function getDaysWeek() 
    {
        $days1 = array(
            1 => "Sunday",
            2 => "Monday",
            3 => "Tuesday",
            4 => "Wednesday",
            5 => "Thursday",
            6 => "Friday",
            7 => "Saturday",
        );
        $days2 = array(
            1 => "Monday",
            2 => "Tuesday",
            3 => "Wednesday",
            4 => "Thursday",
            5 => "Friday",
            6 => "Saturday",
            7 => "Sunday",
        );
        if(FIRST_DAY == 'Monday') {
            return $days2;
        } else {
            return $days1;
        }
        
    }
    public static function AmPmToTstamp($hours, $minutes, $type) 
    {
        $minutes = sprintf("%02d", $minutes);
        $hours = sprintf("%02d", $hours);
        $time = strtotime ("$hours:$minutes $type"); 
        return $time;
    }
    
}