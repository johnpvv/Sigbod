<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
include("../include/conn.php");
set_time_limit(900);
$link = Conectarse(); //Variable de coneccion
$rs = mysql_query("SELECT * FROM const_config WHERE const_cat ='est' AND const_est='1'", $link); //constantes de configuracion, reconoce todos los categoria mail guardados
$row1 = mysql_fetch_array($rs);
$org = $row1["const_val"];

$buscar = $_POST['saldo'];
$est = $_POST['est'];
$comilladob = '"';
$comillasim = "'";
$anio = $_POST["anio"];
$date = strtotime($anio);
$anio2 = date('dmY', $date);

$rs = mysql_query("SELECT * FROM const_config WHERE const_cat ='ticket' AND const_est='1'", $link); //constantes de configuracion, reconoce todos los categoria ticket guardados
$row1 = mysql_fetch_array($rs);
$ticket = $row1["const_val"];

$url = "https://api.mercadopublico.cl/servicios/v1/publico/licitaciones.json?ticket=$ticket&CodigoOrganismo=$org";
$val = 0;
$html = "";
if ($anio != "") {
    $url .= "&fecha=$anio2";
} else {
    $val++;
}
if ($buscar != "") {
    $url .= "&codigo=$buscar";
} else {
    $val++;
}
if ($est != "") {
    $url .= "&estado=$est";
}
if ($val > 1) {
    echo "<script>alert('Error, ingrese algun dato para Buscar (" . $val . ")');</script>";
    exit();
}
//echo $url;
$data = json_decode(file_get_contents($url), true);
$num = $data["Cantidad"];
if ($num === 0 || $num == "") {
    echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran los documentos que cumplen con los filtros.'/>&nbsp;&nbsp;No hay datos para Mostrar</p>";
} else {
    echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran los documentos que cumplen con los filtros.'/>&nbsp;&nbsp;(se han encontrado: <b>$num</b> Resultados)</p>";
    echo '<form id="export" method="post" name="export" action="">';
    echo '<table id="tabladoc" class="table2">';
    $html = $html . '<table border="1" cellpadding="0" cellspacing="0">';
    echo '<thead><tr><th width="20">N°</th><th width="100">ID Mercado Publico</th><th width="400">Nombre Licitación</th><th width="40">Cod. Estado</th><th width="80">Nombre Estado</th><th width="50">Fecha Cierre</th><th width="80" data-sorter="false" data-filter="false">Acciones:</th></tr></thead>';
    $html = $html . '<thead><tr><th width="30">N°</th><th width="160">ID Mercado Publico</th><th width="600">Nombre Licitacion</th><th width="90">Cod. Estado</th><th width="150">Nombre Estado</th><th width="100">Fecha Cierre</th></tr></thead>';
    $j = 1;
    for ($i = 0; $i < $num; $i++) {
        $oc = $data["Listado"][$i]["CodigoExterno"];
        $nombre = $data["Listado"][$i]["Nombre"];
        $estado = $data["Listado"][$i]["CodigoEstado"];
        if($data["Listado"][$i]["FechaCierre"]==""){
            $fechac="Sin Datos";
        }else{            
            $fechac = date('d/m/Y h:i A', strtotime(str_replace("T", " ", $data["Listado"][$i]["FechaCierre"])));
        }        
        $rs2 = mysql_query("SELECT est_lic_nombre FROM est_lic_mp WHERE est_lic_cod ='$estado'", $link);
        $row2 = mysql_fetch_array($rs2);
        $nomest = $row2["est_lic_nombre"];
        if ($estado == "7") {
            $estilo = " style='color:red;'";
        } else {
            $estilo = "";
        }
        $mp = "<a href=../edicion/decodeJsonLic.php?id=" . $oc . "&op=1 target='_blank' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=640, width=1068 left=100 top=20" . $comillasim . "); return false;" . $comilladob . "><img src='../img/mp1.png' width='25' height='25' title='Ver Informacion Avanzada de la Licitacion'></a>";
        $lic = "<a href=https://www.mercadopublico.cl/Procurement/Modules/RFB/DetailsAcquisition.aspx?idlicitacion=" . $oc . " target='_blank' title='Ver Licitacion' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=550, width=800 left=300 top=100" . $comillasim . "); return false;" . $comilladob . ">" . $oc . "</a>";

        printf("<tr><td><b>%s</b></td><td><b>%s</b></td><td style='text-align:left;'><b>%s<b></td><td>%s</td><td" . $estilo . "><b>%s</b></td><td><b>%s</b></td><td>%s</td></tr>", $j, $lic, $nombre, $estado, $nomest, $fechac, $mp);
        $html = $html . "<tr><td><b>$j</b></td><td><b>$oc</b></td><td style='text-align:left;'>$nombre</td><td>$estado</td><td" . $estilo . "><b>$nomest</b></td><td style='width:120px;'>$fechac</td></tr>";
        $j++;
    }
    echo '</table>';
    $html = $html . '</table>';
    echo '<script type="text/javascript">$(document).ready(function () {$("#tabladoc").tablesorter();});</script>';
}
echo"<input type='hidden' name='flag' id='flag' value='1'>";
echo"<textarea name='html' hidden='yes'>" . $html . "</textarea></form>";
mysql_close($link);
?>