<?php

namespace App\Models;

use App\Models\Base\BookingAppointment as BaseBookingAppointment;
use App\Yena\BookingTime;

class BookingAppointment extends BaseBookingAppointment
{
	protected $appends = ['start_time', 'end_time', 'status_text', 'services', 'services_name', 'nice_start_time', 'nice_end_time'];

	protected $fillable = [
		'user',
		'payee_user_id',
		'service_ids',
		'date',
		'time',
		'settings',
		'info',
		'appointment_status',
		'price',
		'is_paid'
	];
	protected $casts = [
		'service_ids' => 'array',
		'settings' => 'array'
	];

	public function payee(){
		return $this->belongsTo(User::class, 'payee_user_id', 'id');
	}
	public function getStatusTextAttribute(){
		switch ($this->appointment_status) {
			case 0:
				return __('Pending');
			break;
			case 1:
				return __('Completed');
			break;
			case 2:
				return __('Canceled');
			break;
		}


		return 'error';
	}
	
	public function getServicesAttribute()
	{
		$services = [];
		$item = $this;

		if(!empty($item->service_ids) && is_array($item->service_ids)){
			foreach ($item->service_ids as $key => $value) {
				/*if($service = Service::find($value)){
					$services[$key] = $service;

					if ($after_hour = AfterHourService::where('barber_id', $item->barber_id)->where('service_id', $service->id)->first()) {
						$services[$key]['price'] = $after_hour->price;
					}
				}*/
			}
		}


        return $services;
	}
	
	public function getServicesNameAttribute()
	{
		$services = [];
		$item = $this;

		if(!empty($item->service_ids) && is_array($item->service_ids)){
			$items = BookingService::whereIn('id', $item->service_ids)->get();

			foreach ($items as $i) {
				$services[] = $i->name;
			}
		}

		$services = implode(', ', $services);


        return $services;
	}

	public function getStartTimeAttribute()
	{
        try{
            $times = explode('-', $this->time);

            return (int) $times[0];
        }catch(\Exception $e){

        }

        return false;
	}
	
	public function getEndTimeAttribute()
	{
        try{
            $times = explode('-', $this->time);

            return (int) $times[1];
        }catch(\Exception $e){

        }

        return false;
	}

	
	public function getNiceStartTimeAttribute()
	{
        try{
            $times = explode('-', $this->time);
			$time = (int) $times[0];
			$time = (new BookingTime($this->user_id))->format_minutes($time);
            return $time;
        }catch(\Exception $e){

        }

        return false;
	}
	
	public function getNiceEndTimeAttribute()
	{
        try{
            $times = explode('-', $this->time);
			$time = (int) $times[1];
			$time = (new BookingTime($this->user_id))->format_minutes($time);
            return $time;
        }catch(\Exception $e){

        }

        return false;
	}
	
	public function getServices()
	{
		$item = $this;
		$services = BookingService::whereIn('id', $item->service_ids)->get();


        return $services;
	}
}
