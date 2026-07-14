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
$result = mysql_query($query, $link);
$saldo = [];
if ($query == "") {
    echo '<script>alert("No Hay Datos para Exportar");				
			history.back(-1);
	</script>';
} else {
    $header = "Familia;Codigo Producto;Nombre Producto;Unidad Medida;Codigo CENABAST;Stock Actual;Precio Unitario Neto;Cantidad Programada Mensual;Total Valorizado PAC;%Faltante;Ordenes de compra;Destacado;";
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header("Content-Disposition: attachment; filename=sigbod_PAC_Ped.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $header . "\n";
    $res2 = mysql_query("SELECT saldo_codigo AS C, sum(saldo_pendiente) AS T FROM saldos WHERE  `saldo_est` = 0 group by saldo_codigo", $link);
    while ($row2 = mysql_fetch_array($res2)) {//llenar arreglo con los saldos y dejarlo preparado para consultar las oc con saldo
        $saldo[$row2["C"]] = $row2["T"];
    }
    mysql_free_result($res2);

    while ($row = mysql_fetch_array($result)) {
        $cod = $row["pac_codigo"];
        $tipo = $row["prd_destacado"];
        if ($tipo == "1") {
            $tipo = "Si";
        } else {
            $tipo = "No";
        }
        $pac = $row["pac_cantidad"] / 12;
        if ($pac == 0) {
            $porc = 0;
        } else {
            $porc = 100 - (($row["stock_cantidad"] / $pac) * 100);
        }
        if ($saldo[$cod] == 0) {
            $codoc = "Sin OC";
        } else {
            $codoc = "Con OC";
        }
        echo $row["prd_fam"] . ';' . $row["prd_codigo"] . ';' . iconv("UTF-8", "WINDOWS-1252", $row["prd_glosa"]) . ';' . $row["unimed_nombre"] . ';' . $row["prd_cenabast"] . ';' . $row["stock_cantidad"] . ';' . "$ " . number_format($row["prd_precio"], 2, ',', '.') . ';' . number_format($pac, 0, ',', '.') . ';' . "$ " . number_format(($row["prd_precio"] * $pac), 0, ',', '.') . ';' . number_format($porc, 1, ',', '') . '%;' . $codoc . ';' . $tipo . ';' . "\n";
        $sum = $sum + ($row["prd_precio"] * ($pac));
    }
    echo "\n" . ";;;;;;;;Total Valorizado con IVA: ;$" . number_format(($sum * 1.19), 2, ',', '.');
    mysql_free_result($result);
    mysql_close($link);
}
?>