<?php

namespace App\Models;

use DateTime;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'brand',
        'registration_plate',
        'parking_lot_id',
        'vehicle_type_id',
        'card_id',
        'entered_at',
        'exited_at',
        'price_of_exit',
        'in_parking',
    ];

    /**
     * Calculate 
     * @return array $result
     */
    public function calculateParkingPrice()
    {
        // rates
        $daily_rate = $this->vehicleType->daily_rate;
        $night_rate = $this->vehicleType->night_rate;

        // get timezone form parking lot
        $timezone = $this->parkinglot->timezone;
        // time
        $entered_at = Helper::adjustTimeZone($this->entered_at, $timezone);
        $exited_at = Helper::adjustTimeZone($this->exited_at ? $this->exited_at : 'now', $timezone);
        
        // timestamps
        $entered_at_t = $entered_at->getTimestamp();
        $exited_at_t = $exited_at->getTimestamp();
        
        $daily_hours = 0;
        $night_hours = 0;

        $day_start = clone $entered_at;
        $day_start->setTime(8, 0);
        $day_end = clone $entered_at;
        $day_end->setTime(18, 0);

        //timestamps for day
        $day_start_t = $day_start->getTimestamp();
        $day_end_t = $day_end->getTimestamp();

        if ($entered_at->format('Y-m-d') == $exited_at->format('Y-m-d')) {

            if(($entered_at < $day_start && $exited_at < $day_start) || ($entered_at > $day_end && $exited_at > $day_end)){
                $night_hours = $exited_at_t - $entered_at_t;
            } else if($entered_at >= $day_start && $exited_at >= $day_start && $entered_at <= $day_end && $exited_at <= $day_end){
                $daily_hours = $exited_at_t - $entered_at_t;
            } else if($entered_at < $day_start && $exited_at > $day_end){
                $daily_hours = $day_end_t - $day_start_t;
                $night_hours = ($day_start_t - $entered_at_t) + ($exited_at_t - $day_end_t);
            } else if($entered_at < $day_start && $exited_at < $day_end){
                $daily_hours = $exited_at_t - $day_start_t;
                $night_hours = $day_start_t - $entered_at_t;
            } else {
                $daily_hours = $day_end_t - $entered_at_t;
                $night_hours = $exited_at_t - $day_end_t;
            }

        } else {
            $night_start = clone $entered_at;
            $night_start->setTime(24, 0);
            $night_start_t = $night_start->getTimestamp();

            // calculate for the start date
            if($entered_at < $day_start){
                $daily_hours = $day_end_t - $day_start_t;
                $night_hours = ($night_start_t - $day_end_t) + ($day_start_t - $entered_at_t);
            } else if($entered_at < $day_end){
                $daily_hours = $day_end_t - $entered_at_t;
                $night_hours = $night_start_t - $day_end_t;
            } else {
                $night_hours = $night_start_t - $entered_at_t;
            }

            // modify the date for the end date
            $y_m_d = explode('-', $exited_at->format('Y-m-d'));
            $day_start->setDate($y_m_d[0], $y_m_d[1], $y_m_d[2]);
            $day_end->setDate($y_m_d[0], $y_m_d[1], $y_m_d[2]);
            $night_start->setDate($y_m_d[0], $y_m_d[1], $y_m_d[2]);

            //timestamps update for end date
            $day_start_t = $day_start->getTimestamp();
            $day_end_t = $day_end->getTimestamp();
            $night_start_t = $night_start->getTimestamp();

            // calculate for the end date 
            if($exited_at < $day_start){
                $night_hours +=  ($exited_at_t - $night_start_t);
            } else if($exited_at < $day_end){
                $daily_hours += ($exited_at_t - $day_start_t); 
                $night_hours += ($day_start_t - $night_start_t);
            } else {
                $daily_hours += ($day_end_t - $day_start_t);
                $night_hours += ($exited_at_t - $day_end_t) + ($day_start_t - $night_start_t);
            }

            // the interval betwen start and end date
            $entered_at->setTime(0, 0);
            $exited_at->setTime(0, 0);
            $datediff = $entered_at->diff($exited_at)->days - 1;
            if ($datediff > 0) {
                // the hour multiplay by the days then by the rate
                $night_duration = 24 - Helper::secondsToHoursMinutes($day_end_t - $day_start_t);
                $night_duration *= 3600;

                $daily_hours += (($day_end_t - $day_start_t) * $datediff);
                $night_hours += ($night_duration * $datediff);
            }
        }

        // in decimal to be able to calculate the seconds too
        $daily_hours_d = Helper::secondsToHoursMinutes($daily_hours, 'decimal');
        $night_hours_d = Helper::secondsToHoursMinutes($night_hours, 'decimal');

        $without_discount = ($daily_hours_d * $daily_rate) + ($night_hours_d * $night_rate);


        $total_price = $without_discount;
        if($this->card){
            $total_price = $without_discount - ($without_discount * ($this->card->discount / 100));
        }

        // format to be with 2 decimals after separator
        $without_discount = number_format((float)$without_discount, 2, '.', '');
        $total_price = number_format((float)$total_price, 2, '.', '');

        $result = [
            'daily_hours' => Helper::secondsToHoursMinutes($daily_hours, 'HH:MM:SS'),
            'night_hours' => Helper::secondsToHoursMinutes($night_hours, 'HH:MM:SS'),
            'currency' => $this->parkingLot->currency,
            'discount' => $this->card->discount ?? 0 . '%',
            'without_discount' => $without_discount,
            'total_price' => $total_price,
        ];

        return $result;
    }

    public function parkingLot()
    {
        return $this->belongsTo(ParkingLot::class);
    }

    /**
     * Get the vehicle type.
     */
    public function vehicleType()
    {
        return $this->hasOne(VehicleType::class, 'id', 'vehicle_type_id');
    }

    /**
     * Get the card.
     */
    public function card()
    {
        return $this->hasOne(Card::class, 'id', 'card_id');
    }
}
