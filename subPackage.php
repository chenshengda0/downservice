<?php
	include "downFunc.php";
	
	$urldata = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : '';
	$urldata = get_object_vars(json_decode($urldata));
	$cnt = 0;
	while(1){
		$return = subpackage($urldata);
		if(-2 != $return || 3 == $cnt){	
			break;	
		} 		
		$cnt++;
	}
		
	echo base64_encode($return);
?>