<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$usuario = $_SESSION["usuario"];
$query = $_SESSION['query'];
ini_set('memory_limit', '3000M');
set_time_limit(3000);
include("../include/conn.php");
$link = Conectarse();
$fini = date("Y-01-01");
$ffin = date("Y-m-d");
$sql = 'SELECT `saldo_oc`,`saldo_fechaoc`,`saldo_codigo`,`saldo_preciounit`,`saldo_solicitado`,`saldo_recibido`,`saldo_pendiente`,`saldo_fechacarga`,`saldo_est`,`prov_rut`,`prov_dv`,`prov_nombre` FROM sigbod.saldos S, proveedores where saldo_pendiente=0 AND prov_rut=saldo_rutprov AND saldo_fechaoc BETWEEN "' . $fini . '" AND "' . $ffin . '" AND (select count(*) from documento where doc_noc = S.saldo_oc) = 0 ORDER BY saldo_fechaoc, saldo_oc';
$data = mysql_query($sql);

$header = "N_Orden de Compra;Fecha OC;RUT Proveedor;Nombre Proveedor;Codigo Producto;Precio Producto;Cantidad Solicitada;Cantidad Recibida;Cantidad Pendiente;Fecha Creacion;Estado Item";
header('Content-Encoding: UTF-8');
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header("Content-Disposition: attachment; filename=sigbod_saldo_sin_Factura.csv");
header("Pragma: no-cache");
header("Expires: 0");
echo $header . "\n";

while ($row = mysql_fetch_array($data)) {
    $sum = $sum + $row["saldo_preciounit"];
    echo $row["saldo_oc"] . ';' . $row["saldo_fechaoc"] . ';' . $row["prov_rut"]."-".$row["prov_dv"].';' . $row["prov_nombre"] . ';' . $row["saldo_codigo"] . ';$' . number_format($row["saldo_preciounit"], 0, '.', '') . ';' . $row["saldo_solicitado"] . ';' . $row["saldo_recibido"] . ';' . $row["saldo_pendiente"] . ';' . $row["saldo_fechacarga"] . ';' . $row["saldo_est"] . "\n";
}
echo ";;;;Total Valorizado:;$" . number_format($sum, 0, ',', '.');
mysql_free_result($data);
mysql_close($link);
?>