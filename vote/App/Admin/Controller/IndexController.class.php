<?php
namespace Admin\Controller;

class IndexController extends CommonController{
    public function index(){
        $this->display();
    }
    public function iframe(){
        $this->display();
    }
    /*
     * 退出
     *
     * **/

    public function loginOut(){
        session('login_name',null);
        session('login_password',null);
        $this->success('退出成功',U('Login/login'));
    }
}