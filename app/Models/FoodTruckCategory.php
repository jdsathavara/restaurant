<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodTruckCategory extends Model {

    protected $table = 'food_truck_category';
    protected $fillable = [
		'food_truck_id',
        'name',
        'parent_id',
        'sort_order',
        'status'
    ];
    protected $hidden = ['food_truck_id' ,'updated_at', 'created_at'];
	
	public function childMenus() { 
		return $this->hasMany( 'App\Models\FoodTruckCategory', 'parent_id'); 
	} 

	public function parentMenus() {
		return $this->belongsTo('App\Models\FoodTruckCategory', 'parent_id'); 
	}  

    public function addCategory($input) {
        return $this->create($input->all());
    }
	
    public function updateCategory($id, $input) {
        $updated = $this->find($id)->update($input);
        $category = $this->find($id);
        if($updated) {
            return $category;
        }
        return false;
    }
	
	public function deleteCategory($id) {
        $deleted = $this->destroy($id);
        return true;
    }
	
	public function getTruckCategories($id) {
		$categories = self::with('childMenus')
			->where('parent_id', 0)
			->where('food_truck_id', $id)
			->where('status', 'enable')
			->get();
		return $categories;
	}
}