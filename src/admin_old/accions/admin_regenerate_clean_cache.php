<?php
//à
use Illuminate\Support\Facades\Session;

	$sc=new security();
	$c=new cache();
	if ($sc->testSession()==0) {
		$sc->endSession();
	}
	else {
		Session::put('missatge', $c->cleanCache());
		$sc->redirect_url(APP_BASE.'/get_main');
	}
?>