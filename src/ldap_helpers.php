<?php
# Copyright 2018 Menne Kamminga <kamminga DOT m AT gmail DOT com>. All rights reserved.
# Use of this source code is governed by a BSD-style
# license that can be found in the LICENSE file.

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

function getLdapMemberships($base_dn,$search_filter,&$groups) {
	global $con;
	$resultArr = array();
	$result = ldap_search($con, $base_dn, $search_filter, array('samaccountname','memberof','displayname','title'));
	if (FALSE !== $result){
		$entries = ldap_get_entries($con, $result);
		//print_r($entries);
		for ($x=0; $x<$entries['count']; $x++){
			$resultArr[$entries[$x]['dn']] = array(
				"dn"=>$entries[$x]['dn'],
				"samaccountname"=>$entries[$x]['samaccountname'][0],
				"displayname"=>isset($entries[$x]['displayname']) ? $entries[$x]['displayname'][0]: $entries[$x]['dn'],
				"title"=>isset($entries[$x]['title']) ? $entries[$x]['title'][0]: "",
			);
			foreach($groups as $dn=>$values) {
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
