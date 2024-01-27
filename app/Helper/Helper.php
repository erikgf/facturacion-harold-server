<?php

namespace App\Helper;

class Helper{
    public static function cleanJson(array $array_json){
        return array_filter($array_json, function($item){
            return $item != null;
        });
    }
}