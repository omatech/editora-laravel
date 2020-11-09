<?php
//á

///////////////////////////////////////////////////////////////////////////////////////////
function redirect_action ($url) {
	header('Content-Type: text/html; charset=UTF-8', true);
	header('Location: '.$url);
	exit;
}
?>