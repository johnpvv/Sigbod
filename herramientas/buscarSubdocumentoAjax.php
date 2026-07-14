<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
if ($_SESSION['perfil'] > 1) {
    echo '<script>alert("Su Perfil no permite la modificación de este dato, Favor contacte al Administrador.");history.back(-1);</script>';
    echo '<button class="boton" onclick="javascript:window.close()">Salir&nbsp;<img class="img" alt="Salir" title="salir de la pantalla de edicion" src="../img/salir.png"></button>';
    exit();
}
include("../include/conn.php");
$link = Conectarse(); //Variable de conexion
$date = date("Y-m-d H:i:s");
$est = $_POST["est"]; //estado
$id = $_POST["id"]; //id
$user = $_SESSION['usuario'];
     
/* ------------------actualizar datos de Documento en BBDD y mostrar resultado en pantalla------------------ */

$sql3 = "UPDATE `documento` SET `doc_estado`='$est',`doc_umodif`='$user',`doc_fechamod`='$date' WHERE '$id'= doc_id";
$data=MySQL_query($sql3, $link) or die("<script>alert('Ha Ocurrido un Error: " . mysql_error() . "');</script>");
$res=mysql_affected_rows($link);
if($res>0){
echo '<script>alert("Los datos se han actualizado Correctamente");</script>';
echo "<img src='../img/check.png' width='25' height='25' />";
}else{
    echo '<script>alert("Ha ocurrido un Error en la Grabacion");</script>';
    echo "<img src='../img/error.png' width='25' height='25' />";
}
mysql_close($link);
?>