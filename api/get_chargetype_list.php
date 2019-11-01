<?php
	require("./inc/functions.php");	
	require("./inc/config.inc.php");	
	require("./inc/libvar.inc.php");
	//print_r($CHANNEL_CARD_CONFIG_ARR);
	echo array2json(
			array(
				"proto" => 23,
				"reqsuccess" => AM_REQUEST_SUCCESS,
				"list" => $CHANNEL_CARD_CONFIG_ARR,
			)	
		);
	if($memobj)$memobj->close();
		die;	
?>
