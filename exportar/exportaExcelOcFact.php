<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$usuario = $_SESSION["usuario"];
$query = $_SESSION['queryoc'];
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
    $header = "OC Mercadopublico;Rut Proveedor;Nombre Prov;Fecha OC;Valor Total OC;Monto Total Facturado;Diferencia";
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header("Content-Disposition: attachment; filename=sigbod_oc_Factura.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $header . "\n";
    while ($row = mysql_fetch_array($result)) {
        $oc = $row['doc_noc'];
        $sql2 = "SELECT SUM(saldo_solicitado * saldo_preciounit) AS totaloc, saldo_oc, saldo_rutprov, saldo_fechaoc FROM saldos WHERE saldo_oc='$oc'";
        $data2 = mysql_query($sql2);
        $row2 = mysql_fetch_array($data2);
        $suma = $row["SUMA"];
        $valoroc = $row2["totaloc"] * 1.19;
        $dif = $suma - $valoroc;
        echo $oc . ';' . $row["prov_rut"] . "-" . $row["prov_dv"] . ';' . $row["prov_nombre"] . ';' . $row2["saldo_fechaoc"] . ';$' .  number_format(($valoroc), 0, '', '.') . ';$' . number_format(($suma), 0, '', '.') . ';$' .  number_format(($dif), 0, '', '.') . "\n";
    }
    mysql_free_result($result);
    mysql_close($link);
}
?>