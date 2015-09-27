<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    protected $table = "user_groups";
    protected $fillable = ['name'];
    
    /**
     * Relationship between UserGroup Model and User Model (many to many)
     * @return object
     */
    public function users( ) {
        return $this->belongsToMany('App\User', 'users_user_groups_rel', 'user_group_id', 'user_id');
    }
    
    /**
     * Relationship between Rule Model and UserGroups Model (many to many)
     * @return object
     */
    public function rules( ) {
        return $this->belongsToMany('App\Http\Models\Rule', 'user_groups_rules_rel', 'user_group_id', 'rule_id');
    }
}
