<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    protected $table = "user_groups";
    protected $fillable = ['group_name'];
    
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
    
//    todo Create getStandardJsonFormat method
//    todo Create getBeforeStandardArray method
//    todo Create prepareUserGroupGreedyData method
//    todo Create UserGroupsController class
//    todo Create show method in UserGroupsController class
//    todo Create index method in UserGroupsController class.
//    todo Create show route
//    todo Create index route
//    todo build data for testing show method
//    todo test show method
//    todo build data for testing index method
//    todo test index method
}
