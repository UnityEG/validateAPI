<?php

namespace App\Http\Models;

use App\EssentialEntities\Transformers\UserGroupTransformer;
use App\EssentialEntities\Transformers\UserTransformer;
use App\EssentialEntities\Transformers\RuleTransformer;
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
    
    /**
     * Get Standard Json API format for single object
     * @return array
     */
    public function getStandardJsonFormat( ) {
        return (new UserGroupTransformer())->transform($this->prepareUserGroupGreedyData());
    }
    
    /**
     * Get Before standard Json API format to make a class of Json objects
     * @return array
     */
    public function getBeforeStandardArray( ) {
        return (new UserGroupTransformer())->beforeStandard($this->prepareUserGroupGreedyData());
    }
    
    /**
     * Prepare User Group and its relationships data in a greedy way to be used in Json standard format
     * @return array
     */
    private function prepareUserGroupGreedyData() {
        $user_group_greedy_array = $this->load('users', 'rules')->toArray();
        (empty($user_group_greedy_array['users'])) ?  : $user_group_greedy_array['users'] = (new UserTransformer())->transformCollection( $user_group_greedy_array['users']);
        (empty($user_group_greedy_array['rules'])) ?  : $user_group_greedy_array['rules'] = (new RuleTransformer())->transformCollection($user_group_greedy_array['rules']);
        return $user_group_greedy_array;
    }

}
