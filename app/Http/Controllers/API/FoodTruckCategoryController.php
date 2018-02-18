<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Response;
#use Illuminate\Validation\Validator;
use Validator;

use App\Models\FoodTruckCategory;
use App\Models\FoodTruck;


class FoodTruckCategoryController extends ApiController
{
    protected $category;

    public function __construct(FoodTruckCategory $category,Request $request)
    {
        $this->category = $category;
        $this->request = $request;
    }


    /**
     * @SWG\POST(
     *     path="/user/foodTrucks/{id}/category",
     *     summary="This api is used to add food truck category",
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
     *         description="Name of category",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="parent_id",
     *         in="formData",
     *         description="Parent category ID",
     *         required=false,
     *         type="integer"
     *     ),
	 *     @SWG\Parameter(
     *         name="sort_order",
     *         in="formData",
     *         description="Sort order of category",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="status",
     *         in="formData",
     *         description="Status of (enable/disable)",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="success"
     *     )
     * )
     */
    public function addCategory($id){
		
		$truck = FoodTruck::where('id', $id)->where('status', 'enable')->count();
        if(!$truck) {
            return $this->response(null,'f','500','truck not found');
        }

        $this->validate($this->request, [
            'name' => 'required',
			'status' => 'required|in:enable,disable',
        ]);
		
		$this->request->request->add(['food_truck_id' => $id]);
		
        $category = $this->category->addCategory($this->request);

        if($category){
            return $this->response($category,'s','200','category created successfully');
        }
        return $this->response($category,'f','500','failed to create category');
    }
	/**
     * @SWG\PUT(
     *     path="/user/category/{id}",
     *     summary="This api is used to update category detail",
     *     tags={"Restaurant App"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="category id",
     *         required=true,
     *         type="integer"
     *     ),
	 *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="Name of category",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="parent_id",
     *         in="formData",
     *         description="Parent category ID",
     *         required=false,
     *         type="integer"
     *     ),
	 *     @SWG\Parameter(
     *         name="sort_order",
     *         in="formData",
     *         description="Sort order of category",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="status",
     *         in="formData",
     *         description="Status of (enable/disable)",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="success"
     *     )
     * )
     */
    public function updateCategory($id, Request $request){

        $category = $this->category->find($id);
        if(!$category) {
            return $this->response(null,'f','500','category not found');
        }
		
        $this->validate($this->request, [
            'name' => 'required',
			'status' => 'required|in:enable,disable',
        ]);

        $update = $this->category->updateCategory($id, $request->all());

        if($update){
            return $this->response($update,'s','200','category has been updated successfully');
        }
        return $this->response(null,'f','500','failed to update category');
    }
	/**
     * @SWG\DELETE(
     *     path="/user/category/{id}",
     *     summary="This api is used to delete category",
     *     tags={"Restaurant App"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="category id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="success"
     *     )
     * )
     */
    public function deleteCategory($id){

        $category = $this->category->find($id);
        if(!$category) {
            return $this->response(null,'f','500','category not found');
        }

        $delete = $this->category->deleteCategory($id);

        if($delete){
            return $this->response(null,'s','200','category has been deleted successfully');
        }
        return $this->response(null,'f','500','failed to delete category');
    }
	/**
     * @SWG\GET(
     *     path="/user/category/{id}",
     *     summary="This api is used to get category detail",
     *     tags={"Restaurant App"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="category id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="success"
     *     )
     * )
     */
	public function getCategory($id){

        $category = $this->category->find($id);
        if(!$category) {
            return $this->response(null,'f','500','category is not found');
        } else {
			return $this->response($category,'s','200','category has been found.');
		}
		
        return $this->response(null,'f','500','failed to find category');
    }
	/**
     * @SWG\GET(
     *     path="/user/foodTrucks/{id}/category",
     *     summary="This api is used to get all categories detail from truck id",
     *     tags={"User App", "User App"},
	 *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="truck id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="success"
     *     )
     * )
     */
    public function getTruckCategory($id){
		
		$category = FoodTruckCategory::where('parent_id', 0)->where('food_truck_id', $id)->where('status', 'enable')->count();
		
        if(!$category) {
            return $this->response(null,'f','500','category not found');
        }

        $allCategories = $this->category->getTruckCategories($id);
		
        if($allCategories) {
            return $this->response($allCategories,'s','200','category found.');
        }
		
        return $this->response(null,'f','500','failed to find category');
    }
}
