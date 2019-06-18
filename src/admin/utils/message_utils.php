<?php

/**
 * message_utils
 *
 * @version $Id$
 * @copyright 2004 
 **/

function html_message($p_text, $p_mode)
{
  if ($p_mode=='E')
  {
       $html='
        <div class="col_item alert alert_wrong">
            <span></span>
            <div><p>'.$p_text.'</p></div>
            
            <p class="btn_close"><a onclick="$(&quot;.alert&quot;).hide();" href="javascript://">Cerrar</a></p>
        </div>';
       /*
            $icon=APP_BASE.'/images/error.gif';
            $class='omp_message_error';
            $html='<div id="message">
            <img src="'.$icon.'" border="0" class="img_message" />
            <h3 class="ko_message">'.$p_text.'</h3>
            </div>';
        */
  }
  if ($p_mode=='O'||$p_mode=='W')
  {
    
        $html='
        <div class="col_item alert alert_right">
            <span></span>
            <div><p>'.$p_text.'</p></div>
           
            <p class="btn_close"><a onclick="$(&quot;.alert&quot;).hide();" href="javascript://">Cerrar</a></p>
        </div>';
        /*
        $icon=APP_BASE.'/images/valid.gif';
	$class='omp_message_ok';
	$html='<div id="message">
		<img src="'.$icon.'" border="0" class="img_message" />
		<h3 class="ok_message">'.$p_text.'</h3>
	</div>';
        */
  }
  /*
  if ($p_mode=='W')
  {
    $icon=APP_BASE.'/images/alert.gif';
	$class='omp_message_ko';
	$html='<div id="message">
		<img src="'.$icon.'" border="0" class="img_message" />
		<h3 class="ko_message">'.$p_text.'</h3>
	</div>
	';
  }
    */
  
	return $html;


}

 function html_message_error($p_text)
 {
   return html_message($p_text, 'E');
 }

 function html_message_ok ($p_text)
 {
   return html_message($p_text, 'O');
 }
 function html_message_warning ($p_text)
 {
   return html_message($p_text, 'W');
 }
 
?>