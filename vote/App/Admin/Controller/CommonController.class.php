<?php
namespace Admin\Controller;
use Think\Controller;

class CommonController extends Controller{
    /*
     * 检验是否登录
     *
     * **/
    public function __construct(){
        parent::__construct();
        $name = session('login_name');
        $password = session('login_password');
        if(!isset($name) || empty($name)){
            $this -> error('请先登录',U('Login/login'),3);
        }
        if(!isset($password) || empty($password)){
            $this -> error('请先登录',U('Login/login'),3);
        }
    }
}