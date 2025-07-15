<?php

namespace App\Yena;

use App\Models\BookingAppointment;
use App\Models\BookingOrder;
use App\Models\BookingService;
use App\Models\User;
use App\Yena\BookingTime;

class YenaBook{

    public $user;

    public $charge;
    public $trx;
    public $booking;

    public $time;
    public $date;
    public $services = [];
    public $price;
    public $customer;
    public $duration;

    public $charge_customer = false;

    public function __construct($user_id) {
        $this->user = User::where('id', $user_id)->first();
    }

    public function setTime($time){
        $time = explode('-', $time);
        // dd($time);
        $this->duration = 0;

        if(!empty($this->services)){
            
            $end_time = 0;
            foreach($this->services as $key => $value){
                if($service = BookingService::where('id', $value)->where('user_id', $this->user->id)->first()){
                    $this->price += $service->price;
                    $end_time += $service->duration;
                }
            }

            $this->duration = $end_time;

            $time[1] = $time[0] + $end_time;
        }

        $time = implode('-', $time);
        $this->time = $time;


        return $this;
    }

    public function setDate($date){
        $this->date = $date;
        return $this;
    }

    public function setServices($ids){
        $this->services = $ids;
        
        return $this;
    }

    public function setPrice($price){
        $this->price = $price;
        return $this;
    }

    public function setCustomer($id){
        if(!$this->customer = User::where('id', $id)->first()){
            return $this;
        }

        return $this;
    }

    public function setTrx($extra = []){
        $method = 'Manual';

        $order = new BookingOrder();
        $order->user_id = $this->user->id;
        $order->customer_id = $this->customer->id;
        $order->appointment_id = null;
        $order->extra = $extra;
        $order->method = $method;
        $order->currency = ao($this->user->payments, 'currency');
        $order->ref = str()->random(7);
        $order->price = $this->price;
        $order->status = 0;
        $order->save();

        $this->trx = $order;

        return $this;
    }

    public function save($settings = [], $function = false, $is_paid = 0){
        $time_class = new BookingTime($this->user->id);
        $status = $is_paid;

        if($time_class->check_time($this->time, $this->date)){
            return $this->return_array(false, __('This time has already been booked.'));
        }

        $appointment = new BookingAppointment();
        $appointment->user_id = $this->user->id;
        $appointment->payee_user_id = $this->customer->id;
        $appointment->service_ids = $this->services;
        $appointment->date = $this->date;
        $appointment->price = $this->price;
        $appointment->is_paid = $status;
        $appointment->settings = $settings;
        $appointment->time = $this->time;
        $appointment->save();

        $this->booking = $appointment;

        // Send Notifcations
        $this->notify();
        
        if($this->trx){
            
            $this->trx->appointment_id = $this->booking->id;
            $this->trx->update();
        }

        if ($function) {
            return $function($this->booking);
        }

        return $this->return_array(true, $this->booking);
    }

    public function notify(){
        return;
        $email = new Email;
        // Get email template
        $template = $email->template('booking/appointment_created', ['booking' => $this->booking, 'payee' => $this->customer, 'user' => $this->user, 'duration' => $this->duration]);
        // Email array
        $mail = [
            'to' => $this->customer->email,
            'subject' => __('Appointment Created'),
            'body' => $template
        ];
        
        // Send Email
        $email->send($mail);
        
        /*$notify = new Notifications($this->salon->id);

        $notify->setKey('customer.appointment_created')->setAppointment($this->booking->id, true)->setReceiver('customer')->send();

        // Send To Barber
        $notify->setKey('barber.appointment_created')->setAppointment($this->booking->id, true)->setReceiver('barber')->send();*/
    }



    public function return_array($status, $response) {
        return ['status' => $status, 'response' => $response];
    }
}