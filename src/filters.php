<?php
# Copyright 2018 Menne Kamminga <kamminga DOT m AT gmail DOT com>. All rights reserved.
# Use of this source code is governed by a BSD-style
# license that can be found in the LICENSE file.

defined('__MAINAPP__') or die('nope');


function writefilter_samaccountname($dn, &$entry) {
	return false;
}

function writefilter_unicodepwd($dn, &$entry) {
	$pw = $entry['unicodepwd'][0];
	$entry = array("unicodePwd"=>iconv("UTF-8", "UTF-16LE", '"' .$pw. '"'));
	return true;
}


function writefilter_useraccountcontrol($dn, &$entry) {
	global $ad_users;
	$value = $ad_users[$dn]['useraccountcontrol'];
	
	if(((int)$value & 2) ==0) {
		$value |= 2;
	} else {
		$value &= ~2;
	}
	$entry['useraccountcontrol'][0] = $value;
	return true;
}

function displayfilter_useraccountcontrol($dn,$value, &$node,$att,bool $edit,$attributes) {
	if($edit) {
		$input = addElement('div',$node,($value & 2)==0 ? "✔":"",array('class'=>'editable','value'=>1));	
	} else {
		$attribs = array(
			'type'=>'button',
			'value'=>getLang('btn:create'),
			'class'=>'createnew btn btn-primary',
			'name'=>$att
		);
		$input = addElement('input',$node,$value,$attribs);	
	}

	return $input;
}


function displayfilter_samaccountname($dn,$value, &$node,$att,bool $edit,$attributes) {
	$input = false;
	if($edit) {
		$input = addElement('span',$node,$value,array('class'=>'detail_bt'));
	} else {
		$input = displayfilter_default($dn,$value,$node,$att,$edit,$attributes);
	}
	return $input;
}

function displayfilter_unicodepwd($dn,$value, &$node,$att,bool $edit,$attributes) {
	$input = addElement('input',$node,$value,array(
		'type'=>'password',
		'placeholder'=>getLang('field:password'),
		'name'=>$att,
	));	
	$input = addElement('input',$node,$value,array(
		'type'=>'password',
		'placeholder'=>getLang('field:password_repeat'),
		'name'=>$att,
		'class'=>$edit?'editable':'checkpw',
		
	));	
	return $input;
}

function displayfilter_member($dn,$value, &$node,$att,bool $edit,$attributes) {
	global $ad_users;
	$members_dn = explode('{||}',$value);
	$select = addElement('select',$node,'',array('class'=>'groupmembers'));
	foreach($members_dn as $dns) {
		$name = $dns;
		if(isset($ad_users[$name])) {
			$name = $ad_users[$name]['displayname'];
		}
		$input = addElement('option',$select,$name);
	}
	return $select;
}

function displayfilter_membership($dn,$value, &$node,$att,bool $edit,$attributes) {
	$input = addElement('div',$node,$value ? "✔":"",array('class'=>'editable'));	
	return $input;	
}

function displayfilter_default($dn,$value, &$node,$att,bool $edit,$attributes) {
	$input = addElement('input',$node,$value,array(
		'type'=>'text',
		'value'=>$value,
		'name'=>$att,
		'placeholder'=>getLang($attributes[$att]),
		'class'=>$edit?'editable':'',
	));
	return $input;
}
