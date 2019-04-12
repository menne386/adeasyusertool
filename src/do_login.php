<?php

defined('__MAINAPP__') or die('nope');

$user = '{unknown}';


function parseSecret(string $in) {
	return hex2bin(preg_replace('/\s+/', '', $in));
}

function calculateOathHOTP($counter,$secret) {
	$digits = 8;
	$temp = '';
	while ($counter != 0) {
		$temp .= chr($counter & 0xff);
		$counter >>= 8;
	}
	$counter = substr(str_pad(strrev($temp), 8, "\0", STR_PAD_LEFT), 0, 8);
	$hash = hash_hmac('sha1', $counter, $secret, true);
	$offset = ord($hash[19]) & 0xf;
	$otp  = (ord($hash[$offset + 0]) & 0x7f) << 24;
	$otp |= (ord($hash[$offset + 1]) & 0xff) << 16;
	$otp |= (ord($hash[$offset + 2]) & 0xff) << 8;
	$otp |= (ord($hash[$offset + 3]) & 0xff);
	$otp %= pow(10, $digits);
	return str_pad($otp, $digits, '0', STR_PAD_LEFT);
}

/*for($a=0;$a<10;$a++) {
	addElement('div',$body,calculateOathHOTP($a,$domain_admin_oath_hotp_secret));
}*/

if(isset($_POST['keepalive'])) {
	header('Content-Type: application/json');
	$ret = array();
	$ret['time'] = $_SESSION['timestamp'];
	ldap_close($con);
	die(json_encode($ret));
}

//Timeout:



if (!isset($_SESSION['AUTH'])) {
	if(isset($_POST['username']) && isset($_POST['password'])) {
		$code = 0;
		$fullusername = $_POST['username']."@".$config['domain'];
		if($config['openldap']) {
			$fullusername="CN=".$_POST['username'].','.$config['ldap_base_dn_users'];
		}

		$ldapbind = ldap_bind($con, $fullusername, $_POST['password']);
		if($config['use_oath_hotp']) {
			$code |= 1;// Bit 1 is set (use_oath_hotp is enabled)
		}
		if(!$ldapbind) {
			$code |= 2; // Bit 2 is set (ldap bind failed)
		}
		$validOath = ($config['use_oath_hotp']) ? false : true;		
		
		if($ldapbind && $config['use_oath_hotp']) {
			require_once('secrets.php'); 
			$_SESSION['debug'][] = "Start Oath-HOTP";
			if(isset($secrets[$fullusername])) { 
				$token = $_POST['token'];
				$_SESSION['debug'][] = "Token:".$token;
				$secret = parseSecret($secrets[$fullusername]['secret']);
				$ctr = $secrets[$fullusername]['initial'];
				$ctrFile = md5($fullusername.$config['sessionname']);
				if(file_exists($ctrFile)) {
					//load counter from disk.
					$ctr = (int)(file_get_contents($ctrFile));
				}
				for($a=0;$a<(int)$config['oath_hotp_maxdeviation'];$a++) {
					
					$calculated = calculateOathHOTP($ctr,$secret);
					$_SESSION['debug'][] = "Calculated:".$secrets[$fullusername]['token'].$calculated;
					
					if($secrets[$fullusername]['token'].$calculated == $token) {
						$validOath = true;
						file_put_contents($ctrFile,$ctr);//save new counter offset:
						
						break;
					}
					
					$ctr ++;					
				}
			} else {
				$_SESSION['debug'][] = "No secret found for $fullusername";
				$code |= 4; // Bit 3 is set (no secret for user)
			}
			if(!$validOath) {
				$code |= 8; // Bit 4 is set (no valid oath could be calculated)
			}
		}
		
		if($ldapbind && $validOath) {
			$user = $fullusername;
			writelog(array('action'=>'login_success'));
			$data = serialize(array('u'=>$fullusername,'p'=>$_POST['password']));			
			$_SESSION['AUTH'] = cryptoCoder($data);
			unset($_SESSION['error']);
		} else {
			writelog(array('action'=>'login_failed','code'=>$code));
			$_SESSION['error'] = getLang('error:login_failed')." ($code)";
		}
		reload();
	}
	
	//Render login document:
	$document->loadHTMlFile("tmpl/loginform.html",LIBXML_NOWARNING|LIBXML_NOERROR);
	replaceDocumentMarkers($document);
	die($document->saveHTML());
} else {
	$data = unserialize(cryptoCoder($_SESSION['AUTH'],'d'));
	if(isset($_GET['logout'])) {
		$user = $data['u'];
		unset($data);
		writelog(array('action'=>'logout_success'));
		kill_and_reload();
	}
	if(isset($data['u']) && isset($data['p']) ) {
		$ldapbind = ldap_bind($con, $data['u'], $data['p']);
		if($ldapbind) {
			$user = $data['u'];
			unset($data);//Unset the encrypted auth block.
			
		} else {
			$_SESSION['error'] = getLang('error:login_failed')." (0)";
			kill_and_reload();
		}
	} else {
		$_SESSION['error'] = getLang('error:login_failed')." (0)";
		kill_and_reload();
	}
}
