<?php
namespace Admin\Controller;
use Think\Controller;
class UserController extends Controller{
    public function user_list(){
         $static_file='./Html/Admin/'.date('Y-m-d').'.html';
         if(file_exists($static_file)){
                $expr=3600*24*10;
                $file_ctime=filectime($static_file);
                if($expr+$file_ctime>time()){
                 echo file_get_contents($static_file);exit();
             }else{
                unlink($static_file);
                file_put_contents($static_file,$this->static_create());
                ob_end_flush();
             }
         }else{
           file_put_contents($static_file,$this->static_create());
           ob_end_flush();
         } 
    }
    public function static_create(){
        ob_start();
        $userObj=D('user');
        $page=I('get.p',1);
        $count=$userObj->count();
        //分页的第一个参数是页码数，第二个参数是每页显示的条数
        $data=$userObj->alias('u')
            ->field('u.*,d.dept_name')
            ->join("join oa_dept d on u.user_dept = d.dept_id")
            ->page($page,C('pagesize'))
            ->select();
        //$pageObj=new \Think\Page($count,C('pagesize'));
        //$pagelist=$pageObj->show();
        //$this->assign('pagelist',$pagelist);
        $this->assign('count',$count);
        $this->assign('pagesize',C('pagesize'));
        $this->assign('user_list',$data);
        $this->display();
        return $content=ob_get_contents();
    }
    public function user_add(){
        $userObj=M('dept');
        $data=$userObj->field('dept_name,dept_id')
                ->select();
        $this->assign('dept',$data);
        $this->display();
    }
    public function addHanddle(){
        $userObj=D('user');
        $data=$userObj->create('',1);
        //$userObj->user_ctime=date('Y-m-d');
        if(!$data){
           $error= $userObj->getError();
            $this->error("$error",U('user_add',3));
        }else{
            if($userObj->add()){
                $this->success('添加成功',U('user_list'),3);
            }else{
                $this->error('添加失败',U('user_add'),3);
            }
        }
    }
    public function getContent(){
        $page=I('get.page');
        $user=D('user');
        $data=$user->page($page,C('pagesize'))
                   ->select();
        $this->assign('user_list',$data);
        $this->display();
    }
    //统计
    public function addUp(){
        $sql="select dept_name,count(*) num from oa_dept d join oa_user u on d.dept_id = u.user_dept
              group by user_dept";
        $user=D('user');
        $data=$user->query($sql);
        $str='';
        foreach($data as $value){
            $str.="['$value[dept_name]'".','."$value[num]],";
        }
        //echo $str;
       // exit();
        $this->assign('str',$str);
        $this->display();
    }
}