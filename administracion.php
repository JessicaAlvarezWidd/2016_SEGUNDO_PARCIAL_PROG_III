<?php

require_once "clases/AccesoDatos.php";
require_once "clases/Usuario.php";

$queMuestro = isset($_POST['queMuestro']) ? $_POST['queMuestro'] : NULL;

switch ($queMuestro) {

    case "2":

        session_start();

        if (isset($_SESSION["Usuario"]))
        {
            session_unset();
            echo "Ok";
        }
        else
            echo "No";

        break;

    case "3":

        require_once "grilla.php";

        break;

    case "4":

        require_once "form.php";

        break;

    case "5":

        require_once "clases/Archivo.php";

        $resultado = new stdClass();

        if (!isset($_FILES["imagenModificada"]))
        {
            $resultado->exito = FALSE;
            $resultado->mensaje = "ERROR";
            echo json_encode($resultado);
        }
        else
        {
            $resultado = Archivo::Subir();
            echo json_encode($resultado);
        }

        break;

    case "6":

        require_once "clases/Archivo.php";
        $resultado = new stdClass();
        $resultado->exito = TRUE;

        if (!isset($_POST["nuevoUser"]))
        {
            $resultado->exito = FALSE;
            $resultado->mensaje = "ERROR";
            echo json_encode($resultado);
        }
        else
        {
            $nuevoUsuario = json_decode($_POST["nuevoUser"]);            

            $origen = $_POST["fotoNueva"];

            $destino = "fotos/" . $nuevoUsuario->foto;

            $fotoNueva = substr_count($origen, "tmp/") > 0;

            if (!$fotoNueva || Archivo::Mover($origen, $destino))
            {
                if (Usuario::Agregar($nuevoUsuario))
                    $resultado->mensaje = "El usuario se agrego.";
                else
                {
                    $resultado->mensaje = "Error al agregar al usuario.";
                    $resultado->exito = FALSE;
                }
            }
            else
            {
                $resultado->mensaje = "Error al guardar la imagen.";
                $resultado->exito = FALSE;   
            }

            echo json_encode($resultado);
        }

        break;

    case "7":

        require_once "clases/Archivo.php";
        $resultado = new stdClass();
        $resultado->exito = TRUE;

        if (!isset($_POST["idEliminar"]) || !isset($_POST["fotoEliminar"]))
        {
            $resultado->exito = FALSE;
            $resultado->mensaje = "ERROR";
        }
        else
        {
            $userBorrar = new Usuario($_POST["idEliminar"]);

            if (session_id() == '')
                session_start();

            if (isset($_SESSION["Usuario"]))
            {
                $userEnSesion = json_decode($_SESSION["Usuario"]);
                if ($userEnSesion->id == $_POST["idEliminar"])
                {
                    $resultado->exito = FALSE;
                    $resultado->mensaje = "Imposible eliminarse a si mismo";
                }
                else
                {
                    if (Usuario::Borrar($_POST["idEliminar"]))
                    {
                        if ($_POST["fotoEliminar"] != './fotos/pordefecto.jpg')
                        {
                            Archivo::Borrar($_POST["fotoEliminar"]);
                        }                            

                        if (isset($_COOKIE["theme" . $_POST["idEliminar"]]))
                            setcookie("theme" . $_POST["idEliminar"], " ", time() - 1);

                        $resultado->mensaje = "Usuario eliminado.";                        
                    }
                    else
                    {
                        $resultado->exito = FALSE;
                        $resultado->mensaje = "Error al eliminar al usuario.";
                    }
                }
            }
        }
        echo json_encode($resultado);

        break;
        
    case "8":

        require_once "clases/Archivo.php";
        $resultado = new stdClass();
        $resultado->exito = TRUE;
        $resultado->userEnSesionMod = FALSE;

        if (!isset($_POST["usuarioModificado"]))
        {
            $resultado->exito = FALSE;
            $resultado->mensaje = "ERROR";
            echo json_encode($resultado);
        }
        else
        {
            $userMod = json_decode($_POST["usuarioModificado"]);            

            $userOriginal = new Usuario($userMod->id);

            $origen = $_POST["fotoNueva"];
            $destino = "fotos/" . $userMod->foto;

            if ($userOriginal->ActualizarFoto($origen, $destino))
            {
                if (Usuario::Modificar($userMod))
                {
                    $resultado->mensaje = "El usuario ha sido modificado.";

                    if (session_id() == '')
                        session_start();
                    if (isset($_SESSION["Usuario"]))
                    {
                        $usuarioEnSesion = json_decode($_SESSION["Usuario"]);
                        if ($usuarioEnSesion->id == $userMod->id)
                        {
                            $_SESSION["Usuario"] = json_encode($userMod);
                            $resultado->userEnSesionMod = TRUE;
                            $resultado->usuarioEnSesionNombre = $userMod->nombre;
                            $resultado->usuarioEnSesionPerfil = $userMod->perfil;
                            $resultado->usuarioEnSesionFoto = $destino;
                        }
                    }
                }
                else
                {
                    $resultado->mensaje = "Error al modificar al usuario.";
                    $resultado->exito = FALSE;
                }
            }
            else
            {
                $resultado->mensaje = "Error al guardar la imagen.";
                $resultado->exito = FALSE;   
            }

            echo json_encode($resultado);
        }

        break;

	case "9":

        require_once "grillaTheme.php";

		break;
	
	case "10":

        $resultado = new stdClass();
        $resultado->exito = TRUE;

        if (!isset($_POST["tema"]))
        {
            $resultado->exito = FALSE;
            $resultado->mensaje = "ERROR";
            echo json_encode($resultado);
        }
        else
        {
            if (session_id() == '')
                session_start();

            $tema = $_POST["tema"];

            $usuarioEnSesion = json_decode($_SESSION["Usuario"]);

            if (setcookie("theme" . $usuarioEnSesion->id, $tema, time() + 86400*30))
                $resultado->mensaje = "Se ha guardado el tema.";
            else
            {
                $resultado->exito = FALSE;
                $resultado->mensaje = "Error al querer guardar el tema.";
            }
            echo json_encode($resultado);
        }

		break;   
		
    default:
        echo ":(";
}