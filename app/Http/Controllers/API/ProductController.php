<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Response;
#use Illuminate\Validation\Validator;
use Validator;

use App\Models\Product;
use App\Models\ProductSize;


class ProductController extends ApiController
{
    protected $product_size;

    public function __construct(Product $product, ProductSize $product_size, Request $request)
    {
		$this->product = $product;
		$this->product_size = $product;
        $this->request = $request;
    }
    public function addProduct(){

        $this->validate($this->request, [
            'name' => 'required',
			'status' => 'required|in:enable,disable',
        ]);
		/* $product = new Product;
		$product->name = 'krunal';
		$product->save();
		
		$sizes = new ProductSize(['name' => 'A new sizes']);
		$product = Product::find(12);
		$product->sizes()->save($sizes); */
		
		
		print_r($product);
		die('111');

        if($product){
            return $this->response($product,'s','200','product created successfully');
        }
        return $this->response($product,'f','500','failed to create product');
    }
}
