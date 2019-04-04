<?php

defined('__MAINAPP__') or die('nope');

$tabIndex = 1;

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
	global $tabIndex;
	$attribs = array(
		'type'=>$edit?'checkbox':'button',
		'value'=>$edit?$value:getLang('btn:create'),
		'__dn'=>$dn,
		'class'=>$edit?'editable':'createnew btn btn-primary',
		'tabindex'=> $tabIndex,
		//'class'=>$edit?'editable':'createnew',
		'name'=>$att
	);
	if(((int)$value & 2) ==0 && $edit) {
		$attribs['checked'] = true;
	}
	
	$input = addElement('input',$node,$value,$attribs);	
	
	
	
	++$tabIndex;
}


function displayfilter_samaccountname($dn,$value, &$node,$att,bool $edit,$attributes) {
	$input = false;
	if($edit) {
		$input = addElement('span',$node,$value,array());
	} else {
		$input = displayfilter_default($dn,$value,$node,$att,$edit,$attributes);
		//$input->setAttribute("autofocus",true);
	}
	return $input;
}

function displayfilter_unicodepwd($dn,$value, &$node,$att,bool $edit,$attributes) {
	global $tabIndex;
	$input = addElement('input',$node,$value,array(
		'type'=>'password',
		'placeholder'=>getLang('field:password'),
		'__dn'=>$dn,
		'tabindex'=> $tabIndex,
		'name'=>$att,
	));	
	++$tabIndex;
	$input = addElement('input',$node,$value,array(
		'type'=>'password',
		'placeholder'=>getLang('field:password_repeat'),
		'__dn'=>$dn,
		'name'=>$att,
		'class'=>$edit?'editable':'checkpw',
		'tabindex'=> $tabIndex,		
		
	));	
	++$tabIndex;
	return $input;
}

//CN=Jan de vries,OU=BOMWSE,DC=bomwse,DC=local{||}CN=Bert Visser,OU=BOMWSE,DC=bomwse,DC=local
function displayfilter_member($dn,$value, &$node,$att,bool $edit,$attributes) {
	global $tabIndex;
	global $ad_users;
	$members_dn = explode('{||}',$value);
	$select = addElement('select',$node,'',array('tabindex'=> $tabIndex,'class'=>'groupmembers'));
	foreach($members_dn as $dns) {
		$name = $dns;
		if(isset($ad_users[$name])) {
			$name = $ad_users[$name]['displayname'];
		}
		$input = addElement('option',$select,$name);
	}
	++$tabIndex;
	return $select;
}

function displayfilter_membership($dn,$value, &$node,$att,bool $edit,$attributes) {
	global $tabIndex;
	$attribs = array(
		'type'=>'checkbox',
		'value'=>$value,
		'__dn'=>$dn,
		'class'=>$edit?'editable':'',
		'tabindex'=> $tabIndex,
		'title'=>getLang($attributes[$att]),
		'name'=>$att
	);
	if($value==1) {
		$attribs['checked'] = true;
	}
	++$tabIndex;
	//$hidden = addElement('input',$node,'',array('type'=>'hidden','value'=>$value));	
	$input = addElement('input',$node,$value,$attribs);	
	
}

function displayfilter_default($dn,$value, &$node,$att,bool $edit,$attributes) {
	global $tabIndex;
	$input = addElement('input',$node,$value,array(
		'type'=>'text',
		'value'=>$value,
		'__dn'=>$dn,
		'name'=>$att,
		'placeholder'=>getLang($attributes[$att]),
		'tabindex'=> $tabIndex,
		'class'=>$edit?'editable':'',
	));
	++$tabIndex;
	return $input;
}
