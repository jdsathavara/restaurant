<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model {

    protected $table = 'product_size';
    protected $fillable = [
        'product_id',
        'name',
        'price'
    ];
    protected $hidden = ['product_id', 'created_at', 'updated_at'];

    public function product() {
        return $this->belongsTo('App\Models\Product');
    }
}