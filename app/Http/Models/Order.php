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
    
    public function getBeforeStandardArray(){
        return \OrderTransformer::beforeStandard($this->prepareOrderGreedyData());
    }

    private function prepareOrderGreedyData() {
        $order_greedy_data = $this->load( ['user', 'vouchers'])->toArray();
        (empty($order_greedy_data['user'])) ?  : $order_greedy_data['user'] = \UserTransformer::beforeStandard( $order_greedy_data['user']);
        (empty($order_greedy_data['vouchers'])) ?  : $order_greedy_data['vouchers'] = \VoucherTransformer::transformCollection( $order_greedy_data['vouchers']);
        return $order_greedy_data;
            }
    
//    todo Create getBeforeStandardArray method in Order Model class
//    todo Create prepareOrderGreedyData method in Order Model
    
}
