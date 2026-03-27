<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'product_name',
        'quantity',
        'total',
        'status',
    ];

    /**
     * Get the user that owns the monitor.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}