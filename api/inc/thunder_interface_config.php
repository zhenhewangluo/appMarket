<?php
/**
 *  接口访问权限配置
 *  @Author      Li Xiaan
 *  @CreateTime  2010-05-03
 *  @Update
 */
 $INTERFACE_CONFIG_ARR = array();
 
 //Default Auth Config
 $INTERFACE_CONFIG_ARR[0] = array(
	"AUTH_LEVEL"      => AM_AUTH_NEED_SESSION,
	"LOG_ALLOW_MIN_LEVEL" => 'I',
 ); 
 //===================================================
 // The following interfaces need session support
 //===================================================
 $INTERFACE_CONFIG_ARR[2] = $INTERFACE_CONFIG_ARR[0];
 $INTERFACE_CONFIG_ARR[3] = $INTERFACE_CONFIG_ARR[0];
 $INTERFACE_CONFIG_ARR[4] = $INTERFACE_CONFIG_ARR[0];
 $INTERFACE_CONFIG_ARR[5] = $INTERFACE_CONFIG_ARR[0];
 $INTERFACE_CONFIG_ARR[6] = $INTERFACE_CONFIG_ARR[0];
 $INTERFACE_CONFIG_ARR[7] = $INTERFACE_CONFIG_ARR[0];
 $INTERFACE_CONFIG_ARR[8] = $INTERFACE_CONFIG_ARR[0];
 $INTERFACE_CONFIG_ARR[9] = $INTERFACE_CONFIG_ARR[0];
 $INTERFACE_CONFIG_ARR[11] = $INTERFACE_CONFIG_ARR[0];
 $INTERFACE_CONFIG_ARR[12] = $INTERFACE_CONFIG_ARR[0];

 //===================================================
 // The following interfaces don't need session
 //===================================================
 //AnonymousLogin 
 $INTERFACE_CONFIG_ARR[1] = array(
	"AUTH_LEVEL"      => AM_AUTH_FOR_ALL,
	"LOG_ALLOW_MIN_LEVEL" => 'I',
 ); 
 //Send Pwd
 $INTERFACE_CONFIG_ARR[24] =  $INTERFACE_CONFIG_ARR[1];
 //Register
 $INTERFACE_CONFIG_ARR[22] = $INTERFACE_CONFIG_ARR[1];
 //Logon 	
 $INTERFACE_CONFIG_ARR[20] = $INTERFACE_CONFIG_ARR[1];
 //Logout
 $INTERFACE_CONFIG_ARR[21] = $INTERFACE_CONFIG_ARR[1];
 //Upload View Log
 $INTERFACE_CONFIG_ARR[25] = $INTERFACE_CONFIG_ARR[1];

 //=================================================================
 // The following interfaces need user login, be very careful.
 //=================================================================
 $INTERFACE_CONFIG_ARR[13] = array(
	"AUTH_LEVEL"      => AM_AUTH_NEED_LOGON,
	"LOG_ALLOW_MIN_LEVEL" => 'I',
 );
 $INTERFACE_CONFIG_ARR[14] = $INTERFACE_CONFIG_ARR[13];
 $INTERFACE_CONFIG_ARR[15] = $INTERFACE_CONFIG_ARR[13];
 $INTERFACE_CONFIG_ARR[16] = $INTERFACE_CONFIG_ARR[13];
 $INTERFACE_CONFIG_ARR[23] = $INTERFACE_CONFIG_ARR[13];
 $INTERFACE_CONFIG_ARR[26] = $INTERFACE_CONFIG_ARR[13];
 $INTERFACE_CONFIG_ARR[27] = $INTERFACE_CONFIG_ARR[13];

 ?>
