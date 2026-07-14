<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$usuario = $_SESSION["usuario"];
ini_set('memory_limit', '3000M');
set_time_limit(3000);
include("../include/conn.php");
$link = Conectarse();
$fini = $_GET["fini"];
$ffin = $_GET["ffin"];

$sql = "SELECT saldo_codigo, prd_glosa, prd_precio, unimed_nombre, SUM(saldo_pendiente) AS suma FROM saldos, productos, unimed WHERE saldo_est = 0 AND saldo_codigo=prd_codigo AND prd_unimed=unimed_id AND saldo_fechaoc BETWEEN '$fini' AND '$ffin' AND saldo_pendiente > 0 GROUP BY saldo_codigo";
$result = mysql_query($sql, $link);
$contar = mysql_num_rows($result);
    if ($contar <= 0) {
        echo '<script>alert("Error, no hay datos para exportar, favor revise los filtros...");				
            history.go(-1);
            </script>';
            exit();
    }
$header = ";;;INFORME CONSOLIDADO DE SALDOS\n";    
$header = $header . ";;;Fecha consulta desde: ".$fini." Hasta ".$ffin."\n\n";
$header = $header ."Codigo;Glosa;U.M.;Precio Unitario en sistema;Precio neto En ultima OC;Fecha Ultima OC;Total Saldo Pendiente";
header('Content-Encoding: UTF-8');
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header("Content-Disposition: attachment; filename=sigbod_saldooc_consolidado.csv");
header("Pragma: no-cache");
header("Expires: 0");
echo $header . "\n";
while ($row = mysql_fetch_array($result)) {
    $cod = $row["saldo_codigo"];
    $sql1 = "SELECT saldo_fechaoc, saldo_preciounit FROM saldos WHERE saldo_codigo='$cod' ORDER BY saldo_fechaoc DESC LIMIT 1";
    $result1 = mysql_query($sql1, $link);
    $row1 = mysql_fetch_array($result1);
    echo $cod . ';' . iconv("UTF-8", "WINDOWS-1252", $row["prd_glosa"]) . ';' . $row["unimed_nombre"] . ';$' . number_format($row["prd_precio"], 2, ',', '.') . ';$' . number_format($row1["saldo_preciounit"], 2, ',', '.') . ';' . $row1["saldo_fechaoc"] . ';' . $row["suma"] . "\n";
}
mysql_free_result($result);
mysql_close($link);
?>