<?php

namespace App\EssentialEntities\Transformers;

/**
 * Description of UserGroupTransformer
 *
 * @author mohamed
 */
class UserGroupTransformer extends Transformer{

    public function beforeStandard( array $item ) {
        $response = [
            "id" => (string)$item['id'],
            "group_name" => (string)$item['group_name']
        ];
        (empty($item['users'])) ?  : $response["relations"]["users"]["data"] = $item['users'];
        (empty($item['rules'])) ?  : $response["relations"]["rules"]["data"] = $item['rules'];
        return $response;
    }

    public function transform( array $item ) {
        return ["data"=>  $this->beforeStandard($item)];
    }

}
