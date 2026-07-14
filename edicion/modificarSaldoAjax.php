<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
if ($_SESSION['perfil'] > 1) {
    echo '<script>alert("Su Perfil no permite la modificacion de estos datos, Favor contacte al Administrador.");history.back(0);</script>';
    exit();
}
if ($_SESSION['pedido'] == 0) {
    echo"<script>javascript:history.go(-2)</script>";
    exit();
}
$usuario = $_SESSION["usuario"];
$date = date("Y-m-d");
$fechaobs = date("d/m/Y H:i:s");
$datetime = date("Y-m-d H:i:s");
include("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
/* ------------------arreglos para obtener datos desde pagina anterior------------------ */
$i = $_POST["contador"]; //cantidad de elementos totales
$rut1 = $_POST["rutpv"];
$rut = $_POST["rut"];
$oc = $_POST["oc"];
$nompv = $_POST["nompv"];
$fechaoc = $_POST["fechaoc"];
$foc = $_POST["foc"];
$op = $_POST["op"];
$obs = $_POST["obs"];
$lic = $_POST["lic"];
$parc = $_POST["parc"];
$cantsol = [];
$cantrec = [];
$cantpend = [];
$preciooc = [];
$codigo = [];
$tipo = [];

if ($parc == "on") {
	$parc=1;
}else{
	$parc=0;
}
for ($j = 0; $j < $i; $j++) {
    $cantsol[$j] = $_POST["txtsol_" . $j]; //array con las cantidades solicitadas
    $cantrec[$j] = $_POST["txtrec_" . $j]; //array con las cantidades recibidas para modificar
    $cantpend[$j] = $_POST["txtpend_" . $j]; //array con las cantidades pendientes para modificar
    $preciooc[$j] = $_POST["txtprec_" . $j]; //array con los precios para modificar
    $codigo[$j] = $_POST["txtcod_" . $j]; //array con los codigos 
    $tipo[$j] = $_POST["txttipo_" . $j];
}
for ($k = 0; $k < count($codigo); $k++) {
    if ($tipo[$k] == "n") {
        $sql3 = "INSERT IGNORE INTO saldos(saldo_clave, saldo_oc, saldo_fechaoc, saldo_rutprov, saldo_codigo, saldo_preciounit, saldo_solicitado, saldo_recibido, saldo_pendiente,saldo_fechacarga) "
                . "VALUES ('$oc$codigo[$k]','$oc','$fechaoc','$rut1','$codigo[$k]','$preciooc[$k]','$cantsol[$k]','$cantrec[$k]','$cantpend[$k]','$date')";
    } else {
        $sql3 = "UPDATE `saldos` SET `saldo_preciounit`='$preciooc[$k]',`saldo_solicitado`='$cantsol[$k]',`saldo_recibido`='$cantrec[$k]',`saldo_pendiente`='$cantpend[$k]',`saldo_fechacarga`='$date', `saldo_fechaoc`='$foc' WHERE '$oc$codigo[$k]'= saldo_clave";
    }
    MySQL_query($sql3, $link) or die("<script>alert('Ha Ocurrido un Error: " . mysql_error() . "Favor intente mas tarde.')");
}

if ($obs == "") {
    $obs = "Modificado el: " . $fechaobs;
}
$sql4 = "INSERT IGNORE INTO oc_obs (oc_obs_oc, oc_obs_det, oc_obs_fecha, oc_obs_user,oc_obs_lic, oc_obs_parc) VALUES ('$oc', '$obs','$datetime','$usuario','$lic','$parc')";
MySQL_query($sql4, $link);
echo '<script>alert("La Orden de compra: ' . $oc . ' se ha grabado correctamente...");</script>';
echo '<script>window.location.reload();</script>';
$_SESSION['pedido'] = 0;
mysql_close($link);
?>
