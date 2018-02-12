<?php

namespace App\Http\Controllers;

/**
 * @SWG\Swagger(
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Truck API",
 *         contact={"email":"nirmalgoswami247@gmail.com"},
 *     ),
 *     consumes={"application/x-www-form-urlencoded"},
 *     produces={"application/json"}
 * )
 */
/**
 * @SWG\Tag(
 *   name="User App",
 *   description="User App APIs",
 * )
 *  @SWG\Tag(
 *   name="Driver App",
 *   description="Driver App APIs",
 * )
 * @SWG\Tag(
 *   name="Restaurant App",
 *   description="Restaurant App APIs",
 * )
 */
class ApiController extends Controller
{


    public function response($data,$status='fail',$code='200',$message='',$trace=''){

        if($status == 's'){
            $status = 'success';
        }else if($status == 'f'){
            $status = 'fail';
        }
        $res=[];
        $res['status']=$status;
        $res['code']=$code;
        $res['data']=$data;
        $res['message']=$message;

        if($status == 'fail'){
            $res['trace']=$trace;
        }


        return Response()->json($res,$code);
    }
}
