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
    $header = "Codigo Producto;Nombre Producto;Unidad Medida;Stock Actual;Stock Critico;Stock Minimo;Stock Maximo;Precio Unitario;";
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header("Content-Disposition: attachment; filename=sigbod_nivel_stocks.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $header . "\n";
    while ($row = mysql_fetch_array($result)) {
        echo $row["prd_codigo"] . ';' .  $row["prd_glosa"] . ';' . $row["unimed_nombre"] . ';' . $row["stock_cantidad"] . ';'. $row["tiponivel_critico"] . ';' . $row["tiponivel_min"] . ';'. $row["tiponivel_max"] . ';'. "$ " . number_format($row["prd_precio"], 2, ',', '.') .  "\n";
    }
    mysql_free_result($result);
    mysql_close($link);
}
?>