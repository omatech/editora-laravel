<?php

/**
 * message_utils
 *
 * @version $Id$
 * @copyright 2004 
 **/

function html_message($p_text, $p_mode)
{

  switch($p_mode){
    case 'E':
      $class = 'message-wrong';
      break;
      
    case 'W':
      $class = 'message-wrong';
      break;
    case 'O':
      $class = 'message-ok';
      break;
    default:
      $class = 'message-wrong';
      break;
  }
  $html = '<div class="toaster">
      <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
          <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="toast-body">
          <p class="message '.$class.'"><i class="icon-circle-alert"></i> <span class="txt">'.$p_text.'</span></p>
        </div>
      </div> 
    </div>';
  
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