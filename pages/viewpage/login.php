<?php

if(isset($_SESSION['SESS_ERROR_MSG'])) {
  _pageVar("ERROR_MSG",$_SESSION['SESS_ERROR_MSG']);
  unset($_SESSION['SESS_ERROR_MSG']);
} else {
  _pageVar("ERROR_MSG",false);
}

?>
  
  