<?php

	require_once "clases/AccesoDatos.php";
	require_once "clases/Usuario.php";

	if (isset($_POST["email"]) && isset($_POST["password"]))
	{
		$objeto = new stdClass();
		$objeto->email = $_POST["email"];
		$objeto->password = $_POST["password"];

		$usuario = Usuario::TraerUsuarioLogueado($objeto);

		if ($usuario !== false)
		{
			session_start();

			$_SESSION["Usuario"] = json_encode($usuario);

			echo "Ok";
		}
		else
			echo "ERROR";
	}
	else
		header("location:login.php");
?>