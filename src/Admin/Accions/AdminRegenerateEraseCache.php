<?php
//à
use Illuminate\Support\Facades\Session;

	$sc=new security();
	$c=new cache();
	if ($sc->testSession()==0) {
		$sc->endSession();
	}
	else {
		Session::put('missatge', $c->eraseCache());
		$sc->redirect_url(APP_BASE.'/get_main');
	}
?>