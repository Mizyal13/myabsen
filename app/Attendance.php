<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    //
    protected $fillable = [
        'employee_id',
        'entry_ip',
        'entry_time',
        'entry_location',
        'time',
        'entry_photo',
        'exit_photo',
        'exit_ip',
        'exit_location',
        'registered',
    ];
    public function employee() {
        return $this->belongsTo('App\Employee');
    }
}
