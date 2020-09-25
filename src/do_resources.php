<?php
# Copyright 2018 Menne Kamminga <kamminga DOT m AT gmail DOT com>. All rights reserved.
# Use of this source code is governed by a BSD-style
# license that can be found in the LICENSE file.

defined('__MAINAPP__') or die('nope');

if(isset($_REQUEST['resource'])) {
	$content = "/*Generated ".date(getLang('datetime'))."*/\n";
	$files = array();
	switch($_REQUEST['resource']) {
		case 'styles.css':
			header('Content-Type: text/css');
			$files = array(
				'tmpl/bootstrap.min.css',
				'tmpl/base_styles.css',
				'style.css',
			);
			break;
		case 'main.js':
			header('Content-Type: text/javascript');
			$files = array(
				'jquery-3.3.1.min.js',
				'jquery.tablesorter.min.js',
				'main.js',
			);
			break;
	}
	foreach($files as $f) {
		$content .= "\n/*$f*/\n";
		$content .= file_get_contents($f);
	}
	$keys = array(); 
	$values = array();
	//print_r($config);
	foreach($config['style'] as $key=>$data) {
		//echo "$key > $data";
		$keys[$key] = '$('.$key.')';
		$values[$key] = $data;
	}
	$count = 0;
	$content = str_replace($keys,$values,$content,$count);
	
	$content .="\n/*ReplaceCount:$count markers: ".count($keys)."*/\n";
	die($content);
}
