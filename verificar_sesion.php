<?php
	session_start();

	if (!isset($_SESSION["Usuario"]))
		header("location:login.php");

?>