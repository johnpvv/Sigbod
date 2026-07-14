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
$anio = $_GET["anio"];
$anio2 = $_GET["anio2"] . " 23:59:59";
$anio1 = " AND saldo_fechaoc BETWEEN '$anio' AND '$anio2' ";
$link = Conectarse();
$result = mysql_query($query, $link);
if ($query == "") {
    echo '<script>alert("No Hay Datos para Exportar");				
			history.back(-1);
	</script>';
} else {
    $header = "Rut Proveedor;Nombre Proveedor;Valor neto Transado;Valor Total Transado;Cantidad OC Encontradas;";
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header("Content-Disposition: attachment; filename=sigbod_prov_montos.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $header . "\n";
    while ($row = mysql_fetch_array($result)) {
		$prov = $row['saldo_rutprov'];
            $sql2 = "SELECT COUNT(DISTINCT(saldo_oc)) AS total FROM saldos WHERE saldo_rutprov='$prov'" . $anio1 . " GROUP BY saldo_oc";
			$data2 = mysql_query($sql2);
            $cnt= mysql_num_rows($data2);			
        echo $row["saldo_rutprov"] . "-" . $row["prov_dv"] . ';' . $row["prov_nombre"] . ';$' . number_format($row["suma"], 0, ',', '.'). ';$' . number_format(($row["suma"] * 1.19), 0, ',', '.'). ';' . $cnt. "\n";
    }
    mysql_free_result($result);
    mysql_close($link);
}
?>