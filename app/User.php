<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

    use Authenticatable,
        CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function Merchant() {
        return $this->hasOne('App\Merchant');
    }

    public function FeedBack() {
        return $this->hasMany('App\FeedBack');
    }
    
    /**
     * Relationship between User Model and VoucherParameter Model (One to Many)
     * @return object
     */
    public function voucherParameters( ) {
        return $this->hasMany('App\Http\Models\VoucherParameter', 'user_id', 'id');
    }
    
    /**
     * Relationship between User Model and Voucher Model ( one to many)
     * @return object
     */
    public function vouchers( ) {
        return $this->hasMany('App\Http\Models\Voucher', 'user_id', 'id');
    }

    /**
     * Relationship between User Model and VoucherValidationLog Model ( one to many)
     * @return object
     */
    public function voucherValidationLogs( ) {
        return $this->hasMany('App\Http\Models\VoucherValidationLog', 'user_id', 'id');
    }

}
