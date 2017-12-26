<?php
namespace Wx\Controller;
use Think\Controller;
class IndexController extends Controller {
    public $appid = 'wxca95c7b351a92ded';
    public $appSecret = '182135f712b29573c5641a4697a2f145';
    public $limit = 9;
    

    /*
     * 微信页面授权
     *
     * **/
    public function wxAuth(){

        //$redirect_url = 'http://www.ychudong.com/vote/Wx/Index/voterLists';
        $redirect_url = 'http://www.ychudong.com/vote/Wx/Index/voterLists';
        $str = 'location:https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this -> appid.'&redirect_uri='.urlencode($redirect_url).'&response_type=code&scope=snsapi_base&state=vote#wechat_redirect';
        header($str);exit;
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this -> appid.'&secret='.$this -> appSecret.'&code='.$code.'&grant_type=authorization_code';
        $urlResult = $this -> https_request($url);

        $urlResult = json_decode($urlResult,true);
        //echo $str;
        //var_export($str);die;
    }
    /*
     * curl请求
     *
     * **/
    public function https_request($url, $data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    /*
     * 填写用户信息
     *
     * **/
    public function user(){
        $this -> display();
    }

    /*
     * 客户认证接口
     *
     * **/
    public function userVerify(){
        try{

        }catch(\Exception $e){

        }
    }







}