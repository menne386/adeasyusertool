<?php

defined('__MAINAPP__') or die('nope');

$fieldLimits = array(
	'samaccountname'=>array(
		'min'=>5,
		'max'=>40
	),
	'mail'=>array(
		'min'=>5,
		'max'=>60,
		'email'=>true
	),
	'displayname'=>array(
		'min'=>5,
		'max'=>60
	),
	'cn'=>array(
		'min'=>5,
		'max'=>60
	),
	'unicodepwd'=>array(
		'min'=>5,
		'max'=>40
	),
);

function validate_field($name,$value) {
	global $attributes;
	global $fieldLimits;
	if(isset($fieldLimits[$name])) {
		$limits = $fieldLimits[$name];
	
		if(strlen($value)<$limits['min']) {
			return getLang('error:to_short')." (".getLang('field:user:'.$name).")";
		}
		if(strlen($value)>$limits['max']) {
			return getLang('error:to_long')." (".getLang('field:user:'.$name).")";
		}
		if(isset($limits['email'])&&$limits['email']) {
			if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
				return getLang('error:invalid_email') ." (".getLang('field:user:'.$name).")";
				
			}
		}
	} 
	return true;
}

if(isset($_POST['log'])) {
	error_reporting(0);
	header('Content-Type: application/json');
	die(json_encode(getLog($_POST['log'])));
}

if(isset($_POST['dn'])) {
	error_reporting(0);
	header('Content-Type: application/json');
	$dn = $_POST['dn'];
	$id = $_POST['id'];
	$result = array(
		'status'=>'fail',
		'error'=>'',
		'id'=>$id,
	);
	if($dn=='users_new') {
		//$result['_POST'] = print_r($_POST,true);
		$CN = $_POST['attributes']['displayname'];
		$dn = 'CN='. ldap_escape($CN,'',LDAP_ESCAPE_DN).','.$config['ldap_base_dn_users'];
		
		$ldaprecord = $_POST['attributes'];
		
		$ldaprecord["useraccountcontrol"] = "66048";
		$ldaprecord["userprincipalname"] = $ldaprecord['samaccountname']."@".$config['fqdn'];
		$ldaprecord['objectclass'][0] = "top";
		$ldaprecord['objectclass'][1] = "person";
		$ldaprecord['objectclass'][1] = "organizationalPerson";
		$ldaprecord['objectclass'][2] = "user";
		$ldaprecord['cn'] = $CN;
		$ldaprecord["unicodepwd"] = iconv("UTF-8", "UTF-16LE", '"' .$_POST['attributes']['unicodepwd']. '"');
		
		//Make sure all required field are filled:
		foreach($fieldLimits as $key=>$v) {
			if(!isset($ldaprecord[$key])) {
				$ldaprecord[$key] = "";
			}
		}
		//Valideer alle velden:
		foreach($ldaprecord as $key=>$value) {
			$r = validate_field($key,$value);
			if($r!==true) {
				$result['error'] = $r;
				die(json_encode($result));
			}
		}

		
		if(ldap_add($con, $dn, $ldaprecord)) {
			$ldaprecord['unicodepwd'] = '"******"';
			writelog(array('action'=>'new_user',$ldaprecord));
			$result['status'] = 'ok';
		} else {
			$result['error'] = getLdapError();
		}	
		die(json_encode($result));
	}
	$attr = $_POST['attribute'];
	$filterfn = 'writefilter_'.$attr;
	
	$isUserMod = in_array($dn,array_keys($ad_users)) && in_array($attr,array_keys($attributes));
	$isMembershipUpdate = in_array($dn,array_keys($ad_users)) && in_array($attr,array_keys($ad_groups));
	//$isGroupMod = in_array($dn,array_keys($ad_groups)) && in_array($attr,array_keys($attributes_g));
	
	
	if($isMembershipUpdate) {
		$entry = array(
			'member'=> $dn
		);
		if(ldap_mod_add ( $con , $attr , $entry)) {
			$result['status'] = "ok";
			writelog(array('action'=>'membership_add','group'=>$attr,'member'=>$dn));
		} else {
			if(ldap_errno($con)==68) {
				//Error 68 == "already exists"
				if(ldap_mod_del ( $con , $attr , $entry)) {
					$result['status'] = "ok";
					writelog(array('action'=>'membership_del','group'=>$attr,'member'=>$dn));
				} else {
					$result['error'] = getLdapError();
					ldap_get_option($con,LDAP_OPT_DIAGNOSTIC_MESSAGE,$result['diagnostic']);
					$result['data'] = $entry;
				}
			} else {
				$result['error'] = getLdapError();
				ldap_get_option($con,LDAP_OPT_DIAGNOSTIC_MESSAGE,$result['diagnostic']);
				$result['data'] = $entry;
			}
		}
	} else if($isUserMod) {
		
		$entry = array(
			$attr=> array(
				0=>$_POST['value']
			)
		);
		$modify = true;
		if(function_exists($filterfn)) {
			$modify = $filterfn($dn,$entry);
		}
		if($modify) {
			$r = validate_field($attr,$_POST['value']);
			if($r!==true) {
				$result['error'] = $r;
			} else {
				$rr = null;
				if(strlen(trim($_POST['value']))==0 && $attr!='useraccountcontrol') {
					//Delete property:
					$entry = array($attr=>array());
					$rr = ldap_mod_del($con,$dn,$entry);
				} else {
					$rr = ldap_modify($con,$dn,$entry);
				}
				if($rr) {
					if($attr=='unicodepwd') {
						$_POST['value'] = '"*****"';
					}
					writelog(array('action'=>'modify','dn'=>$dn,'attribute'=>$attr,'value'=>$_POST['value']));
					$result['status'] = "ok";
				} else {
					$result['error'] = getLdapError();
					ldap_get_option($con,LDAP_OPT_DIAGNOSTIC_MESSAGE,$result['diagnostic']);
					$result['data'] = $entry;
				}
			}
		} else {
			$result['error'] = getLang('error:filter');
		}
	}
	
	die(json_encode($result));
}

