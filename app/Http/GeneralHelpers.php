<?php

/*
 * Custom Helpers
 */

/**
* Search for keys in array recursively and return with value or false
* @param array $array
* @param string $keySearch
* @return mixed 
*/
function array_deep_search(array $array, $keySearch)
{
//        todo Fix get value if equal to 0
//        todo solving the problem of deep array with adding another loop to walk throug non array values in the main array
   foreach ($array as $key => $item) {
       if ($key === $keySearch) {
           if ( $array[$keySearch] === FALSE || $array[$keySearch] === 0 || $array[$keySearch] === "0") {
               return (FALSE === $array[$keySearch]) ? "false" : "0";
           }else{
               return $array[$keySearch];
           }
       }else {
           if (is_array($item) && ($result = array_deep_search($item, $keySearch))) {
              return $result;
           }//if (is_array($item) && ($result = $this->findKey($item, $keySearch)))
       }//if ($key === $keySearch)
   }
   return false;
}