<?php
$noWorld = ["modules","popup"];

if(in_array(PAGE, $noWorld)) {
	if($_SERVER['HTTP_REFERER']==null || strlen($_SERVER['HTTP_REFERER'])<=1) {
		header("Location:"._link(""));
		exit("This page is allowed within Application only.");
	}
	echo "<script>if(top==window) window.location='"._link("")."';</script>";
}
?>
