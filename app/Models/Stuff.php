<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

//  Mendefinisikan kelas Stuff yang merupakan turunan dari kelas Model
class Stuff extends Model 
{
    use SoftDeletes; //optional, digunakan hanya untuk table yg menggunakan fitur softdeletes
    protected $fillable = ["name", "category"]; 
//  Mendefinisikan kolom-kolom yang dapat diisi secara massal melalui metode create() atau fill()
    public function stuffStock()
    {
        return $this->hasOne(StuffStock::class);
    }

    public function InboundStuff()
    {
        return $this->hasMany(InboundStuff::class);
    }   

    public function lendings()
    {
        return $this->hasMany(Lending::class);
    }   
}
