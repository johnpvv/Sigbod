<?php

$timeant = microtime(true); //calcular el tiempo de ejecucion, capturo tiempo inicial
session_start();
ini_set('memory_limit', '3000M');
set_time_limit(3000);
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
include("../include/conn.php");
$link = Conectarse();
$cuenta = 0;
$fechahoy = strtotime(date("Y-m-d H:i:s"));
$fechaini = $_POST['anio'];
$fechafin = $_POST['anio1'] . " 23:59:59";
$tipo = $_POST['chk'];
$saldo = $_POST['saldo'];

$rango = "AND mov_fecha BETWEEN '$fechaini' AND '$fechafin' ";
if ($tipo == "true") {
    $dest = " AND prd_destacado='1'";
} else {
    $dest = "";
}
echo '<table class="table2" id="tabladoc">';
echo '<thead>
    <tr class="texto5">
    <th width="60">Código Producto</th>
    <th width="220">Glosa</th>
    <th width="60">Unidad de Medida</th>
    <th width="70">Tipo Solicitud</th>
	<th width="60">Folio Solicitud</th>
    <th width="70">Fecha Solicitud</th>
    <th width="250">Proveedor</th>
    <th width="120">Orden de Compra</th>    
    <th width="60"> Cantidad Solicitada</th>
    <th width="60" data-sorter="false" data-filter="false">Acciones</th></tr>
    </thead>';

$sql6 = "SELECT prd_codigo, prd_glosa, prd_unimed, prd_destacado, unimed_nombre, unimed_id, mov_rutprv, mov_fecha, detmov_codigoprd, detmov_cantidad, detmov_oc, detmov_id, detmov_precio, mov_id, detmov_codigoprd, prov_rut, prov_dv, prov_nombre
	FROM detmovimiento, movimiento, productos, unimed, proveedores
	WHERE prov_rut=mov_rutprv AND mov_id=detmov_id AND mov_estado='1' AND prd_unimed=unimed_id AND prd_codigo=detmov_codigoprd " . $rango . $dest . " AND (prov_nombre LIKE '%" . $saldo . "%' or prov_rut LIKE '%" . $saldo . "%' or detmov_oc LIKE '%" . $saldo . "%' OR prd_codigo LIKE '%" . $saldo . "%' OR prd_glosa LIKE '%" . $saldo . "%') ORDER BY prov_nombre ASC, detmov_oc ASC "; //seleccionar el ultimo registro de atras para adelante
//echo $sql6;
$_SESSION['query_solicitudes'] = $sql6;
$res6 = MySQL_query($sql6, $link) or die(mysql_error());
while ($row6 = mysql_fetch_array($res6)) {
    $codigo = $row6["prd_codigo"];
    $fechamov = strtotime($row6["mov_fecha"]);
    $tipo = "Solicitud a proveedores";
    $sol = "<a href=pedidoPDF.php?id=" . $row6['mov_id'] . " target='_blank'><img border='0' alt='Ver solicitud' title='Ver Solicitud' src='../img/pdf.png' width='24' height='22'></a>";
    printf("<tr><td><b>%s</b></td><td align='left'>%s</td><td>%s</td><td>%s</td><td>SOL-%s</td><td>%s</td><td align='left'><b>%s</b></td><td><b>%s</b></td><td><b>%s</b></td><td>%s</td></tr>", $codigo, $row6["prd_glosa"], $row6["unimed_nombre"], $tipo, $row6["mov_id"], date("d/m/Y h:i A", strtotime($row6["mov_fecha"])), "<b style='color:red;'>" . number_format($row6["prov_rut"], 0, '.', '.') . "-" . $row6["prov_dv"] . "</b>&nbsp;" . " " . $row6["prov_nombre"], $row6["detmov_oc"], $row6["detmov_cantidad"], $sol);
    $cuenta++;
}
$timedesp = microtime(true); //calculo tiempo final
$time = round($timedesp - $timeant, 2); //resto los tiempos y obtengo la demora de la ejecucucion

echo '</tr>';
echo '</table>';
echo '<table class="table2"><tr>';
echo '<td colspan="11" class="texto8b">Se han encontrado: ' . $cuenta . ' Registros. (La consulta ha tardado: ' . $time . ' Segundos)</td>';
echo '</tr>';
echo '</table>';
echo '<script type="text/javascript">$(document).ready(function () {$("#tabladoc").tablesorter();});</script>';
mysql_close($link);
?>