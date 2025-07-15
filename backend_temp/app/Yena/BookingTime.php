<?php

namespace App\Yena;

use App\Models\BookingAppointment;
use App\Models\User;
use App\Models\BookingWorkingBreak;

class BookingTime{
    public $interval = 60;
    public $time = [];
    public $user;

    public function __construct($user_id = null) {
        $this->user = User::where('id', $user_id)->first();

        $this->time = $this->array_time();
    }




    public function minutes_to_hours($time) {
      $hour_type = $this->user->booking_hour_type;
      $time = (int) $time;
      if($time){
        $hours = floor($time / 60);
        if($hour_type == '12' && $hours > 12) $hours = $hours - 12;
        return $hours;
      }else{
        return 0;
      }
    }

  public function am_or_pm($minutes) {
    $hour_type = $this->user->booking_hour_type;
    if($hour_type == '24') return '';
    return ($minutes < 720) ? 'AM' : 'PM';
  }

  public function minutes_to_hours_and_minutes($minutes, $format = '%02d:%02d', $add_ampm = true) {
    if(!$format) $format = '%02d:%02d';
    $minutes = (int) $minutes;

    if ($minutes === '') {
        return;
    }
    $ampm = ($add_ampm) ? $this->am_or_pm($minutes) : '';
    $hours = $this->minutes_to_hours($minutes);

    $minutes = ($minutes % 60);

    return sprintf($format, $hours, $minutes).$ampm;
  }

  public function format_minutes($minutes, $format = '%02d:%02d', $add_ampm = true){
    $minutes = (int) $minutes;

    try {
        $hr = $this->user->booking_hour_type == 12 ? 'h:i A' : 'H:i A';
        return date($hr, mktime(0,$minutes));
        
    } catch (\Exception $th) {
        return $minutes;
    }

    if(!$format) $format = '%02d:%02d';
    $minutes = (int) $minutes;

    if ($minutes === '') {
        return;
    }
    $ampm = ($add_ampm) ? $this->am_or_pm($minutes) : '';
    $hours = $this->minutes_to_hours($minutes);

    $minutes = ($minutes % 60);

    return sprintf($format, $hours, $minutes).$ampm;
  }

  
  public function work_time_to_min($date, $user_id){
        $day = date('l', strtotime($date));
        $day_id = $this->get_day_id($day);
        $slot = $this->get_timeslot_by_day($day_id, $user_id);

        return [ao($slot, 'from'), ao($slot, 'to')];
  }

    public function array_time(){
        $time = [];
        $open_time = strtotime("00:00");
        $close_time = strtotime("23:59");
        for( $i = $open_time; $i<$close_time; $i += ($this->interval * 60)) {
            $time[date("H:i",$i)] = ['12hr' => date("h:i A",$i), '24hr' => date("H:i A",$i), 'raw' => $i];
        }

        return $time;
    }

    public function formatted_time($time){
        $time = strtotime($time);
        if (!$salon = $this->user) {
            return date("h:i A", $time);
        }

        if ($this->user->booking_hour_type == '12') {
            return date("h:i A", $time);
        }


        return date("H:i A", $time);
    }

    public function from_to($from = '00:00', $to = '00:00', $key = null){
        // 12 hr
        $from = $this->format_minutes($from);
        $to = $this->format_minutes($to);
        $time = ['from' => $from, 'to' => $to];

        return ao($time, $key);
    }

    public function get_time_all($date, $salon, $location = false){
        if (!$salon = SalonModel::find($salon)) {
            return false;
        }

        // Date
        $date = \Carbon\Carbon::parse($date);
        $max_time = $this->max_barber_time_all($date, $salon, $location);
        if(!$max_time){
            return [];
        }
        $value = [];
        $interval = ao($salon->settings, 'time_interval');

        $from = hour2min(\Carbon\Carbon::parse('08:00')->format('H:i'));
        $to = $max_time;

        $value['date'] = $date;
        $value['times'] = $this->get_time_slots($interval, $from, $to, $salon->id);

        return $value;
    }


    public function max_time_all($date){
        // End time
        $date = \Carbon\Carbon::parse($date);
        $end_time = [];

        $day = $this->get_day_id(date('l', strtotime($date->format('Y-m-d'))));
        if (ao($this->user->booking_workhours, "$day.enable")) {
            $end_time[] = (int) ao($this->user->booking_workhours, "$day.to");
        }

        $max_time = 0;
        if(!empty($end_time)){
            $max_time = max($end_time);
        }

        return $max_time;
    }
    

