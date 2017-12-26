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
    * 显示选票人列表
    *
    * **/

    public function voterLists(){
        /*
         * 接收授权码
         *
         * **/
        //dump($_GET);
        if(isset($_GET['wid']) && !empty($_GET['wid'])){
            $openid = $_GET['wid'];
            $listData = $this -> getListPrepare($openid);

            $list = $listData;
            //file_put_contents('openid.txt',json_encode($urlResult));

            $listArr = explode(',',$list['info']);
            $this -> assign('count',$listArr[0]);
            $this -> assign('openid',$listArr[1]);
            $this -> assign('page',$listArr[2]);
            $this -> assign('user_list',$list['user_list']);
            $userObj=M('Candidate');
            $totalData = $userObj -> select();
            $totalPage = ceil(count($totalData) / $this -> limit);
            $this -> assign('totalPage',$totalPage);
            $this -> display();
        }else{
            if(isset($_GET['code']) && !empty($_GET['code'])){
                $code = $_GET['code'];
                /*
                 * 通过授权码获取openid
                 *
                 * **/
                $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this -> appid.'&secret='.$this -> appSecret.'&code='.$code.'&grant_type=authorization_code';
                $urlResult = $this -> https_request($url);

                $urlResult = json_decode($urlResult,true);
                $openid = $urlResult['openid'];
                $listData = $this -> getListPrepare($openid);

                $list = $listData;
                //file_put_contents('openid.txt',json_encode($urlResult));

                $listArr = explode(',',$list['info']);
                $this -> assign('count',$listArr[0]);
                $this -> assign('openid',$listArr[1]);
                $this -> assign('page',$listArr[2]);
                $this -> assign('user_list',$list['user_list']);
                $userObj=M('Candidate');
                $totalData = $userObj -> select();
                $totalPage = ceil(count($totalData) / $this -> limit);
                $this -> assign('totalPage',$totalPage);
                $this -> display();

        }
           // file_put_contents('openid.txt',$openid);

        }


    }
    /*
     * 临时的候选人列表
     *
     * **/

    public function voterList(){
        $page = isset($_POST['page'])?$_POST['page']:1;
        $userObj=M('Candidate');
        //默认wxid
        $openid ='kkskkskskdkkkfkgkgkkhkkhkk';

        $listData = $this -> getListPrepare($openid,$page);
        $list = $listData;
        //候选人信息列表
        $this -> assign('user_list', $list['user_list']);
        //file_put_contents('list.txt',json_encode( $list['user_list']));
        //微信用户信息，以投票人数，页数
        $listArr = explode(',',$list['info']);
        $this -> assign('count',$listArr[0]);
        $this -> assign('openid',$listArr[1]);
        $this -> assign('page',$listArr[2]);
        $this -> assign('user_list',$listData['user_list']);
        $totalData = $userObj -> select();
        //总页数
        $totalPage = ceil(count($totalData) / $this -> limit);
        $this -> assign('totalPage',$totalPage);
        $this -> display();
    }

    /*
     * 微信短拉去获选人接口
     *
     * **/
    public function getList(){
        try{
            $postData = $_POST;
            //file_put_contents('gogo.txt',serialize($postData));
            if(!isset($postData['wid']) || empty($postData['wid'])){
                throw new \Exception('微信标识为空');
            }
            if(!isset($postData['page']) || empty($postData['page'])){
                throw new \Exception('页数为空');
            }
            $result = $this ->  getListPrepare($postData['wid'],$postData['page']);
            if($result['status'] != 1){
                throw new \Exception('拉去信息失败');
            }
            echo json_encode($result);
        }catch(\Exception $e){
            $result = array('status' => 3,'msg' => $e -> getMessage());
            echo json_encode($result);
        }
    }

    /*
     * 获取候选人详情
     *
     * **/

    public function getDetail(){
        try{
            if(!empty($_GET)){
                $getData = $_GET;
            }else{
                throw new \Exception('参数为空');
            }
            //id-is_vote-count
            $infoArr = explode(',',$getData['id']);
            $CandidateObj = M('Candidate');
            $condition = array();
            $condition['id'] = $infoArr[0];
            //获取详情
            $detail = $CandidateObj ->where($condition) -> find();
            $this -> assign('detail',$detail);
            $this -> assign('is_vote',$infoArr[1]);
            $this -> assign('count',$infoArr[2]);
            $this -> assign('openid',$getData['wid']);
            $this -> display();
        }catch(\Exception $e){
            echo $e ->getMessage();
        }
    }

    public function delete(){
        $VoterObj = M('Voter');
        $condition = array();
        $condition['wid'] = 'ojqVLt32rUKDcCNXXr6DrW-ZSeoQ';
        $a = $VoterObj -> where($condition) -> delete();
        dump($a);
    }
    public function get(){
        $VoterObj = M('Voter');
        $a = $VoterObj -> select();
        dump($a);
    }
    /*
     * 候选人信息获取接口
     *
     * **/
    public function getListPrepare($wx_id,$page=1){
        try{
            header('Content-type:text/json');
            date_default_timezone_set('PRC');

            if (!isset($wx_id)) {
                $retArr = array("status" => 0, "result" => array());
            }
            $VoterObj = M('Voter');
            $condition = array();
            $condition['wid'] = $wx_id;
            $result1 = $VoterObj -> where($condition) -> find();
            //file_put_contents('gogo.txt',$result1);
            if (!empty($result1)) {
                $voteDate = $result1['date'];
                if ( $voteDate != date("Y-m-d")) {
                    $voteIds = array();
                } else {
                    $voteIds = explode(",", $result1['vote_ids']);;
                }


            } else {
                $retArr = array("status" => 2, "result" => array());
            }

            $CandidateObj = M('Candidate');
            $field = '*';
            $result = $CandidateObj -> field($field) -> page($page) ->limit($this -> limit) -> order('votes desc') -> select();

            if (!empty($result) && is_array($result)) {
                foreach($result as &$item) {
                    if (in_array($item['id'], $voteIds)) {
                        $item['is_vote'] = 1;
                    } else {
                        $item['is_vote'] = 0;
                    }
                }
                $info = count($voteIds).','.$wx_id.','.$page;
                //file_put_contents('list.txt',json_encode($result));
                $retArr = array("status" => 1,"total"=> count($voteIds) ,"user_list" => $result,'info' => $info);
            } else {
                $retArr = array("status" => 0, "result" => array());
            }
            return $retArr;
        }catch(\Exception $e){
            $retArr = array("status" => 0, "result" => array());
            return $retArr;
        }
    }

    /*
     * 投票接口
     *
     * **/
    public function vote(){
        try{
            header('Content-type:text/json');
            date_default_timezone_set('PRC');
            file_put_contents('gogo.txt',serialize($_POST));

            if ( !isset( $_POST ) || empty( $_POST ) ){
                throw new \Exception('请求方式错误',0);
            }

            $postData = $_POST;

            if ( !isset($postData['wid']) || !isset($postData['ids'])  ){
                throw new \Exception('参数为空',0);

            }
            $Voter = M('Voter');
            $condition = array();
            $condition['wid'] = $postData['wid'];
            $result1 = $Voter ->where($condition) -> find();
            if (!empty($result1)) {
                $voteDate = $result1['date'];
                if ( $voteDate != date("Y-m-d")) {
                    $voteIds = array();
                } else {
                    $voteIds = explode(",", $result1['vote_ids']);
                }


            } else {
                $voteIds = array();
                //�µ�ͶƱ��
                if (!empty($postData['wid'])) {
                    $addData = array();
                    $addData['wid'] = $postData['wid'];
                    $addData['date'] = date("Y-m-d");
                    $addData['vote_ids'] = $postData['ids'];
                    $affected_rows = $Voter -> add($addData);
                   // file_put_contents('voter.txt',$affected_rows);

                    if($affected_rows ==false){
                        throw new \Exception('数据添加错误',3);
                    }
                }
            }

            if (count($voteIds) > 10) {
                throw new \ Exception('每次只能选10个人',2);
            }

            $ids = explode(",", $postData['ids']);
            file_put_contents('list.txt',json_encode($ids));

            foreach($ids as $id) {
                if(in_array($id, $voteIds)) {
                    continue;
                }
                $Candidate = M('Candidate');
                $condition = array();
                $condition['id'] = $id;
                $affected_rows = $Candidate -> where($condition) -> setInc('votes',1);
                if($affected_rows == false){
                    throw new \Exception('数据修改错误1',3);
                }
            }


            $voteIds = array_merge($voteIds,$ids);

            $voteIds = implode(',',$voteIds);
            file_put_contents('openid.txt',$voteIds);
            $condition = array();
            $condition['wid'] = $postData['wid'];
            $saveData = array();
            $saveData['date'] = date("Y-m-d");
            $saveData['vote_ids'] = $voteIds;
            $affected_rows = $Voter -> where($condition) -> save($saveData);
            if($affected_rows === false){
                throw new \Exception('数据修改错误2',3);
            }
            $retArr = array('status'=>1);
            echo json_encode($retArr);
        }catch(\Exception $e){
            $retArr = array('status' => $e -> getCode(),'message' => $e -> getMessage());
            echo json_encode($retArr);
        }
    }
}