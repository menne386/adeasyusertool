<?php

defined('__MAINAPP__') or die('nope');

$lang = array(
	'datetime'=>'d-M-y H:i:s T',
	'ldap:19'=>'Value violates server constraints',
	'ldap:53'=>'Value does not conform to policy',
	'ldap:68'=>'This record already exists',
	'ldap:50'=>'Access to value denied',
	'error:to_short'=>'Value is to short',
	'error:to_long'=>'Value is to long',
	'error:invalid_email'=>'Value is not a valid email adres',
	'error:filter'=>'Filter denied access to value',
	'error:login_failed'=>'Unable to log in with these credentials',
	'field:cn'=>'Displayname',
	
	'field:password'=>'Password',
	'field:password_repeat'=>'Repeat',
	'field:user:samaccountname'=>'Username',
	'field:user:displayname'=>'Displayname',
	'field:user:givenname'=>'Givenname',
	'field:user:cn'=>'Displayname',
	'field:user:initials'=>'Initials',
	'field:user:sn'=>'Surname',
	'field:user:mail'=>'Email',
	'field:user:department'=>'Department',
	'field:user:title'=>'Functiontitle',
	'field:user:unicodepwd'=>'Password',
	'field:user:useraccountcontrol'=>'Active',

	'field:group:samaccountname'=>'Groupname',
	'field:group:member'=>'Members',
	
	'btn:login'=>'Login',
	'btn:logout'=>'Logout',
	'btn:refresh'=>'Refresh',
	'btn:users'=>'Users',
	'btn:groups'=>'Groups',
	'btn:rights'=>'Matrix',
	'btn:create'=>'Create',
	'btn:logged_in_as'=>'Logged in as',
	'btn:search'=>'Search',
	'txt:version'=>'Version',
	'txt:startscreen'=>'Startscreen',
	'txt:login'=>'Login',
	'txt:login_token'=>'Token',
	'txt:login_help'=>'Login with your username, password and token (where applicable)',
);

function getLang($marker) {
	global $lang;
	
	return isset($lang[$marker])? $lang[$marker]:$marker;
}
