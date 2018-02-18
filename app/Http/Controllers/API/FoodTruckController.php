<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
#use Illuminate\Validation\Validator;
use Validator;

use App\Models\User;
use App\Models\FoodTruck;


class FoodTruckController extends ApiController
{
    protected $truck;

    public function __construct(FoodTruck $truck, Request $request)
    {
        $this->truck = $truck;
        $this->request = $request;
    }
	/**
     * @SWG\GET(
     *     path="/user/foodTrucks",
     *     summary="This api is used to get all food trucks detail",
     *     tags={"User App"},
     *     @SWG\Response(
     *         response=200,
     *         description="success"
     *     )
     * )
     */
    public function allUserTrucks(){

        $allTrucks = $this->truck->getUserTrucks();
		
        if($allTrucks) {
            return $this->response($allTrucks,'s','200','trucks find.');
        }
		
        return $this->response(null,'f','500','failed to find category');
    }
	
	/**
     * @SWG\POST(
     *     path="/user/{id}/foodTrucks",
     *     summary="This api is used to add food truck",
     *     tags={"Restaurant App"},
	 *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="user id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="name of truck",
     *         required=true,
     *         type="string"
     *     ),
	 *     @SWG\Parameter(
     *         name="image",
     *         in="formData",
     *         description="image of truck",
     *         required=false,
     *         type="file"
     *     ),
     *     @SWG\Parameter(
     *         name="sort_order",
     *         in="formData",
     *         description="sort order of truck",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="status",
     *         in="formData",
     *         description="Status (enable/disable) of truck",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="success"
     *     )
     * )
     */
    public function addFoodTruck($id){
		
		$user = USER::where('id', $id)->where('user_type', 'restaurant')->where('status', 'active')->count();
		
        if(!$user) {
            return $this->response(null,'f','500','user is not found');
        }

        $this->validate($this->request, [
            'name' => 'required',
			'status' => 'required|in:enable,disable',
        ]);
		
		$truck = new FoodTruck;
        $truck->user_id = $id;
        $truck->name = $this->request->name;
        $truck->sort_order = $this->request->sort_order;         
        $truck->status = $this->request->status;
		
		if($this->request->hasFile('image')) {
			$this->validate($this->request, [
				'image' => 'image|mimes:jpeg,png,jpg,gif,svg',
			]);
			
			$file = $this->request->file('image');
            $filename = $this->request->image->hashName();
			
			Storage::disk('truck')->put($filename, file_get_contents($file));
			$path = $filename;
			
			$image = $filename;
			$image_url = url('/uploads/truck/'.$filename);
			
			$truck->image = $image;
			$truck->image_url = $image_url;
		}
		
		$truck->save();

        if($truck){
            return $this->response($truck,'s','200','truck created successfully');
        }
        return $this->response($category,'f','500','failed to create category');
    }
	/**
     * @SWG\POST(
     *     path="/user/foodTrucks/{id}",
     *     summary="This api is used to update food truck detail",
     *     tags={"Restaurant App"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="truck id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="name of truck",
     *         required=true,
     *         type="string"
     *     ),
	 *     @SWG\Parameter(
     *         name="image",
     *         in="formData",
     *         description="image of truck",
     *         required=false,
     *         type="file"
     *     ),
     *     @SWG\Parameter(
     *         name="sort_order",
     *         in="formData",
     *         description="sort order of truck",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="status",
     *         in="formData",
     *         description="Status (enable/disable) of truck",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="success"
     *     )
     * )
     */
    public function editFoodTruck($id){
		
		$truck = $this->truck->find($id);
        if(!$truck) {
            return $this->response(null,'f','500','truck not found');
        }
		
        $this->validate($this->request, [
            'name' => 'required',
			'status' => 'required|in:enable,disable',
        ]);
		
        $truck->user_id = $id;
        $truck->name = $this->request->name;
        $truck->sort_order = $this->request->sort_order;         
        $truck->status = $this->request->status;
		
		if($this->request->hasFile('image')) {
			$this->validate($this->request, [
				'image' => 'image|mimes:jpeg,png,jpg,gif,svg',
			]);
			
			$file = $this->request->file('image');
            $filename = $this->request->image->hashName();
			
			Storage::disk('truck')->delete($truck->image);
			Storage::disk('truck')->put($filename, file_get_contents($file));
			
			$image = $filename;
			$image_url = url('/uploads/truck/'.$filename);
			
			$truck->image = $image;
			$truck->image_url = $image_url;
		}
		
		$truck->save();

        if($truck){
            return $this->response($truck,'s','200','truck updated successfully');
        }
        return $this->response($category,'f','500','failed to create category');
    }
}
