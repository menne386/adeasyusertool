<?php

defined('__MAINAPP__') or die('nope');

function getLdapEntries($base_dn,$search_filter,$attributes) {
	global $con;
	$resultArr = array();
	$result = ldap_search($con, $base_dn, $search_filter, array_keys($attributes));
	if (FALSE !== $result){
		$entries = ldap_get_entries($con, $result);
		//print_r($entries);
		for ($x=0; $x<$entries['count']; $x++){
			$resultArr[$entries[$x]['dn']] = array(
				"dn"=>$entries[$x]['dn']
			);
			foreach($attributes as $att=>$att_tr) {
				$cnt = 1;
				if(isset($entries[$x][$att]['count'])) {
					$cnt = $entries[$x][$att]['count'];
				}
				for($a=0;$a<$cnt;$a++) {
					if(isset($resultArr[$entries[$x]['dn']][$att])) {
						$resultArr[$entries[$x]['dn']][$att].="{||}";
					} else {
						$resultArr[$entries[$x]['dn']][$att]="";
					}
					$resultArr[$entries[$x]['dn']][$att] .= isset($entries[$x][$att]) ? (isset($entries[$x][$att][$a])?$entries[$x][$att][$a]:$entries[$x][$att]): "";
				}
			}
		}
	}
	return $resultArr;
}

function getLdapMemberships($base_dn,$search_filter) {
	global $con,$ad_groups;
	$resultArr = array();
	$result = ldap_search($con, $base_dn, $search_filter, array('samaccountname','memberof','displayname'));
	if (FALSE !== $result){
		$entries = ldap_get_entries($con, $result);
		//print_r($entries);
		for ($x=0; $x<$entries['count']; $x++){
			$resultArr[$entries[$x]['dn']] = array(
				"dn"=>$entries[$x]['dn'],
				"samaccountname"=>$entries[$x]['samaccountname'][0],
				"displayname"=>$entries[$x]['displayname'][0]
			);
			foreach($ad_groups as $dn=>$values) {
				$resultArr[$entries[$x]['dn']][$dn] = 0;
			}
			if(isset($entries[$x]['memberof'])) {
				$cnt = $entries[$x]['memberof']['count'];
				for($a=0;$a<$cnt;$a++) {
					$resultArr[$entries[$x]['dn']][$entries[$x]['memberof'][$a]] = 1;
				}
			}
		}
	}
	return $resultArr;
}

function getLdapError() {
	global $con;
	global $lang;
	$nr = ldap_errno($con);
	$str = ldap_error($con);
	if(isset($lang['ldap:'.$nr])) {
		return $lang['ldap:'.$nr];
	}
	return $nr.': '.$str; 
}
