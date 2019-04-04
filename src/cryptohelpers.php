<?php

defined('__MAINAPP__') or die('nope');

function cryptoCoder( $string, $action = 'e' ) {
	global $config;
	global $secret_iv;
	// you may change these values to your own
	$output = false;

	if( $action == 'e' ) {
		$output = base64_encode( openssl_encrypt( $string, $config['encrypt_method'], $config['secret_key'], 0, $secret_iv ) );
	}
	else if( $action == 'd' ){
		$output = openssl_decrypt( base64_decode( $string ), $config['encrypt_method'], $config['secret_key'], 0, $secret_iv );
	}

	return $output;
}

function genNewIv() {
	global $config;
	
	return openssl_random_pseudo_bytes(openssl_cipher_iv_length($config['encrypt_method']));
}