    public function min_time_all($date){
        // End time
        $date = \Carbon\Carbon::parse($date);
        $times = [];
        $day = $this->get_day_id(date('l', strtotime($date->format('Y-m-d'))));
            
        if (ao($this->user->booking_workhours, "$day.enable")) {
            $times[] = (int) ao($this->user->booking_workhours, "$day.from");
        }

        $time = 0;
        if(!empty($times)){
            $time = min($times);
        }

        return $time;
    }

    public function get_time($date, $interval = null){
        $day = date('l', strtotime($date));
        $day_id = $this->get_day_id($day);

        $value = [];
        $interval = (int) $interval ? $interval : ($this->user->booking_time_interval ?: 15);

        $slot = $this->get_timeslot_by_day($day_id, $this->user->id);
        $value['times'] = $this->get_time_slots($interval, ao($slot, 'from'), ao($slot, 'to'));

        //return ao($slot, 'from') .' r'. ao($slot, 'to');

        $value['day_id'] = $day_id;
        $value['date'] = $date;

        return $value;
    }

    public function get_custom_time($date, $salon, $barber, $from, $to){
        if (!$salon = SalonModel::find($salon)) {
            return false;
        }

        $day = date('l', strtotime($date));
        $day_id = $this->get_day_id($day);

        $value = [];
        $interval = ao($salon->settings, 'time_interval');

        $value['times'] = $this->get_time_slots($interval, $from, $to, $salon->id);

        $value['day_id'] = $day_id;
        $value['date'] = $date;

        return $value;
    }


    public function get_day_id($day)
    {
        if ($day == 'Monday') {
            return 1;
        } else if($day == 'Tuesday') {
            return 2;
        }else if($day == 'Wednesday') {
            return 3;
        }else if($day == 'Thursday') {
            return 4;
        }else if($day == 'Friday') {
            return 5;
        }else if($day == 'Saturday') {
            return 6;
        }else if($day == 'Sunday') {
            return 7;
        }
    }


    public function get_id_day($day, $short = false)
    {
        if ($day == 1) {
            $ret = 'Monday';
        } else if($day == 2) {
            $ret = 'Tuesday';
        }else if($day == 3) {
            $ret = 'Wednesday';
        }else if($day == 4) {
            $ret = 'Thursday';
        }else if($day == 5) {
            $ret = 'Friday';
        }else if($day == 6) {
            $ret = 'Saturday';
        }else if($day == 7) {
            $ret = 'Sunday';
        }

        if ($short) {
            $ret = str()->limit($ret, 3, '');
        }



        return $ret;
    }

    public function get_days_array(){
        return [1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => "Thu", 5 => 'Fri', 6 =>'Sat', 7 => 'Sun'];
    }

    public function get(){

        $time = [];
        foreach($this->time as $key => $value){
            $time[$key] = ao($value, '12hr');
        }

        return $time;
    }

    public function get_timeslot_by_day($day, $user_id){


        //return ["enable" => 1,"from" => 0, "to" => 720];
        if(!$user = \App\Models\User::where('id', $user_id)->first()){
            return [];
        }

        if (!ao($user->booking_workhours, "$day.enable")) {
            return [];
        }

        try {
            return $user->booking_workhours[$day];
        } catch (\Exception $th) {
            return [];
        }
    }

    public function check_workday($day, $user_id){
        //$day = \Carbon\Carbon::parse($date);

        $day = $this->get_day_id($day);
        if(!$user = \App\Models\User::find($user_id)){
            return false;
        }

        if (!ao($user->booking_workhours, "$day.enable")) {
            return false;
        }

        try {
            return $user->booking_workhours[$day];
        } catch (\Exception $th) {
            return false;
        }
    }

    public function get_time_slots($interval='', $start_time='', $end_time=''){
        $time = [];

        $_this = $this;

        if($start_time == 0 & $end_time == 0){
            //$end_time = 15;
        }


        for($minutes = $start_time; $minutes <= $end_time; $minutes += $interval){
            $start = $minutes;
            $end = $minutes + $interval;

            $start_formatted = $_this->minutes_to_hours_and_minutes($start);
            $end_formatted = $_this->minutes_to_hours_and_minutes($start);


            $time[$minutes]['start_time'] = $start_formatted;
            $time[$minutes]['end_time'] = $end_formatted;

            
            $time[$minutes]['time_value'] = "$start-$end";
            $time[$minutes]['time_view'] = str_replace(' ', '', "$start_formatted-$end_formatted");
        }

        return $time;
    }
    
