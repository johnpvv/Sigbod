<?php

session_start();
$timeant = microtime(true); //calcular el tiempo de ejecucion, capturo tiempo inicial
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
include("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
$buscar = $_POST['b'];
$buscar1 = $_POST['c'];
$fecha = $_POST['anio'];
if ($buscar == "") {
    $var = "";
} else {
    $var = " AND carga_nombre='" . $buscar . "'";
}
if ($buscar1 == "") {
    $var1 = "";
} else {
    $var1 = " AND carga_tipo='" . $buscar1 . "'";
}
if ($fecha == "") {
    $var2 = "";
} else {
    $var2 = " AND carga_fecha>='" . $fecha . "'";
}
echo '<table class="table2a" id="tabladoc">';
echo '<thead><tr>
    <th>Nombre Usuario</th>
    <th width="100">Tipo de Proceso</th>
    <th>Datos Cargados</th>
    <th>Registros Afectados</th>
    <th width="100">Fecha Movimiento</th>
    </tr></thead>';
$sql = "SELECT * FROM `carga_log`,`usuarios` WHERE usuario_rut=carga_usuario " . $var . $var1 . $var2 . " ORDER BY carga_fecha DESC";
$data = mysql_query($sql);
$contar = mysql_num_rows($data);
//echo $sql;
while ($row = mysql_fetch_array($data)) {
    if ($row["carga_nombre"] == "Borrado") {
        $borrado = "<b>N/A</b>";
    } else {
        $borrado = number_format($row["carga_cuenta"], 0, '.', ',');
    }
    printf("<tr><td>%s</td><td><b>%s</b></td><td>%s</td><td>%s</td><td>%s</td></tr>", $row["usuario_nombre"] . ' ' . $row["usuario_apellidos"], $row["carga_nombre"], $row["carga_tipo"], $borrado, date("d/m/Y h:i:s A", strtotime($row["carga_fecha"])));
}
mysql_free_result($data);
mysql_close($link);
$timedesp = microtime(true); //calculo tiempo final
$time = round($timedesp - $timeant, 2); //resto los tiempos y obtengo la demora de la ejecucucion
echo '</table>';
echo '<table class="table2a">';
echo '<tr>';
echo '<td colspan="5" class="texto8b">Se han encontrado: ' . $contar . ' Registros. (La consulta ha tardado: ' . $time . ' Segundos)</td>';
echo '</tr>';
echo '</table>';
echo '<script type="text/javascript">$(document).ready(function () {$("#tabladoc").tablesorter();showProcessing: true});</script>';
?>