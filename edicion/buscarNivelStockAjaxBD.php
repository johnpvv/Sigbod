<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
if ($_SESSION['perfil'] > 1) {
    echo '<script>alert("Su Perfil no permite la visualizacion de esta pantalla, Favor contacte al Administrador.");history.back(-1);</script>';
    echo '<center><button class="boton" onclick="javascript:window.close()">Salir&nbsp;<img class="img" alt="Salir" title="salir de la pantalla de edicion" src="../img/salir.png"></button></center>';
    exit();
}
$date = date("y-m-d H:i:s");
include("../include/conn.php");
$link = Conectarse(); //Variable de conexion
$min = $_POST["min"]; //cantidades minimas para modificar
$cri = $_POST["cri"]; //cantidades criticas para modificar
$max = $_POST["max"]; //cantidades maximas para modificar
$codigo = $_POST["cod"]; // codigo a modificar          

/* ------------------actualizar datos de saldo oc en BBDD y mostrar resultado en pantalla------------------ */

$sql3 = "UPDATE `tiponivel` SET `tiponivel_min`='$min',`tiponivel_critico`='$cri',`tiponivel_max`='$max',`tiponivel_fecha`='$date' WHERE '$codigo'= tiponivel_codigo";
$data=MySQL_query($sql3, $link) or die("<script>alert('Ha Ocurrido un Error: " . mysql_error() . "');</script>");
$res=mysql_affected_rows($link);
if($res>0){
echo '<script>alert("Los datos se han actualizado Correctamente");</script>';
echo "<img src='../img/check.png' width='20' height='20' />";
}else{
    echo '<script>alert("Ha ocurrido un Error en la Grabacion");</script>';
    echo "<img src='../img/error.png' width='20' height='20' />";
}
mysql_close($link);
?>