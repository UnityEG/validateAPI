<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class UserFeedback extends Model {

    //
    protected $table = 'feedback';
    protected $guarded = ['id'];
    //protected $fillable = ['title', 'body'];

    public function User() {
        return $this->belongsTo('App\User');
    }
    
}
