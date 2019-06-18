<?php
//à
	$sc=new security();
	$c=new cache();
	if ($sc->testSession()==0) {
		$sc->endSession();
	}
	else {
		$_SESSION['missatge']=$c->regenerateCacheWithout();
		$sc->redirect_url(APP_BASE.'/get_main');
	}
?>