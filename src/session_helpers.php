<?php
# Copyright 2018 Menne Kamminga <kamminga DOT m AT gmail DOT com>. All rights reserved.
# Use of this source code is governed by a BSD-style
# license that can be found in the LICENSE file.

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