    public function check_break_time($time_val, $date, $user_id){
        $status = false;
        if(!$user = \App\Models\User::where('id', $user_id)->first()){
            return false;
        }

        $breaks = BookingWorkingBreak::where('user_id', $user_id)->where('date', $date)->get();
        $time_val = explode('-', $time_val);

        $array = [];

        foreach ($breaks as $break) {
            $break_time = explode('-', $break->time);
            $break_start_time = $break_time[0];
            $break_end_time = $break_time[1];


            $start_time = $time_val[0];
            $end_time = $time_val[1];

            // comparing datetime objects
            if (
                ($break_start_time <= $start_time and $break_end_time > $start_time) // if intersecting through time slot start
                or
                ($break_start_time < $end_time and $break_end_time >= $end_time) // if intersecting through time slot end
                or
                ($break_start_time <= $start_time and $break_end_time >= $end_time) // if compare covers us
                or
                ($break_start_time >= $start_time and $break_end_time <= $end_time) // if we contain compare
            ) {
                $array[] = $break->id;
            }
        }

        if(!empty($array)){
            return $array;
        }

        return false;


        return $status;
    }

    public function check_time($time_val, $date){
        $status = false;

        $appointments = BookingAppointment::where('user_id', $this->user->id)->where('date', $date)->get();
        $breaks = BookingWorkingBreak::where('user_id', $this->user->id)->where('date', $date)->get();
        $appointments = $appointments->concat($breaks);

        $appointments->map(function($item){
            $item->start_time_strtotime = strtotime($item->start_time);
            $item->end_time_strtotime = strtotime($item->end_time);
        });

        $time_val = explode('-', $time_val);




        //$appointments = $appointments->where('start_time_strtotime', '>=', strtotime($time_val[0]))->where('end_time_strtotime', '<=', strtotime($time_val[1]));
        //return $appointments;

        foreach ($appointments as $appointment) {
            $appointment_time = explode('-', $appointment->time);
            $appointment_start_time = $appointment_time[0];
            $appointment_end_time = $appointment_time[1];


            $start_time = $time_val[0];
            $end_time = $time_val[1];

            // comparing datetime objects
            if (
                ($appointment_start_time <= $start_time and $appointment_end_time > $start_time) // if intersecting through time slot start
                or
                ($appointment_start_time < $end_time and $appointment_end_time >= $end_time) // if intersecting through time slot end
                or
                ($appointment_start_time <= $start_time and $appointment_end_time >= $end_time) // if compare covers us
                or
                ($appointment_start_time >= $start_time and $appointment_end_time <= $end_time) // if we contain compare
            ) {
                $status = true;
            }
        }


        return $status;
    }
    
    public function check_time_all($time_val, $date, $barbers = []){
        $status = true;

        $array = [];

        foreach ($barbers as $key => $id) {
            $array[$id] = $this->check_time($time_val, $date, $id);
        }

        foreach($array as $key => $value){
            if($value !== true){
                $status = false;
            }
        }

        //return (min($array) === max($array));

        return $status;
    }

    public function booked_time($appointment, $time_val){
        $status = false;
        
        $appointment_time = explode('-', $appointment->time);
        $appointment_start_time = hour2min($appointment_time[0]);
        $appointment_end_time = hour2min($appointment_time[1]);


        $start_time = hour2min($time_val[0]);
        $end_time = hour2min($time_val[1]);

        // comparing datetime objects
        if (
            ($appointment_start_time <= $start_time and $appointment_end_time > $start_time) // if intersecting through time slot start
            or
            ($appointment_start_time < $end_time and $appointment_end_time >= $end_time) // if intersecting through time slot end
            or
            ($appointment_start_time <= $start_time and $appointment_end_time >= $end_time) // if compare covers us
            or
            ($appointment_start_time >= $start_time and $appointment_end_time <= $end_time) // if we contain compare
        ) {
            $status = true;
        }


        return $status;
    }
}
