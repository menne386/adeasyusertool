<?php

defined('__MAINAPP__') or die('nope');

session_name($config['sessionname']);
session_start();

//Session timeout:
if(isset($_SESSION['timestamp'])) {
	if(time()>$_SESSION['timestamp'] + 60) {
		unset($_SESSION['AUTH']);
		unset($_SESSION['_i']);
		$_SESSION['error'] = "Sessie verlopen";
	}
		
}

//Session bound to user:
$userid = array(
	'ip'=>$_SERVER['REMOTE_ADDR'],
	'xfor'=>isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:'empty',
	'xfor2'=>isset($_SERVER['X_FORWARDED_FOR'])?$_SERVER['X_FORWARDED_FOR']:'empty',
);
$userid = hash('sha256',serialize($userid));

if(isset($_SESSION['userid']) && $_SESSION['userid'] != $userid) {
	unset($_SESSION['AUTH']);
	unset($_SESSION['_i']);
	$_SESSION['error'] = "Sessie hergebruik fout";
}
$_SESSION['userid'] = $userid;

$secret_iv = isset($_SESSION['_i'])?$_SESSION['_i']:genNewIv();
$_SESSION['_i'] = $secret_iv;
$_SESSION['timestamp'] = time();



$con = null;

if($config['debug']) {
	ldap_set_option($con, LDAP_OPT_DEBUG_LEVEL, 7);
}

if(isset($config['ca_cert'])) {
	ldap_set_option($con, LDAP_OPT_X_TLS_CRLCHECK, 0)or die("failed to set CRLCHECK");
	ldap_set_option($con, LDAP_OPT_X_TLS_REQUIRE_CERT, 0)or die("failed to set REQUIRE_CERT");
	ldap_set_option($con, LDAP_OPT_X_TLS_CACERTFILE, $config['ca_cert'])or die("failed to set CACERTFILE");
}

$con=ldap_connect($config['server']) or die("Could not connect to $server"); 
ldap_set_option($con, LDAP_OPT_PROTOCOL_VERSION, 3) or die("failed to set protocol version");
ldap_set_option($con, LDAP_OPT_REFERRALS, 0)or die("failed to set referrals");


if(!ldap_start_tls($con)) {
	$num = 0;

	$msg = "";
	ldap_get_option($con,LDAP_OPT_ERROR_NUMBER,$num);
	ldap_get_option($con,LDAP_OPT_DIAGNOSTIC_MESSAGE,$msg);
	print_r(getenv());
	die('start_tls: '.ldap_error($con)."N".$num." M:".$msg." C:".$ca_cert);
}


