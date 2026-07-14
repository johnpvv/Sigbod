<?php

include_once("../include/conn.php");
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
if ($_SESSION['perfil'] > 1) {
    echo '<script>alert("Su Perfil no permite la visualizacion de esta pantalla, Favor contacte al Administrador.");history.back(-1);</script>';
    echo '<center><button class="boton" onclick="javascript:window.close()">Salir&nbsp;<img class="img" alt="Salir" title="salir de la pantalla de edicion" src="../img/salir.png"></button></center>';
    exit();
}
$link = Conectarse();
$pagina = $_POST["txtpagina"];
$option = $_POST["optionpgn"];
$cod = $_POST['txtrut'];
$dv = $_POST['txtdv'];
$nombre = preg_replace("/[\r\n|\n|\r]+/", " ", mb_strtoupper($_POST["txtnombre"]));
$giro = ucwords(mb_strtolower(trim($_POST["txtgiro"]))); //convierte todos los caracteres a minusculas
$dir = ucwords(mb_strtolower(trim($_POST["txtdir"])));
$rep = ucwords(mb_strtolower(trim($_POST["txtnomrep"])));
$mail = strtolower(trim($_POST["txtmail"]));
$fono = $_POST["txtfono"];
$estado = number_format($_POST["txtestado"]);
$fecha = date("y-m-d H:i:s");
if ($pagina === "modifica") {
    $sql = "UPDATE proveedores SET prov_nombre ='$nombre', prov_giro='$giro',prov_direccion='$dir',prov_nomcontacto='$rep',prov_email='$mail',prov_telefono='$fono',prov_estado='$estado',prov_fechacreacion='$fecha' WHERE prov_rut='$cod'";
} else {
    $sql = "INSERT INTO `proveedores`(`prov_rut`, `prov_dv`, `prov_nombre`, `prov_giro`, `prov_direccion`, `prov_nomcontacto`, `prov_email`, `prov_telefono`, `prov_estado`, `prov_fechacreacion`) VALUES ('$cod','$dv','$nombre','$giro','$dir','$rep','$mail','$fono','$estado','$fecha')";
}
mysql_query($sql, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
$cont = mysql_affected_rows();
if ($cont > 0) {
    setcookie("prov", "1");//grabar cookie prov, para saber que se creó
}
mysql_close($link);
if ($option == 1) {
    echo"<script>alert('Felicidades, los datos se han actualizado correctamente...');javascript:window.close();</script>";
} else {
    echo'<script>alert("Felicidades, los datos se han actualizado correctamente...");javascript:history.go(-1);</script>';
}
?> 
