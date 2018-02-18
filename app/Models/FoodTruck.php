<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodTruck extends Model {

    protected $table = 'food_truck';
    protected $fillable = [
        'user_id',
        'name',
        'image',
        'image_url',
        'sort_order',
        'status'
    ];
    protected $hidden = ['user_id', 'image', 'created_at', 'updated_at'];

    public function getUserTrucks() {
		$trucks = self::where('status', 1)->paginate(10);
		return $trucks;
	}
}