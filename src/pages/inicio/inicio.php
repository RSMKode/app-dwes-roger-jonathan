<?php
require("../../../libs/utils.php");
//De config.php leeremos $extensionesValidas, $rutaImagenes, $maxFichero.
require("../../../libs/config.php");

session_start();

cabecera("Registro", "../../styles.css");
$errores = [];

echo "<h1>Iniciar Sesión</h1>";
echo "<main class='container'>";

if (isset($_SESSION["correo"])) {
    // Si ya se ha iniciado sesión, redirigimos a la página principal
    echo "<p>Ya has iniciado sesión.</p>";
    echo "<a clas='accent' href='../perfil/perfil-usuario.php'>Ir al perfil de usuario</a>";
} else if (!isset($_REQUEST['enviar'])) {
    // Incluimos formulario vacio
    require("form-inicio.php");
} else {
    //Sanitizamos
    $correo = recoge("correo");
    $pass = recoge("pass");

    //Validamos los campos que no son ficheros
    cCorreo($correo, "correo", $errores);
    cPass($pass, "pass", $errores, 30, 4);

    if (empty($errores)) {

        $archivo = fopen("../../$rutaArchivos" . DIRECTORY_SEPARATOR . "datosUsuarios.txt", "r");
        while (!feof($archivo)) {
            $linea = str_replace("\n", "", fgets($archivo));

            if ($linea != "") {
                $datos = explode("|", $linea);

                $correoTemp = $datos[0];
                $passTemp = $datos[1];
                echo "$correoTemp $passTemp";

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
                    header("location:../perfil/perfil-usuario.php");
                }
            }
        }
        fclose($archivo);

        //Redirigimos a valid.php
        echo "<h2>Datos incorrectos</h2>";
    } else {
        require("form-inicio.php");
    }
}
echo "</main>";
pie();
