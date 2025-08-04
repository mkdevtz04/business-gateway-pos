<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 
        'category_id', 
        'quantity_available', 
        'size', 
        'price', 
        'tax_rate', 
        'last_updated',
        'image_path' // Add this line
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function checkLowStock()
    {
        if ($this->quantity_available < 10) {
            \Auth::user()->notify(new LowStockNotification($this));
        }
    }
}
