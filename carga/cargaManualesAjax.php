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
$rs = mysql_query("SELECT MAX(manual_id) AS id FROM manuales");
if ($row = mysql_fetch_row($rs)) {
    $id = trim($row[0] + 1);
}
$nombre = $_POST["nombrearch"];
$file = $_FILES['arch']['name'];
$nomarch = "manual_" . $id; //el nombre de archivo se compone de el id del registro principal
$extension = pathinfo($_FILES['arch']['name'], PATHINFO_EXTENSION); //capturar extension del archivo
$destino = '../manuales/' . $nomarch . '.' . strtolower($extension);
copy($_FILES['arch']['tmp_name'], $destino);

$sql = "INSERT INTO `manuales`(`manual_nombre`,`manual_url`,`manual_estado`) VALUES ('$nombre','$destino','1')";
MySQL_query($sql, $link)or die();
$contmod = mysql_affected_rows();
if ($contmod != 0) {
    echo '<script>alert("Los datos se han Actualizado Correctamente...");</script>';
} else {
    echo '<script>alert("Ha Ocurrido Un error con los Datos Ingresados (' . $contmod . '), favor Revisar y completar todos los campos Requeridos...");</script>';
}
mysql_close($link);
?>