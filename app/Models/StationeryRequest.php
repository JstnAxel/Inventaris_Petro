<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StationeryRequest extends Model
{
        protected $fillable = ['user_id', 'stationary_id', 'quantity', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stationary()
    {
        return $this->belongsTo(Stationary::class, 'stationary_id');
    }

}
