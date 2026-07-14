<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
if ($_SESSION['perfil'] > 1) {
    echo '<script>alert("Su Perfil no permite la visualizacion de esta pantalla, Favor contacte al Administrador.");history.back(-1);</script>';
    exit();
}
include("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
$cat = $_POST["cat"];
$nom = $_POST["nom"];
$val = $_POST["val"];
$est = $_POST["est"];

$sql = "INSERT INTO `const_config`(`const_cat`,`const_nom`,`const_val`,`const_est`) VALUES ('$cat','$nom','$val','$est')";
MySQL_query($sql, $link)or die();
$contmod = mysql_affected_rows();
if ($contmod != 0) {
    echo '<script>alert("Los datos se han Actualizado Correctamente...");</script>';
} else {
    echo '<script>alert("Ha Ocurrido Un error con los Datos Ingresados (' . $contmod . '), favor Revisar y completar todos los campos Requeridos...");</script>';
}
mysql_close($link);
?>