<?php

defined('__MAINAPP__') or die('nope');

function writelog($data=array()) {
	global $config;
	global $user;
	$data['user'] = $user;
	$data['timestamp'] = time();
	$data['sid'] = session_id();

	$file = $config['log_dir'].date('Ym').'.log';
	file_put_contents($file,json_encode($data).",\n",FILE_APPEND) or die("failed to write to log: ".$file);
}


function getLog($myDate) {
	global $config;
	$file = $config['log_dir'].$myDate.'.log';

	return json_decode('{"entries":['.rtrim(rtrim(file_get_contents($file)),',').']}',true);	
}
