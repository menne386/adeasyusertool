<?php
# Copyright 2018 Menne Kamminga <kamminga DOT m AT gmail DOT com>. All rights reserved.
# Use of this source code is governed by a BSD-style
# license that can be found in the LICENSE file.
/**
* @author: Menne Kamminga <mkamminga@bevolkingsonderzoeknoord.nl>
* 
* Here we define a global array with oath-hotp secret keys:
* To NOT accidently commit your configuration:
*   git update-index --assume-unchanged secrets.php
*/
defined('__MAINAPP__') or die('nope');

$secrets = array();


$secrets['administrator@SHORT_DOMAIN_NAME'] = array(
	'secret'=>"00 01 02 03 04 05 06 07 08 09 0a 0b 0c 0d 0e 0f 10 11 12 13", //The oath-hotp secret key in hex string format
	'initial'=>0, //The counter INITIAL value
	'token'=> 'ubnuXXXXXXXX' //The token identifier 
);

$secrets['someotheraccount@SHORT_DOMAIN_NAME'] = array(
	'secret'=>"00 01 02 03 04 05 06 07 08 09 0a 0b 0c 0d 0e 0f 10 11 12 13", //The oath-hotp secret key in hex string format
	'initial'=>0, //The counter INITIAL value
	'token'=> 'ubnuXXXXXXXX' //The token identifier 
);

