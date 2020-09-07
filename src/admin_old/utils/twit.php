<?php

use Illuminate\Support\Facades\Session;


//SEND TWIT TO TWITTER
// The twitter API address
require_once($_SERVER['DOCUMENT_ROOT'].'/conf/ompinfo.php');
if(is_numeric(Session::get('user_id'))) {
	$message = $_REQUEST['message'];
	$lg = Session::get('u_lang');
	
	require_once($_SERVER['DOCUMENT_ROOT'].DIR_LANGS.$lg.'/messages.inc');
	
	$url = 'http://twitter.com/statuses/update.xml';
	// Alternative JSON version
	// $url = 'http://twitter.com/statuses/update.json';
	// Set up and execute the curl process
	
	$curl_handle = curl_init();
	curl_setopt($curl_handle, CURLOPT_URL, "$url");
	curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl_handle, CURLOPT_POST, 1);
	curl_setopt($curl_handle, CURLOPT_POSTFIELDS, "status=$message");
	curl_setopt($curl_handle, CURLOPT_USERPWD, TWITUSER.":".TWITPASS);
	$buffer = curl_exec($curl_handle);
	$info=curl_getinfo($curl_handle);
	curl_close($curl_handle);
	// check for success or failure
	if ($buffer===false || $info['http_code'] != 200) {
		echo __('editora_lang::messages.no_twitted');
	} 
	else {
		//echo $info['http_code'].'-->'.$buffer;
		echo __('editora_lang::messages.twitted');
	}
}
?>