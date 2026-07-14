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
if ($query == "") {
    echo '<script>alert("No Hay Datos para Exportar");				
			history.back(-1);
	</script>';
} else {
    $header = "Familia;Codigo Producto;Nombre Producto;Unidad Medida;Stock Actual;Nivel de Stock;Precio Unitario;Ordenes de Compra";
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header("Content-Disposition: attachment; filename=sigbod_nivel_stocks.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $header . "\n";
    while ($row = mysql_fetch_array($result)) {
        $codigo=$row["prd_codigo"];
        $nivel = $row["stock_cantidad"];
        if ($nivel < 10) {
            $tipo = "Insuficiente";
        } else {
            $tipo = "Bajo";
        }
        $sql="SELECT * FROM saldos WHERE saldo_codigo='$codigo' AND saldo_pendiente > 0";
        $res = MySQL_query($sql, $link) or die(mysql_error());
        $contar = mysql_num_rows($res);
        if ($contar == 0) {
            $codoc = "Sin OC";
        } else {
            $codoc = "Con OC";
        }
        echo $row["prd_fam"] . ';' . $codigo . ';' . $row["prd_glosa"] . ';' . $row["unimed_nombre"] . ';' . $row["stock_cantidad"] . ';' . $tipo . ';' . "$ " . number_format($row["prd_precio"], 2, ',', '.') . ';' . $codoc . "\n";
    }
    mysql_free_result($result);
    mysql_close($link);
}
?>