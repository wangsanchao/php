<?php
/**
 * vote �ӿ�
 */
header('Content-type:text/json');
//post���� wid ids 
date_default_timezone_set('PRC');

//��־��¼
$dir = dirname(__FILE__).'/log';
if (!file_exists($dir)){
	mkdir ($dir,0777,true);
}
$fh = fopen($dir."/".date("Y-m-d")."-log","a");
fwrite ($fh,date('Y-m-d H:i:s').'*********************start***********************'."\r\n");

if ( !isset( $_POST ) || empty( $_POST ) ){
	fwrite ($fh,date('Y-m-d H:i:s').' api-request '.'null'."\r\n");
	$ret = array('code'=>10001,'desc'=>'��������(params)');
	$retArr = array('status'=>0,'result'=>$ret);
	fwrite ($fh,date('Y-m-d H:i:s').' api-response '.json_encode($retArr)."\r\n");
	fwrite ($fh,date('Y-m-d H:i:s').'*********************end***********************'."\r\n");
	fclose($fh);
	echo json_encode($retArr);
	exit();
	
}

$postData = $_POST;

fwrite ($fh,date('Y-m-d H:i:s').' api-request '.json_encode($postData)."\r\n");
if ( !isset($postData['wid']) || !isset($postData['ids'])  ){
	$ret = array('code'=>10001,'desc'=>'��������(params)');
	$retArr = array('status'=>0,'result'=>$ret);
	fwrite ($fh,date('Y-m-d H:i:s').' api-response '.json_encode($retArr)."\r\n");
	fwrite ($fh,date('Y-m-d H:i:s').'*********************end***********************'."\r\n");
	fclose($fh);
	echo json_encode($retArr);
	exit();
}

//wid 

$pdoMysql = new PDO("mysql:host=127.0.0.1;port=3306;dbname=vote","root","YChd2016!@#");
$pdoMysql->exec('set names utf8');

$strSql1   = 'SELECT * FROM `voter` where wid= '.$postData['wid'];
$resouce1  = $pdoMysql->query($strSql1);
$result1   = $resouce1->fetch(PDO::FETCH_ASSOC);

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
		$sql = "insert ignore into voter(wid, date, vote_ids) value ( ".$postData['wid'].",'".date("Y-m-d")."','')";
		$affected_rows = $pdoMysql->exec($sql);
		fwrite ($fh,date('Y-m-d H:i:s').' api-sql '.$sql."\r\n");
	}
}

if (count($voteIds) > 10) {
	$retArr = array('status'=>2,'result'=>"����ͶƱ�����Ѿ���10Ʊ��");
	echo json_encode($retArr);exit();
}

$ids = explode(",", $postData['ids']);

//ͶƱ�˺ͱ�ͶƱ����Ϣ(���������ˣ�)

foreach($ids as $id) {
	if(in_array($id, $voteIds)) {
		continue;
	}
	$sql = "update candidate set votes = votes+1 where id = '".$id."'";
	fwrite ($fh,date('Y-m-d H:i:s').' api-sql '.$sql."\r\n");
	$affected_rows = $pdoMysql->exec($sql);
}

//�ϲ�ids �� voteIds 
$voteIds = $voteIds+$ids;
$voteIds = implode(',',$voteIds);
//Ȼ�����
$sql = "update voter set date='".date("Y-m-d")."',vote_ids = '".$voteIds."' where wid = '".$postData['wid']."'";
fwrite ($fh,date('Y-m-d H:i:s').' api-sql '.$sql."\r\n");
$affected_rows = $pdoMysql->exec($sql);


$retArr = array('status'=>1);
fwrite ($fh,date('Y-m-d H:i:s').' api-response '.json_encode($retArr)."\r\n");
fwrite ($fh,date('Y-m-d H:i:s').'*********************end***********************'."\r\n");
fclose($fh);
echo json_encode($retArr);
exit();
