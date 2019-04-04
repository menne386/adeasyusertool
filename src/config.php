<?php
/**
* @author: Menne Kamminga <mkamminga@bevolkingsonderzoeknoord.nl>
* 
* Here we define all global configuration
*/
defined('__MAINAPP__') or die('nope');

$config = array();

$config['sessionname'] = "00112233445566778899";//Session name, should be a random string, this is used as cookie name for the php session
$config['domain'] = "SHORT_DOMAIN_NAME"; //Domain part of user name, when logging in, this is appended to the username to perform ldap_bind
$config['fqdn'] = "DOMAIN_NAME.COM"; //FQDN is required when creating new users for the domain.
$config['server'] = "AD_SERVER.DOMAIN_NAME.COM"; //The full server DNS name
$config['ldap_base_dn_users'] = 'OU=SOME_OU_WITH_USERS,OU=SOME_OU,DC=DOMAIN_NAME,DC=COM';//The base OU we are searching in for users, new users are created here:
$config['ldap_base_dn_groups'] = 'OU=SOME_OU_WITH_USERS,OU=SOME_OU,DC=DOMAIN_NAME,DC=COM';//The base OU we are searching in for groups
$config['ca_cert'] = "c:\\xampp\\ca.pem"; //The certificate of the domain Certificate Authority (required for secure LDAPS connection)
$config['secret_key'] = hash( 'sha256', 'some very secret password' );//The secret key is used to protect session data.
$config['encrypt_method'] = "AES-256-CBC";//The encryption method used to protect session data
$config['page_title'] = "AD User Tool"; //The <title> attribute in the page header.
$config['use_oath_hotp'] = false; //Enable 2e factor authentication based on oath hotp
$config['oath_hotp_maxdeviation'] = 100; //Maximum number that the oath can deviate.
$config['debug'] = false;
$config['version'] = '0.5.1';
//$config['language'] = 'nl'; //Language code (2 letters)

//Include the default translation
require_once('lang/default.php');

//Include configured lang:
if(isset($config['language'])) {
	require_once('lang/'.$config['language'].'.php');
	foreach($lang_override as $k=>$v) {
		$lang[$k]=$v;
	}
}


