<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    protected $table = "rules";
    protected $fillable = ['name'];
    
    /**
     * Relationship between Rule Model and UserGroups Model (many to many)
     * @return object
     */
    public function userGroups( ) {
        return $this->belongsToMany('App\Http\Models\UserGroup', 'user_groups_rules_rel', 'rule_id', 'user_group_id');
    }
}
