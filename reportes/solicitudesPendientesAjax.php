<?php

$timeant = microtime(true); //calcular el tiempo de ejecucion, capturo tiempo inicial
session_start();
ini_set('memory_limit', '3000M');
set_time_limit(3000);
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
include("../include/conn.php");
$link = Conectarse();
$buscar = $_POST['b'];
$cont = 0;
$_SESSION["dia1"] = $buscar;
$c = $_POST['chkall'];
if ($c == "true") {
    $dest = "";
} else {
    $dest = "prd_destacado=1 AND";
}
$fechahoy = strtotime(date("Y-m-d H:i:s"));
$fechaactual = strtotime(date($_POST['anio']));
$_SESSION["fecha1"] = $fechaactual;
$sql5 = "SELECT prd_codigo, prd_glosa, prd_unimed, unimed_nombre, unimed_id, tiponivel_codigo, prd_destacado, stock_codigo, stock_cantidad, tiponivel_critico
        FROM productos, stock, tiponivel, unimed
        WHERE " . $dest . " prd_codigo=stock_codigo AND tiponivel_codigo=stock_codigo AND prd_unimed=unimed_id AND stock_cantidad <= tiponivel_critico ORDER BY prd_codigo";
$res5 = MySQL_query($sql5, $link) or die(mysql_error());
$cuenta = 0;
$_SESSION['query'] = $sql5;
//echo $sql5;
echo '<table class="table2" id="tabladoc">';
echo '<thead><tr class="texto5">
    <th width="80">Código Producto</th>
    <th width="370">Glosa</th>
    <th width="70">Unidad de Medida</th>
    <th width="60" class="color_amarillo">Stock Actual</th>
    <th width="65">Stock Critico</th>
    <th width="80">Tipo Solicitud</th>
    <th width="90">Fecha Ultima Solicitud</th>
    <th width="100">Orden de Compra</th>
    <th width="70">Folio Solicitud</th>
    <th width="60"> Cantidad Solicitada</th>
    <th width="60" data-sorter="false" data-filter="false">Acciones</th></tr></thead>';
while ($row5 = mysql_fetch_array($res5)) {
    $codigo = $row5["prd_codigo"];
    $sql6 = "SELECT mov_rutprv, mov_fecha, detmov_codigoprd, detmov_cantidad, detmov_oc, detmov_id, detmov_precio, mov_id FROM detmovimiento, movimiento WHERE detmov_codigoprd = '$codigo' AND mov_id=detmov_id ORDER BY detmov_id desc LIMIT 1"; //seleccionar el ultimo registro de atras para adelante
    $res6 = MySQL_query($sql6, $link) or die(mysql_error());
    $row6 = mysql_fetch_array($res6);
    $fechamov = strtotime($row6["mov_fecha"]);
    $dif6 = round(($fechaactual - $fechamov) / (3600 * 24));
    $difdia = round(($fechahoy - $fechamov) / (3600 * 24));
    if ($difdia <= 2) {
        $clase = " class='texto4b'";
    } else {
        $clase = "";
    }
    $tipo = "solicitud a proveedores";
    if ($dif6 < $buscar) {
        $sol = "<a href=pedidoPDF.php?id=" . $row6['mov_id'] . " target='_blank'><img border='0' alt='Ver solicitud' title='Ver Solicitud' src='../img/pdf.png' width='24' height='22'></a>";
        printf("<tr><td><b>%s</b></td><td align='left'>%s</td><td>%s</td><td class='color_amarillo'><b>%s</b></td><td>%s</td><td>%s</td><td" . $clase . ">%s</td><td><b>%s</b></td><td>%s</td><td><b>%s</b></td><td>%s</td></tr>", $codigo, $row5["prd_glosa"], $row5["unimed_nombre"], $row5["stock_cantidad"], $row5["tiponivel_critico"], $tipo, date("d/m/Y h:i A", strtotime($row6["mov_fecha"])), $row6["detmov_oc"], $row6["mov_id"], $row6["detmov_cantidad"], $sol);
        $cuenta++;
    }
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