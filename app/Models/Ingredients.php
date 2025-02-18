<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ingredients extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'total_amount',
        'current_amount',
        'is_alert_email_sent'
    ];
    
    public function Product()
    {
        return $this->belongsTo(Product::class, 'id');
    }
}
