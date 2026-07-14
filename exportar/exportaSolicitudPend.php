<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$usuario = $_SESSION["usuario"];
$query = $_SESSION['query'];
$dia = $_SESSION["dia1"];
ini_set('memory_limit', '3000M');
set_time_limit(3000);
include("../include/conn.php");
$link = Conectarse();
$result = mysql_query($query, $link);
$fechaactual = $_SESSION["fecha1"];

if ($query == "") {
    echo '<script>alert("No Hay Datos para Exportar");				
			history.back(-1);
	</script>';
} else {
    $header = "Codigo Producto;Nombre Producto;Unidad Medida;Stock Actual;Stock Critico;Fecha Ultima Solicitud;Orden de Compra; RUT Proveedor; Nombre Proveedor;Folio Solicitud Interna;Cantidad Solicitada;Precio_oc";
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header("Content-Disposition: attachment; filename=sigbod_Productos_Solicitados.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $header . "\n";
    while ($row = mysql_fetch_array($result)) {
        $codigo = $row["prd_codigo"];
        $sql6 = "SELECT * FROM detmovimiento, movimiento, proveedores WHERE detmov_codigoprd = '$codigo' AND mov_id=detmov_id AND mov_rutprv=prov_rut ORDER BY detmov_id desc LIMIT 1"; //seleccionar el ultimo registro de atras para adelante
        $res6 = MySQL_query($sql6, $link) or die(mysql_error());
        $row6 = mysql_fetch_array($res6);
        $fechamov = strtotime($row6["mov_fecha"]);
        $dif6 = round(($fechaactual - $fechamov) / (3600 * 24));
        if ($dif6 < $dia) {
            echo $codigo . ';' . iconv("UTF-8", "WINDOWS-1252", $row["prd_glosa"]) . ';' . $row["unimed_nombre"] . ';' . number_format($row["stock_cantidad"], 0, ',', '.') . ';' . $row["tiponivel_critico"] . ';' . date("d/m/Y h:i A", strtotime($row6["mov_fecha"])) . ';' . $row6["detmov_oc"] . ';' . $row6["prov_rut"].';'. iconv("UTF-8", "WINDOWS-1252", $row6["prov_nombre"]) . ';' . $row6["mov_id"] . ';' . $row6["detmov_cantidad"] . ';' . number_format($row6["detmov_precio"], 2, ',', '.') .';' ."\n";
        }
    }
}
mysql_free_result($result);
mysql_close($link);
?>