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
    $header = "OC Mercadopublico;Rut Proveedor;Nombre Prov;Fecha OC;Fecha creacion Sigbod;Monto Total;Estado Recepciones;Estado Items";
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header("Content-Disposition: attachment; filename=sigbod_oc.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $header . "\n";
    while ($row = mysql_fetch_array($result)) {
        $oc = $row['saldo_oc'];
        $sql2 = "SELECT SUM(saldo_solicitado * saldo_preciounit) AS total, count(saldo_oc) AS cuenta, SUM(saldo_est) AS est FROM saldos WHERE saldo_oc='$oc'";
        $data2 = mysql_query($sql2);
        $row2 = mysql_fetch_array($data2);
        $suma = $row["suma"];
        $rec = $row["rec"];
        if ($suma == 0) {
            $estado = "Recepción Completa";
        } else if ($rec == 0) {
            $estado = "Sin Recepciones";
        } else {
            $estado = "Recepción Parcial";
        }
        $cuenta = $row2["cuenta"];
            $est = $row2["est"];
            if ($est == 0) {
                $estoc = "Vigentes";
                $style = " style='color:blue;'";
            } else if ($est == $cuenta) {
                if ($rec == 0) {
                    $estoc = "Anulada";
                } else {
                    $estoc = "Saldo Pendiente Anulado";
                }
            } else {
                $estoc = "Anulación Parcial";
            }
        echo $row["saldo_oc"] . ';' . $row["saldo_rutprov"] . "-" . $row["prov_dv"] . ';' . $row["prov_nombre"] . ';' . $row["saldo_fechaoc"] . ';' . $row["saldo_fechacarga"] . ';' . number_format(($row2["total"] * 1.19), 0, '', '.') . ';' . iconv("UTF-8", "WINDOWS-1252", $estado) .';'.iconv("UTF-8", "WINDOWS-1252", $estoc). "\n";
    }
    mysql_free_result($result);
    mysql_close($link);
}
?>