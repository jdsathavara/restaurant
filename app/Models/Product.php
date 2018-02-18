<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    protected $table = 'product';
    protected $fillable = [
        'category_id',
        'user_id',
        'name',
        'price',
        'description',
        'dressing',
        'sort_order',
        'status',
    ];
    protected $hidden = ['user_id', 'category_id', 'sort_order', 'status','updated_at', 'created_at'];
	
	public function sizes() {
        return $this->hasMany('App\Models\ProductSize');
    }
}