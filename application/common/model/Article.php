<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/22
 * Time: 11:00
 */
namespace app\common\model;

use think\Model;

class Article extends Model
{
    public function getUpdateTimeAttr($value)
    {
        $update_time = date('Y-m-d',$value);
        return $update_time;
    }
}