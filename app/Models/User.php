<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {

    protected $table = 'user';
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile',
        'password',
        'user_type',
        'mobile_verify_code',
        'email_verify_code',
        'reset_password_verify_code',
        'is_login',
        'status'
    ];
    protected $hidden = [ 'mobile','user_type','mobile_verify_code','email_verify_code','reset_password_verify_code','status','password','updated_at','created_at','is_login' ];

    public function createUser($input){
        return $this->create($input->all());
    }
    public function updateUser($id,$input){

        $updated = $this->find($id)->update($input);
        $user = $this->find($id);
        if($updated) {
            return $user;
        }
        return false;
    }
}