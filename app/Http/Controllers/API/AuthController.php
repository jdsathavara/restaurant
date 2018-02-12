<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Response;
#use Illuminate\Validation\Validator;
use Validator;

use App\Models\User;


class AuthController extends ApiController
{
    protected $user;

    public function __construct(User $user,Request $request)
    {
        $this->user = $user;
        $this->request = $request;

    }
    /**
     * @SWG\POST(
     *     path="/auth/login",
     *     summary="This api is used to login user",
     *     tags={"Driver App","Restaurant App","User App"},
     *     @SWG\Parameter(
     *         name="user_type",
     *         in="formData",
     *         description="type of user (user/restaurant/driver)",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="mobile",
     *         in="formData",
     *         description="mobile number of user",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="password",
     *         in="formData",
     *         description="password of user",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="success"
     *     )
     * )
     */
    public function login(){

        $this->validate($this->request, [
            'user_type' => 'required|in:user,driver,restaurant',
            'mobile' => 'required',
            'password' => 'required'
        ]);


        $type = $this->request->input('user_type');
        $mobile = $this->request->input('mobile');
        $password = $this->request->input('password');

        $user = User::where(array('mobile'=>$mobile,'password'=>$password,'user_type'=>$type))->first();

        if($user){
            $verify = User::where(array('mobile'=>$mobile,'password'=>$password,'user_type'=>$type,'status'=>'mobile-verify'))->first();
            if($verify){
                $verify['mobile_verified']=0;
                return $this->response($verify,'f','200','please verify mobile first');

            } else{
                $this->request->request->add(['is_login'=>'yes']);
                $update = $this->user->updateUser($user->id,array('is_login'=>'yes'));

                if($update){
                    $update['mobile_verified']=1;
                    return $this->response($update,'s','200','user is available');
                }else{
                    return $this->response(null,'f','500','something went wrong');
                }
            }


        } else
            return $this->response($user,'f','200','mobile and password are wrong');

    }


    /**
     * @SWG\POST(
     *     path="/auth/register",
     *     summary="This api is used to register user",
     *     tags={"Driver App","Restaurant App","User App"},
     *     @SWG\Parameter(
     *         name="user_type",
     *         in="formData",
     *         description="type of user (user/restaurant/driver)",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="mobile",
     *         in="formData",
     *         description="mobile number of user",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="password",
     *         in="formData",
     *         description="password of user",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="success"
     *     )
     * )
     */
    public function register(){

        $this->validate($this->request, [
            'user_type' => 'required|in:user,driver,restaurant',
            'mobile' => 'required',
            'password' => 'required'
        ]);

        $checkduplicate = User::where(array('mobile'=>$this->request->input('mobile'),'user_type'=>$this->request->input('user_type')))->first();
        if($checkduplicate){
            $verify = User::where(array('mobile'=>$this->request->input('mobile'),'user_type'=>$this->request->input('user_type'),'status'=>'mobile-verify'))->first();
            if($verify){
                $verify['mobile_verified']=0;
                return $this->response($verify,'f','200','You are already registered. kindly verify your mobile first');

            } else{

                return $this->response(null,'f','200','mobile already exists');
            }

        }

        //@todo implement sms gateway and set verify code
        $this->request->request->add(['mobile_verify_code'=>'123456']);

        $user = $this->user->createUser($this->request);

        if($user){

            return $this->response($user,'s','200','account created successfully');
        }
        return $this->response($user,'f','500','failed to create account');

    }
    /**
     * @SWG\POST(
     *     path="/auth/verifyMobile/{id}",
     *     summary="This api is used to verify user mobile",
     *     tags={"Driver App","Restaurant App","User App"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="user id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="verify_code",
     *         in="formData",
     *         description="verification code which is sent on user mobile. for now use 123456",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="success"
     *     )
     * )
     */
    public function verifyMobile($id){

        $this->validate($this->request, [
            'verify_code' => 'required'
        ]);

        $check = User::where(array('id'=>$id,'mobile_verify_code'=>$this->request->input('verify_code')))->first();
        if($check){

            //update user fields
            $this->request->request->add(['mobile_verify_code'=>'']);
            $this->request->request->add(['status'=>'active']);
            $update = $this->user->updateUser($id,$this->request->all());

            if($update){
                return $this->response(null,'s','200','account has been activated successfully');
            }
            return $this->response(null,'f','500','failed to activate account');

        }else{
            return $this->response(null,'f','200','code is invalid');
        }

    }
    /**
     * @SWG\PUT(
     *     path="/auth/forgotPassword",
     *     summary="This api is used to send verification code on email to reset password",
     *     tags={"Driver App","Restaurant App","User App"},
     *     @SWG\Parameter(
     *         name="user_type",
     *         in="formData",
     *         description="type of user (user/restaurant/driver)",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="mobile",
     *         in="formData",
     *         description="mobile number of user",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="success"
     *     )
     * )
     */
    public function forgotPassword(Request $request){


        // Perform Validation
        $validator = Validator::make($request->all(), [
            'user_type' => 'required|in:user,driver,restaurant',
            'mobile' => 'required',
        ]);
        if ($validator->errors()->count()) {
            return $this->response(null,'f','422','The given data was invalid.',$validator->errors());
        }
        $user = User::where(array('mobile'=>$request->input('mobile'),'user_type'=>$request->input('user_type')))->first();

        if($user){
            //update user fields
            $this->request->request->add(['reset_password_verify_code'=>'123456']);
            $update = User::where(array('mobile'=>$request->input('mobile'),'user_type'=>$request->input('user_type')))->update($this->request->all());
            if($update){
                return $this->response(null,'s','200','code has been sent');
            }else{
                return $this->response(null,'f','500','failed to set email verification code');
            }

        } else
            return $this->response($user,'f','200','code is wrong');
    }
    /**
     * @SWG\PUT(
     *     path="/auth/resetPassword",
     *     summary="This api is used to reset password of user",
     *     tags={"Driver App","Restaurant App","User App"},
     *     @SWG\Parameter(
     *         name="reset_password_verify_code",
     *         in="formData",
     *         description="Verification code which is sent on mobile, for now use : 123456",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="password",
     *         in="formData",
     *         description="new password of account",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="success"
     *     )
     * )
     */
    public function resetPassword(Request $request){
        // Perform Validation
        $validator = Validator::make($request->all(), [
            'reset_password_verify_code' => 'required',
            'password' => 'required'
        ]);
        if ($validator->errors()->count()) {
            return $this->response(null,'f','422','The given data was invalid.',$validator->errors());
        }
        $user = User::where(array('reset_password_verify_code'=>$request->input('reset_password_verify_code')))->first();

        if($user){
            $verifyCode = $request->input('reset_password_verify_code');
            $this->request->request->add(['reset_password_verify_code'=>'']);
            $update = User::where('reset_password_verify_code', $verifyCode)->update($this->request->all());
            if($update){
                return $this->response(null,'s','200','password has been reset successfully');
            }else{
                return $this->response(null,'f','500','failed to reset password');
            }

        } else
            return $this->response($user,'f','200','verification code is wrong');
    }

}
