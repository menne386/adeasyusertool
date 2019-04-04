<?php

defined('__MAINAPP__') or die('nope');



function reload() {
	header('Location: ./');
	session_write_close();
	die("Redirect");	
}

function kill_and_reload() {
	unset($_SESSION['AUTH']);
	unset($_SESSION['_i']);
	unset($_SESSION['debug']);
	reload();
}
