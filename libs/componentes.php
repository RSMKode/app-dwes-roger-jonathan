<?php

// Función para pintar un checkbox con los valores que nos pasan por un array

function pintaCheck(array $valores, string $name)
{
    foreach ($valores as $key => $valor) {
        echo '<input type="checkbox" name="' . $name . '[]" value=' . $valor . '>' . $valor;
    };
};

function pintaRadio(array $valores, string $name)
{
    foreach ($valores as $key => $valor) {
        echo '<input type="radio" name="' . $name . '" value="' . $valor . '">' . $valor . '<br>';
    };
};

function botonCerrarSesion($rutaLocation)
{
    echo "
        <form action='' method='post'>
        <input type='submit' name='cerrarSesion' value='Cerrar Sesión'>
        </form>
        ";

    if (isset($_POST['cerrarSesion'])) {
        session_destroy();
        header("location:$rutaLocation");
    }
}
