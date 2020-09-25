<?php
// Copyright 2018 Menne Kamminga <kamminga DOT m AT gmail DOT com>. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

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
	'error:session_reuse'=>'Session re-use error! Possible hijack attempt!',
	'error:session_expired'=>'Session expired',
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
	'field:group:description'=>'Description',
	
	'btn:login'=>'Login',
	'btn:logout'=>'Logout',
	'btn:refresh'=>'Refresh',
	'btn:users'=>'Users',
	'btn:groups'=>'Roles & Rights',
	'btn:rights'=>'Users & Roles',
	'btn:create'=>'Create',
	'btn:logged_in_as'=>'Logged in as',
	'btn:search'=>'Search',
	'btn:audit'=>'Logbook',
	'txt:version'=>'Version',
	'txt:startscreen'=>'Startscreen',
	'txt:login'=>'Login',
	'txt:login_token'=>'Token',
	'txt:login_help'=>'Login with your username, password and 2nd factor token',
	'txt:login_help_notoken'=>'Login with your username, password',
	'txt:action'=>'Action',
	'txt:datetime'=>'Date/Time',
	'txt:sid'=>'SessionID',
	'txt:user'=>'User',
	'txt:dn'=>'Objectname',
	'txt:attr'=>'Attribute',
	'txt:value'=>'Value',
	'action:login_success'=>'User login',
	'action:modify'=>'Modify attribute',
	'action:logout_success'=>'User logout',
	'action:login_failed'=>'Failed login attempt!',
	'action:membership_del'=>'Remove user from group',
	'action:membership_add'=>'Add user to group',
	'action:new_user'=>'Add new user',
	'action:session_expired'=>'Session expired',
	'action:session_reuse'=>'Session breach!',
);

function getLang($marker) {
	global $lang;
	
	return isset($lang[$marker])? $lang[$marker]:$marker;
}
