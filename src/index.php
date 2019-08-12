<?php
/**
* @author: Menne Kamminga <mkamminga@bevolkingsonderzoeknoord.nl>
* 
* This module defines the main application.
* @todo: session should by bound to IP (X-FORWARDED-FOR) 
* @todo: session should time out.
* @todo: OATH-HOTP (secret + counter in writeable PHP files?)
*/

define('__MAINAPP__',1);

require_once('defaultconfig.php'); //Load configuration variables (defaultconfig.php in turn includes config.php)

require_once('do_resources.php');

require_once('log_helpers.php');

require_once('dom_helpers.php'); //Dom functions:
require_once('session_helpers.php'); //reload/kill_and_reload functions:
require_once('crypto_helpers.php'); //cryptoCoder functions:

require_once('do_initialize.php'); //initialize the ldap connection and session


//Create the document to output to the browser:
$document = DOMImplementation::createDocument(null, 'html', DOMImplementation::createDocumentType("html","-//W3C//DTD XHTML 1.0 Transitional//EN","http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"));
$document->formatOutput = true;


//Execute login logic: (displays a logon screen if no valid session found);
require_once('do_login.php');


//If we get here, we are authenticated properly, LDAP is set up.

$msg = "";

$ad_users = array();
$ad_groups = array();

$search_filter = $config['ldap_search_filter_users'];
$search_filter_rightgroups = $config['ldap_search_filter_rightgroups'];
$search_filter_rolegroups = $config['ldap_search_filter_rolegroups'];

$attributes = $config['ldap_attributes_users'];
$attributes_g = $config['ldap_attributes_groups'];
$attributes_role_to_right = $config['ldap_attributes_groups'];
$attributes_user_to_role = $config['ldap_attributes_rights'];

require_once('filters.php');//Functions that are used to filter/check ldap data before display or before write.

require_once('ldap_helpers.php'); // getLdapEntries & getLdapMemberships functions;

$ad_users = getLdapEntries($config['ldap_base_dn_users'],$search_filter,$attributes);
$ad_rolegroups = getLdapEntries($config['ldap_base_dn_rolegroups'],$search_filter_rolegroups,$attributes_g);
$ad_rightgroups = getLdapEntries($config['ldap_base_dn_rightgroups'],$search_filter_rightgroups,$attributes_g);

foreach($ad_rolegroups as $dn=>$values) {	$attributes_user_to_role[$dn] = $values['samaccountname'];}
foreach($ad_rightgroups as $dn=>$values) {	$attributes_role_to_right[$dn] = $values['samaccountname'];}

$ad_roles_to_rights = getLdapMemberships($config['ldap_base_dn_rolegroups'],$config['ldap_search_filter_rolegroups'],$ad_rightgroups);
$ad_users_to_roles = getLdapMemberships($config['ldap_base_dn_users'],$config['ldap_search_filter_rights'],$ad_rolegroups);


require_once('do_processing.php'); //This does processing of posted values



//Load template:
loadDocumentTemplate($document,'tmpl/index.html');
//Create a temporary div to hold elements
$headerdiv = $document->createElement('div');

//Fill array with markers:
$additionalMarkers = array();
$additionalMarkers['logoutbutton'] = addElement('a',$headerdiv,getLang('btn:logout'),array('href'=>'/?logout=1'));
$additionalMarkers['username'] = addElement('a',$headerdiv,'',array('title'=>getLang('btn:logged_in_as'),'href'=>'javascript:;'));
								 addElement('span',$additionalMarkers['username'],$user);
$additionalMarkers['userstable'] = createTable($headerdiv,'users',$attributes,$ad_users,true,array());
$additionalMarkers['groupstable'] = createTable($headerdiv,'groups',$attributes_role_to_right,$ad_roles_to_rights,false,array_keys($ad_rightgroups));
$additionalMarkers['rightstable'] = createTable($headerdiv,'rights',$attributes_user_to_role,$ad_users_to_roles,false,array_keys($ad_rolegroups));

//Replace markers:
replaceDocumentMarkers($document,$additionalMarkers);

//Output document:
echo $document->saveHTML();
//exit:
ldap_unbind($con);
exit();
