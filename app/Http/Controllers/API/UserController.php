<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Response;
#use Illuminate\Validation\Validator;
#use Illuminate\Contracts\Validation\Validator;
use Validator;

use App\Models\User;


class UserController extends ApiController
{
    protected $user;
    protected $request;
    /**
     * @param Response
     */
    public function __construct(User $user,Request $request)
    {
        $this->user = $user;
        $this->request = $request;

    }
    /**
     * @SWG\PUT(
     *     path="/user/{id}/updateProfile",
     *     summary="This api is used to update user detail",
     *     tags={"Driver App","Restaurant App","User App"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="user id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="first_name",
     *         in="formData",
     *         description="first name of user",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="last_name",
     *         in="formData",
     *         description="last name of user",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="email",
     *         in="formData",
     *         description="email of user",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="success"
     *     )
     * )
     */
    public function updateProfile($id){

        $user = $this->user->find($id);
        if(!$user) {
            return $this->response(null,'f','500','user not found');
        }

        // Perform Validation
        $validator = Validator::make($request->all(), [
            'email' => 'unique:user,email,'.$id
        ]);
        if ($validator->errors()->count()) {
            return $this->response(null,'f','200','Email is already registered');
        }

        //update user fields
        $update = $this->user->updateUser($id,$request->all());

        if($update){
            return $this->response($update,'s','200','account has been updated successfully');
        }
        return $this->response(null,'f','500','failed to update account');

    }
    /**
     * @SWG\PUT(
     *     path="/user/{id}/logout",
     *     summary="This api is used to logout",
     *     tags={"Driver App","Restaurant App","User App"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="user id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="success"
     *     )
     * )
     */
    public function logout($id,Request $request){

        $user = $this->user->find($id);
        if(!$user) {
            return $this->response(null,'f','500','user not found');
        }
        //update user fields
        $this->request->request->add(['is_login'=>'no']);
        $update = $this->user->updateUser($id,$request->all());

        if($update){
            return $this->response(null,'s','200','logout has been successfully');
        }
        return $this->response(null,'f','500','failed to update account');
    }
    /**
     * @SWG\PUT(
     *     path="/user/{id}/sendCode",
     *     summary="This api is used to send verification code on mobile",
     *     tags={"Driver App","Restaurant App","User App"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="user id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="success"
     *     )
     * )
     */
    public function sendCode($id,Request $request){

        $user = $this->user->find($id);
        if(!$user) {
            return $this->response(null,'f','500','user not found');
        }
        //update user fields
        //@todo implement sms gateway and set verify code
        $this->request->request->add(['mobile_verify_code'=>'123456']);
        $update = $this->user->updateUser($id,$request->all());

        if($update){
            return $this->response(null,'s','200','code has been sent');
        }
        return $this->response(null,'f','500','failed to sent code');
    }

}
