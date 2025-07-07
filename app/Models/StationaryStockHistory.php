<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StationaryStockHistory extends Model
{
    protected $fillable = [
        "stationary_id",
        "amount",
    ];
}
