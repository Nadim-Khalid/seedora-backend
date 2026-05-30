<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable =[
        'user_id',
        'store_name',
        'phone',
        'gst_number',
        'address',
        'approval_status',
        'approved_at'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
