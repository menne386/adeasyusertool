<?php
/**
* @author: Menne Kamminga <mkamminga@bevolkingsonderzoeknoord.nl>
* 
* Here we define all global configuration
*
* DO NOT CHANGE CONFIGURATION DEFAULTS HERE! INSTEAD edit "config.php" and add lines you want to override there!
* To NOT commit your configuration:
*   git update-index --assume-unchanged config.php
*/
defined('__MAINAPP__') or die('nope');

$config = array();

//Language code (2 letters) (Unset by default!)
$config['language'] = false; //example: 'nl'

//The dir used to store audit logs: (end in directory seperator.)
$config['log_dir'] = "./auditlogs/";

//Session name, should be a random string, this is used as cookie name for the php session
$config['sessionname'] = "a00112233445566778899";

//Domain part of user name, when logging in, this is appended to the username to perform ldap_bind
$config['domain'] = "SHORT_DOMAIN_NAME"; 

//FQDN is required when creating new users for the domain.
$config['fqdn'] = "DOMAIN_NAME.COM"; 

//The full server DNS name
$config['server'] = "AD_SERVER.DOMAIN_NAME.COM"; 

//Use startTLS when connecting to ldap?
$config['starttls'] = true;

//The certificate of the domain Certificate Authority (required for secure LDAPS connection on some servers) (Unset by default!)
$config['ca_cert'] = false; //example: "c:\\xampp\\ca.pem" 

//If this is true: use openldap bind style instead of active directory style.
$config['openldap'] = false;

//Maximum number that the oath can deviate.
$config['oath_hotp_maxdeviation'] = 100; 

//The secret key is used to protect session data.
$config['secret_key'] = hash( 'sha256', 'please change me to a secret!' );

//The encryption method used to protect session data
$config['encrypt_method'] = "AES-256-CBC";

//This is used as a logo on the login page:
$config['logo'] = "some_logo_file.png"; 

//This is the colors used in styling:
$config['style'] = array(
	'bg:color:low'=>'#612d83',
	'bg:color:mid'=>'#9772c4',
	'bg:color:high'=>'#d8beff',
	'bg:color:button'=>'#97a300',
	'border:color:button'=>'#828d00',
);

//The <title> attribute in the page header.
$config['page_title'] = "AD User Tool"; 

//Enable 2e factor authentication based on oath hotp
$config['use_oath_hotp'] = false; 

//Enable debugging:
$config['debug'] = false;


//The base OU we are searching in for users, new users are created here:
$config['ldap_base_dn_users'] = 'OU=SOME_OU_WITH_USERS,OU=SOME_OU,DC=DOMAIN_NAME,DC=COM';

//The base OU we are searching in for role groups
$config['ldap_base_dn_rolegroups'] = 'OU=SOME_OU_WITH_USERS,OU=SOME_OU,DC=DOMAIN_NAME,DC=COM';

//The base OU we are searching in for right groups
$config['ldap_base_dn_rightgroups'] = 'OU=SOME_OU_WITH_USERS,OU=SOME_OU,DC=DOMAIN_NAME,DC=COM';

//The filter that is used for user searching
$config['ldap_search_filter_users'] = '(&(objectCategory=person)(samaccountname=*))';

//The filter that is used for group searching
$config['ldap_search_filter_rolegroups'] = '(&(objectCategory=group)(samaccountname=*))';

//The filter that is used for group searching
$config['ldap_search_filter_rightgroups'] = '(&(objectCategory=group)(samaccountname=*))';

//The filter that is used for rights matrix searching
$config['ldap_search_filter_rights'] = '(&(objectCategory=person)(samaccountname=*)(mail=*)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))';


//What attributes are available for display/edit for users:
$config['ldap_attributes_users'] = array(
	'samaccountname'=>"field:user:samaccountname",
	'displayname'=>"field:user:displayname",
	'givenname'=>"field:user:givenname",
	'initials'=>"field:user:initials",
	'sn'=>"field:user:sn",
	'mail'=>"field:user:mail",
	'department'=>"field:user:department",
	'title'=>"field:user:title",
	'unicodepwd'=>"field:user:unicodepwd",
	'useraccountcontrol'=>"field:user:useraccountcontrol",
);

//What attributes are available for display/edit for groups:
$config['ldap_attributes_groups'] = array(
	'samaccountname'=>"field:group:samaccountname",
	'member'=>'field:group:member',
);

//What attributes are available for display/edit for rights: (added to these are all the groups)
$config['ldap_attributes_rights'] = array(
	//'samaccountname'=>"field:user:samaccountname",
	'displayname'=>"field:user:displayname",
	'title'=>"field:user:title",
);



//The version number displayed:
$config['version'] = '0.5.1';

//Override configuration:
require_once('config.php');

//Include the default translation
require_once('lang/default.php');

//Include configured lang:
if(isset($config['language']) && $config['language']!==false) {
	require_once('lang/'.$config['language'].'.php');
	foreach($lang_override as $k=>$v) {
		$lang[$k]=$v;
	}
}


