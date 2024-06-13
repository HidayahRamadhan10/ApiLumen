<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Lending extends Model
{
    use SoftDeletes; 
    protected $fillable = ["stuff_id", "date_time", "name", "user_id", "total_stuff", "notes"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }   

    public function Stuff()
    {
        return $this->belongsTo(Stuff::class);
    } 

    public function restorations()
    {
        return $this->hasOne(Restoration::class);
    } 
}


