<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_number', 'lot_number', 'quantity', 'date_manufactured', 'date_released', 'serial_number'
    ];

    public function setLotNumberAttribute($value)
    {
        $lot_date = substr($value, 0, 6);
        $date = Carbon::createFromFormat('dmy', $lot_date);
        $this->attributes['date_manufactured'] = $date;
        $this->attributes['lot_number'] = $value;
    }

    public function scopeCreatedBetween($query, $from, $to)
    {
        $from = Carbon::parse($from)->setHour(0);
        $to = Carbon::parse($to)->setHour(23)->setMinute(59);
        return $query->whereBetween('created_at', [$from, $to]);
    }

    public function scopeReleasedBetween($query, $from, $to)
    {
        $from = Carbon::parse($from)->setHour(0);
        $to = Carbon::parse($to)->setHour(23)->setMinute(59);
        return $query->whereBetween('date_released', [$from, $to]);
    }
}
