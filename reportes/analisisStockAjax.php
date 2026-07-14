<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$buscar = $_POST['b'];
$anio= $_POST['anio'];
if ($buscar=='1'){
    $tipo="nivelstock_contador";
    $titulo="N° de Alertas Entregadas";
    $fecha=" AND nivelstock_fecha>"."'".$anio."'";
}else{
    $tipo="nivelstock_cuentatotal";
    $titulo="N° de Quiebres Encontrados";
    $fecha="AND nivelstock_fecha>"."'".$anio."'";
}
include("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
$sql = "SELECT * FROM `nivelstock`,`productos`, `unimed` WHERE prd_codigo=nivelstock_cod AND prd_unimed=unimed_id ".$fecha." ORDER BY ".$tipo." DESC";
//echo $sql;
$data = mysql_query($sql);
$contar = mysql_num_rows($data);
echo "<table class='table2'>";
echo "<tr><td colspan='5' class='texto2'><img align='center' src='../img/info.png' width='25' height='25' title='se muestran todos los productos que cumplen con los filtros.'/>&nbsp;&nbsp;Se han encontrado: ".$contar." Registros.</td></tr>";
if($contar!==0){
echo '<tr><th width="80">Codigo Articulo</th><th width="350">Glosa</th><th width="90">Presentación</th><th width="80">'.$titulo.'</th><th width="100">Fecha Ultimo Análisis</th></tr>';
echo "<tr>";
while ($row = mysql_fetch_array($data)) {
    printf("<tr><td><b>%s</b></td><td align='left'>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td></tr>", $row["nivelstock_cod"], $row["prd_glosa"], $row["unimed_nombre"], $row[$tipo], date("d/m/Y h:i:s A", strtotime($row["nivelstock_fecha"])));
}
$_SESSION['query'] = $sql;
}else{
    echo "</tr></table>";
}
mysql_close($link);
echo "</tr></table>";
?>
