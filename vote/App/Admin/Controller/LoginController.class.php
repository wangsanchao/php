<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller{
    /*
     * 显示登录页面
     *
     * **/
    public function login(){
        $this->display();
    }
    /*
     * 登录检验
     *
     * **/
    public function checkLogin(){
        $username = I('post.username');
        $password = I('post.password');
		$is_remember = I('post.remember');
        if(strtolower($username) == strtolower(C('LOGIN_NAME')) && $password ==C('LOGIN_PASSWORD')){
            //保存信息到session
            session('login_name',$username);
            session('login_password',$password);
            if(isset($is_remember) && strtolower($is_remember) == 'on'){
                //记住密码，存到cookie
                cookie('login_name',$username);
                cookie('login_password',$password);
            }
            $this->success('登录成功',U('Index/index'),1);
        }else{
            $this->error('登录失败',U('Login/login'),1);
        }
    }


}