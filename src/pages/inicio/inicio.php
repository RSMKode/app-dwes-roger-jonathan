<?php
//Variables y constantes comunes
require($_SERVER["DOCUMENT_ROOT"] . "/app-dwes-roger-jonathan/libs/config.php");
//Libreria de funciones de validación
require($_SERVER["DOCUMENT_ROOT"] . APP_ROOT . "libs/utils.php");
//Libreria de componentes
require($_SERVER["DOCUMENT_ROOT"] . APP_ROOT . "libs/componentes.php");

session_start();
//Se comprueba inactividad, que sea la misma ip de inicio de sesión, y se regenera el id si han pasado 5 minutos
cInactividad($inactivityTime);
cIP();
regenerarSesion();
//Comprobamos el color de la página
cColor();
$esquemaColor = $_COOKIE['esquemaColor'];

cabecera("Registro", $rutaEstilos, $esquemaColor);
require($_SERVER["DOCUMENT_ROOT"] . APP_ROOT . "libs/componentes/encabezado.php");

$errores = [];

echo "<h1>Iniciar Sesión</h1>";
echo "<main class='container'>";

if (isset($_SESSION["correo"])) {
    // Si ya se ha iniciado sesión, redirigimos a la página principal
    echo "<p>Ya has iniciado sesión.</p>";
    echo pintaEnlace(APP_ROOT . "src/pages/perfil/perfil-usuario.php", "Ir al perfil de usuario");
} else if (!isset($_REQUEST['enviar'])) {
    // Incluimos formulario vacio
    require("form-inicio.php");
} else {
    //Sanitizamos
    $correo = recoge("correo");
    $pass = recoge("pass");

    //Validamos los campos que no son ficheros
    cTexto($correo, "correo", $errores, "correo");
    cTexto($pass, "pass", $errores, "pass", 30, 4);

    if (empty($errores)) {

        $archivo = fopen($_SERVER["DOCUMENT_ROOT"] . APP_ROOT . "src" . DIRECTORY_SEPARATOR . $rutaArchivos . DIRECTORY_SEPARATOR . "datosUsuarios.txt", "r");
        while (!feof($archivo)) {
            $linea = str_replace("\n", "", fgets($archivo));

            if ($linea != "") {
                $datos = explode("|", $linea);

                $correoTemp = $datos[0];
                $passTemp = $datos[1];

                if ($correoTemp == $correo && $passTemp == $pass) {

                    $correo = $datos[0];
                    $pass = $datos[1];
                    $nombre = $datos[2];
                    $fechaNacimiento = $datos[3];
                    $rutaFoto = $datos[4];
                    $idioma = $datos[5];
                    $comentario = $datos[6];
                    fclose($archivo);

                    $_SESSION["correo"] = $correo;
                    $_SESSION["pass"] = $pass;
                    $_SESSION["nombre"] = $nombre;
                    $_SESSION["fechaNacimiento"] = $fechaNacimiento;
                    $_SESSION["rutaFoto"] = $rutaFoto;
                    $_SESSION["idioma"] = $idioma;
                    $_SESSION["comentario"] = $comentario;
                    $_SESSION["momentoLogin"] = time();
                    $_SESSION["ip"] = $_SERVER['REMOTE_ADDR'];
                    header("location:../perfil/perfil-usuario.php");
                }
            }
        }
        fclose($archivo);

        //Si no se encuentra el usuario en el archivo guardamos un log del fallo de inicio de sesión
        $horaActual = date("d-m-Y H:i:s");
        $archivo = fopen($_SERVER["DOCUMENT_ROOT"] . APP_ROOT . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . $rutaArchivos . DIRECTORY_SEPARATOR . "logLogin.txt", "a");
        fwrite($archivo, $correo . "|" . $pass . "|" . $horaActual . "|" . PHP_EOL);
        fclose($archivo);

        echo "<h2>Datos incorrectos</h2>";
        echo pintaEnlace("./inicio.php", "Volver a intentar");
    } else {
        require("form-inicio.php");
    }
}
echo pintaEnlace(APP_ROOT . "src/pages/index.php", "Volver al inicio");

echo "</main>";
pie();
