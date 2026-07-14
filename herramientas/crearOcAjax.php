<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}

$usuario = $_SESSION["usuario"];
$fecha = $_POST["fecha"];
$date = date("Y-m-d");
$datetime = date("Y-m-d H:i:s");
include("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
include("../include/NumeroAletras.php");// convertir numeros a letras

/* ------------------arreglos para obtener datos desde pagina anterior------------------ */
$i = $_POST["contador"]; //cantidad de elementos totales
$rut1 = $_POST["rutpv"];
$dv = $_POST["dv"];
$oc = $_POST["txtoc"];
$nlic =$_POST["txtlic"];
$obs = $_POST["txtcom"];
$nompv = $_POST["nompv"];
$parc=$_POST["parc"];
$tot = $_POST["txttotalfinal"];
$est = $_POST["estc"];
$cantsol = [];
$preciooc = [];
$codigo = [];
if ($parc == "on") {
	$parc=1;
}else{
	$parc=0;
}
if ($_SESSION['oc'] == $oc) {
    echo"<script>alert('Error, La OC ya se ha grabado en sistema.')</script>";
    exit();
}
if ($est == "9") {
    $est="1";
}else{
	$est="0";
}

for ($j = 0; $j < $i; $j++) {
    $cantsol[$j] = $_POST["txtreq_" . $j]; //array con las cantidades solicitadas
    $preciooc[$j] = $_POST["txtprec_" . $j]; //array con los precios para modificar
    $codigo[$j] = $_POST["txtcod_" . $j]; //array con los codigos 
}
/* ------------------actualizar datos de saldo oc en BBDD y mostrar resultado en pantalla------------------ */
for ($k = 0; $k < count($codigo); $k++) {
    $sql3 = "INSERT INTO saldos(saldo_clave, saldo_oc, saldo_fechaoc, saldo_rutprov, saldo_codigo, saldo_preciounit, saldo_solicitado, saldo_recibido, saldo_pendiente,saldo_fechacarga,saldo_est) "
            . "VALUES ('$oc$codigo[$k]','$oc','$fecha','$rut1','$codigo[$k]','$preciooc[$k]','$cantsol[$k]','0','$cantsol[$k]','$date','$est')";
    MySQL_query($sql3, $link) or die("<script>alert('Ha Ocurrido un Error al crear la OC seleccionada, probablemente, se encuentre ya registrada en Sistema')</script>");
}
$sql2 = "UPDATE proveedores set prov_estado ='1' WHERE prov_rut='$rut1'"; //agrupar en parentesis los campos para que se ejecute primero la accion
$data2 = mysql_query($sql2);
echo'<script>alert("Se ha Creado correctamente la OC: ' . $oc . '")</script>';
$sql4 = "INSERT IGNORE INTO oc_obs (oc_obs_oc, oc_obs_det, oc_obs_fecha, oc_obs_user, oc_obs_lic, oc_obs_parc) VALUES ('$oc', '$obs','$datetime','$usuario','$nlic','$parc')";
MySQL_query($sql4, $link);
$_SESSION['oc'] = $oc;
echo '<table class="table1"><tr><td class="texto8">SON: ' . convertir($tot) . ' PESOS. (CLP)</td></tr></table>';
mysql_close($link);
?>
