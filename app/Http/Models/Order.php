<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'orders';
    
    /**
     * Fillable fields
     * @var array
     */
    protected $fillable = ['user_id', 'number', 'tax'];
    
    /**
     * Relationship between Order Model and User Model (many to one)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    
    /**
     * Relationship between Order Model and Voucher Model (one to many)
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vouchers(){
        return $this->hasMany('App\Http\Models\Voucher', 'order_id', 'id');
    }
    
}
