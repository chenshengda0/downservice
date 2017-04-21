<?php

define('IPOne', "127.0.0.1");
define('IPTwo', "120.25.104.229");
define('IPThree', "120.25.104.229");
function postReturn($return){
	$return = base64_encode($return);
	echo $return;
	exit();
}

function GetIP($type=0){
	if(!empty($_SERVER["HTTP_CLIENT_IP"])) {
		$cip = $_SERVER["HTTP_CLIENT_IP"];
	} else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	} else if(!empty($_SERVER["REMOTE_ADDR"])) {
		$cip = $_SERVER["REMOTE_ADDR"];
	} else {
		$cip = "";
	}

	preg_match("/[\d\.]{7,15}/", $cip, $cips);
	$cip = $cips[0] ? $cips[0] : 'unknown';
	unset($cips);
	if ($type==1) $cip = myip2long($cip);
	return $cip;
}

function subpackage($urldata){
	$p = base64_decode($urldata['p']);
	$a = base64_decode($urldata['a']);
	$o = base64_decode($urldata['o']);
	$ip = GetIP();
	if ($ip != IPOne && $ip != IPTwo && $ip != IPThree){
		$return = '-6'.$ip;  //拒绝访问
		return $return;
	}
	
	if(empty($p) || empty($a)){
		$return = -3;  //请求数据为空
		return $return;
	}
	
	$opt = md5(md5($p.$a).'resub');
	
	if($o != $opt){
	    $return = -4;  //验证错误
	    return $return;
	}

	$pinyin = isset($p) ? $p :'';
	$agentgame = isset($a) ? $a :'';

	$url = dirname(__FILE__);
	$url = $url.DIRECTORY_SEPARATOR."sdkgame".DIRECTORY_SEPARATOR;

	$sourfile = $url.$pinyin."/".$pinyin.".apk";
	if(!file_exists($sourfile)){
	    $res=mkdir($url.$pinyin,0777,true);
	    if ($pinyin == $agentgame){
	        return 1;
	    }
	    $return = -5;   //游戏原包不存在
	    return $return;
	}
	
	chmod($url, 0777);
	chmod($sourfile, 0777);

	$filename= $agentgame.".apk";
	
	$newfile = $url.$pinyin."/".$filename;
	if(file_exists($newfile)){
	    $return = 2; //已分包
	    return $return;
	}
	
	$return = 0;
	
	if (!copy($sourfile, $newfile)) {
		$return = -1;//无法创建文件,打包失败
		return $return;
	}


	fopen($channelfile, "w");
	
	$channelname = "META-INF/gamechannel";
	
	$zip = new ZipArchive;
	if ($zip->open($newfile) === TRUE) {
	    $zip->addFromString($channelname, json_encode(array('agentgame'=>$agentgame)));
		$zip->close();
		$return = 1;
	} else {
		$return = -2;
	}

	return $return;
}
?>
