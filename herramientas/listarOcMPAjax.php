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
$datetime = date("Y-m-d H:i:s");
$usuario = $_SESSION["usuario"];
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

$url = "https://api.mercadopublico.cl/servicios/v2/publico/ordenesdecompra.json?ticket=$ticket&CodigoOrganismo=$org";
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
    echo '<thead><tr><th width="20">N°</th><th width="100">OC Mercado Publico</th><th width="300">Nombre OC</th><th width="50">Cod. Licitacion</th><th width="40">Cod. Estado</th><th width="100">Nombre Estado</th><th width="80">Registrada en Sigbod</th><th width="100" data-sorter="false" data-filter="false">Acciones:</th></tr></thead>';
    $html = $html . '<thead><tr><th width="30">N°</th><th width="160">OC Mercado Publico</th><th width="600">Nombre OC</th><th width="150">Cod. Licitacion</th><th width="90">Cod. Estado</th><th width="150">Nombre Estado</th><th width="100">Registrada en Sigbod</th></tr></thead>';
    $j = 1;
    for ($i = 0; $i < $num; $i++) {
        $oc = $data["Listado"][$i]["Codigo"];
        $nombre = str_replace(array('\\', ':', '*', '?', '"', '<', '>', '|', "'", "#"), ' ', trim($data["Listado"][$i]["Nombre"])); //reemplazar caracteres no validos;		
		$pos = strpos($nombre, "10570");
		if($pos!=""){
			$n=substr($nombre, $pos, 19);			
			$fpos = strpos($n, " ");
			if($fpos!=""){			
				$nlic=str_replace(array('\\', ':', '*', '?', '"', '<', '>', '|', "'", "#", ',' ,'.'), '', trim(substr($n, 0, $fpos)));
			}else{
				$nlic=str_replace(array('\\', ':', '*', '?', '"', '<', '>', '|', "'", "#", ',' ,'.'), '', trim($n));
			}
		}else{
			$nlic="";
		}
        $estado = $data["Listado"][$i]["CodigoEstado"];
        $rs2 = mysql_query("SELECT est_nombre FROM est_mp WHERE est_cod ='$estado'", $link);
        $row2 = mysql_fetch_array($rs2);
        $nomest = $row2["est_nombre"];
        if ($estado == "9") {
            $estilo = " style='color:red;'";
        } else {
            $estilo = "";
        }
        $rs3 = mysql_query("SELECT count(saldo_oc) AS C, saldo_rutprov FROM saldos WHERE saldo_oc ='$oc'", $link);
        $row3 = mysql_fetch_array($rs3);
        $sis = $row3["C"];
        if ($sis > 0) {
            $check = '<img class="imgnormal" src="../img/check.png" id="ticket" title="La OC esta en Sistema">';
            $chkhtml = "SI";
            $rut = $row3["saldo_rutprov"];
            $linkoc = "<a href=../edicion/modificarSaldo.php?id=" . $rut . "&oc=" . $oc . "&op=1 target='_blank'><img border='0' alt='editar oc' title='Editar OC: " . $oc . "' src='../img/edit.png' width='25' height='25'></a>&nbsp;&nbsp;";
            $pdf = "|&nbsp;&nbsp;<a href=../reportes/ocPDF.php?id=" . $rut . "&oc=" . $oc . " target='_blank'><img border='0' alt='editar oc' title='Generar Reporte OC: " . $oc . "' src='../img/pdf.png' width='25' height='25'></a>";
            $mp = "";
			$res2 = mysql_query("SELECT oc_obs_det,oc_obs_id FROM oc_obs WHERE oc_obs_oc='$oc' ORDER BY oc_obs_id DESC LIMIT 1", $link);
			$cont2 = mysql_num_rows($res2);
			if ($cont2 == 0) {//crear observacion si no existe			
				$sql3 = "INSERT INTO oc_obs (oc_obs_oc, oc_obs_det, oc_obs_fecha, oc_obs_user, oc_obs_lic) VALUES ('$oc', '$nombre','$datetime','$usuario','$nlic')";
				$cnt++;
				MySQL_query($sql3, $link);
			}
		} else {
            $check = '<img class="imgnormal" src="../img/error.png" id="ticket" title="La OC No Aparece en Sistema">';
            $linkoc = "<a href=crearOC.php?oc=" . $oc . "&op=1 target='_blank'><img border='0' alt='crear oc' title='crear OC' id='".$oc."' src='../img/download.png' width='25' height='25' onclick='cambiaImg(this.id)'></a>";
            $rut = "";
            $chkhtml = "NO";
            $pdf = "";
            $mp = "&nbsp;&nbsp;|&nbsp;&nbsp;<a href=../edicion/decodeJsonOC.php?oc=" . $oc . "&op=1 target='_blank' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=640, width=1068 left=100 top=20" . $comillasim . "); return false;" . $comilladob . "><img src='../img/mp1.png' width='25' height='25' title='Ver Informacion Avanzada de la Orden de compra'></a>";
        }
        printf("<tr><td><b>%s</b></td><td><b>%s</b></td><td style='text-align:left;'><b>%s<b></td><td>%s</td><td" . $estilo . "><b>%s</b></td><td " . $estilo . ">%s</td><td>%s</td><td>%s</td></tr>", $j, "<a href=https://www.mercadopublico.cl/PurchaseOrder/Modules/PO/DetailsPurchaseOrder.aspx?codigooc=" . $oc . " target='_blank' title='Ver OC en MP' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=640, width=980 left=200 top=50" . $comillasim . "); return false;" . $comilladob . ">" . $oc . "</a>", $nombre, $nlic, $estado, $nomest, $check, $linkoc . $pdf. $mp);
        $html = $html . "<tr><td><b>$j</b></td><td><b>$oc</b></td><td style='text-align:left;'>$nombre</td><td style='text-align:center;'>$nlic</td><td>$estado</td><td" . $estilo . "><b>$nomest</b></td><td>$chkhtml</td></tr>";
        $j++;
    }
	echo '</table>';
	echo "<table class='table2'><tr><td colspan='8' class='texto8'>Se Han Actualizado: " . number_format($cnt, 0, ',', '.') . " Observaciones de OC.</b></td></tr></table>";
    $html = $html . '</table>';
    echo '<script type="text/javascript">$(document).ready(function () {$("#tabladoc").tablesorter();});</script>';
}

echo"<input type='hidden' name='flag' id='flag' value='1'>";
echo"<textarea name='html' hidden='yes'>" . $html . "</textarea></form>";
mysql_close($link);
?>