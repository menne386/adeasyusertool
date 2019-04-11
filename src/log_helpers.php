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
	global $ad_users;
	global $ad_groups;
	$file = $config['log_dir'].(int)$myDate.'.log';

	$A = json_decode('{"entries":['.rtrim(rtrim(file_get_contents($file)),',').']}',true);	
	$A['entries'] = array_reverse($A['entries']);
	foreach($A['entries'] as $i=>$row) {
		foreach($row as $attr=>$val) {
			if($attr==='timestamp') {
				$A['entries'][$i][$attr] = date(getLang('datetime'),$val);
			}
			if(($attr === 'dn' || $attr === 'member')&& isset($ad_users[$val])) {
				$A['entries'][$i][$attr] = $ad_users[$val]['samaccountname'].'('.$ad_users[$val]['displayname'].')';
			}				
			if(($attr === 'dn' || $attr==='group') && isset($ad_groups[$val])) {
				$A['entries'][$i][$attr] = $ad_groups[$val]['samaccountname'];
			}				
		}
	}
	return $A;
}
