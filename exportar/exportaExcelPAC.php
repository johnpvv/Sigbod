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
    $header = "Familia;Codigo Producto;Nombre Producto;Unidad Medida;Codigo CENABAST;Stock Actual;Precio Unitario Neto;Cantidad Programada;Total Valorizado PAC;Destacado;";
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header("Content-Disposition: attachment; filename=sigbod_PAC.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $header . "\n";
    while ($row = mysql_fetch_array($result)) {
        $tipo = $row["prd_destacado"];
        if ($tipo == "1") {
            $tipo = "Si";
        } else {
            $tipo = "No";
        }
        echo $row["prd_fam"].';'.$row["stock_codigo"] . ';' . iconv("UTF-8", "WINDOWS-1252", $row["prd_glosa"]) . ';' . $row["unimed_nombre"] . ';' . $row["prd_cenabast"] . ';' . $row["stock_cantidad"] . ';' ."$ " .number_format($row["prd_precio"], 2, ',', '.') .';'. $row["pac_cantidad"] . ';' . "$ " . number_format(($row["prd_precio"]*$row["pac_cantidad"]), 2, ',', '.') . ';' . $tipo . ';' . "\n";
        $sum = $sum + ($row["prd_precio"] * $row["pac_cantidad"]);
    }
    echo "\n" . ";;;;;;;;Total Valorizado con IVA: ;$" . number_format(($sum*1.19), 2, ',', '.');
    mysql_free_result($result);
    mysql_close($link);
}
?>