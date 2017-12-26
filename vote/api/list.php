<?php
/**
 * vote 接口
 */
header('Content-type:text/json');
//get方法 wid
date_default_timezone_set('PRC');

$wid = $_GET['wid'];

if (!isset($wid)) {
	$errRet = array("code" => 1001,"desc" => "wid为空");
	$retArr = array("status" => 0, "result" => $errRet);
	echo json_encode($retArr);
	exit();
} 


//耗时
// $stime=microtime(true);

try{
	$pdoMysql = new PDO("mysql:host=127.0.0.1;port=3306;dbname=vote","root","YChd2016!@#");
	$pdoMysql->exec('set names utf8');
	
	$strSql1   = 'SELECT * FROM `voter` where wid= '.$wid;
	$resouce1  = $pdoMysql->query($strSql1);
	$result1   = $resouce1->fetch(PDO::FETCH_ASSOC);
	
	if (!empty($result1)) {
		$voteDate = $result1['date'];
		if ( $voteDate != date("Y-m-d")) {
			$voteIds = array();
		} else {
			$voteIds = explode(",", $result1['vote_ids']);;
		}
		
		
	} else {
		$retArr = array("status" => 2, "result" => "请关注！");
		echo json_encode($retArr);
		exit();
	}
	
	$strSql   = 'SELECT * FROM `candidate` ';
	$resouce  = $pdoMysql->query($strSql);
	$result   = $resouce->fetchAll(PDO::FETCH_ASSOC);
	
	
	if (!empty($result) && is_array($result)) {
		foreach($result as &$item) {
			if (in_array($item['id'], $voteIds)) {
				$item['is_vote'] = true;
			} else {
				$item['is_vote'] = false;
			}
		}
		
		$retArr = array("status" => 1,"total"=> count($voteIds) ,"result" => $result);
	} else {
		$errRet = array("code" => 1001,"desc" => "数据库错误");
		$retArr = array("status" => 0, "result" => $errRet);
	}
	
	
}catch(Exception $e){
    $errRet = array("code" => 1001,"desc" => "数据库错误");
	$retArr = array("status" => 0, "result" => $errRet);
}

// $etime=microtime(true);
// $total=$etime-$stime;   
// echo $total;
echo json_encode($retArr);