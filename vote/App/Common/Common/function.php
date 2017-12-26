<?php
//几乎所有的方法最后的结果都是返回回来的，看似理解很简单，有时候但很容易忘记
function getTreeSort($arr,$pid=0){
    static $sort=array();
    foreach($arr as $value){
        if($value['dept_pid']==$pid){
            $sort[]=$value;
            getTreeSort($arr,$value['dept_id']);
        }
    }
    return $sort;
}
function checkSex($sex){
    $arr=array('男','女');
    if(!in_array($sex,$arr)){
        return false;
    }
    return true;
}
function setTime(){
    return date('Y-m-d');
}