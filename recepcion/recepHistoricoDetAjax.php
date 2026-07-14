<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
include("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
$buscar = $_POST['saldo'];
$comilladob = '"';
$comillasim = "'";
if (empty($buscar)) {
    buscar($buscar);
} else if (!empty($buscar)) {
    buscar($buscar);
} else {
    echo "<script>alert('Error, ingrese algun dato para Buscar');</script>";
}

function buscar($saldo) {
    global $comilladob;
    global $comillasim;
    if (strlen($saldo) > 20) {//si se escriben menos de 20 caracteres buscara
        $sql1 = $saldo;
        $_SESSION['query'] = $sql1;
    } else {
        $sql1 = "SELECT `recep_hist_numov`,`recep_hist_ocinter`,`recep_hist_chilecompra`,`recep_hist_obs`,`recep_hist_fechamov`,`recep_hist_numdoc`,`recep_hist_rutpv`,`recep_hist_numdoc`, 
		`recep_hist_anooc`,`recep_hist_anomov`,`recep_hist_user`,`recep_hist_det_codprd`,`recep_hist_det_glosa`,`recep_hist_det_cant`,`recep_hist_det_um`,`prov_nombre`,`prov_dv`, 
		`recep_hist_det_precio`,`prov_rut`,`tipodoc_glosa` FROM recep_historico_detalle RD left JOIN recep_historico RH ON RH.recep_hist_numov = RD.recep_hist_det_numov
        AND RD.recep_hist_det_ocint = RH.recep_hist_ocinter AND RD.recep_hist_det_anomov = RH.recep_hist_anomov INNER JOIN proveedores P ON P.prov_rut = RH.recep_hist_rutpv 
        INNER JOIN recep_historico_tipodoc RT ON RT.tipodoc_cod = RH.recep_hist_tipodoc  WHERE recep_hist_det_codprd LIKE '%" . $saldo . "%' OR recep_hist_det_glosa LIKE '%" . $saldo . "%' 
        OR recep_hist_numdoc LIKE '%" . $saldo . "%' OR recep_hist_numdoc LIKE '%" . $saldo . "%' OR recep_hist_chilecompra LIKE '%" . $saldo . "%' OR prov_nombre LIKE '%" . $saldo . "%' OR prov_rut LIKE '%" . $saldo . "%'";
    }
    $data = mysql_query($sql1);
    //echo $sql1;
    $_SESSION['query'] = $sql1;
    $contar = mysql_num_rows($data);
    if ($contar === 0) {
        echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran los items que cumplen con los filtros.'/>&nbsp;&nbsp;No hay datos para Mostrar</p>";
        $_SESSION['query'] = "";
    } else {
        if ($saldo == "") {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran todos los items que cumplen con los filtros.'/>&nbsp;&nbsp;Mostrando todos los Registros. (se han encontrado: <b>$contar</b> Resultados)</p>";
        } else {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran todos los items que cumplen con los filtros.'/>&nbsp;&nbsp;Se han encontrado <b>$contar</b> resultados para: *<b>" . $saldo . "</b>*</p>";
        }
        echo '<table class="table2" id="tabladoc">
         <thead><tr><th width="50">OC Mercadopúblico</th><th width="40">OC Interna</th><th width="40">Num. Movim.</th><th width="50">Fecha MOV.</th><th width="200">Proveedor</th><th width="50">N° Doc. Prov.</th><th width="50">Tipo Doc.</th><th width="50">Codigo</th><th width="150">Glosa</th><th width="30">U.M.</th><th width="30">Cant. Rec.</th><th width="30" data-sorter="false" data-filter="false">Acciones:</th></tr></thead>';
        while ($row = mysql_fetch_array($data)) {
            printf("<tr><td><b>%s</b></td><td>%s</td><td>%s</td><td>%s</td><td class='texto10'><b>%s</b></td><td>%s</td><td>%s</td><td><b>%s</b></td><td class='texto10'>%s</td><td><b>%s</b></td><td>%s</td><td>%s</td></tr>", $row["recep_hist_chilecompra"], $row["recep_hist_ocinter"], "<b title='Obs: " . $row["recep_hist_obs"] . "'>" . $row["recep_hist_numov"] . "</b>", date("d/m/Y H:i:s", strtotime($row["recep_hist_fechamov"])),"<a href=../edicion/modificarProveedor.php?id=" . $row["prov_rut"] . "&option=1 target='_blank' title='Ver detalle proveedor' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=540, width=800 left=300 top=100" . $comillasim . "); return false;" . $comilladob . ">" . number_format($row["prov_rut"], 0, '.', '.') . "-" . $row["prov_dv"] . "</a>"."&nbsp;&nbsp;". 
                    $row["prov_nombre"] ,$row["recep_hist_numdoc"],$row["tipodoc_glosa"], $row["recep_hist_det_codprd"], $row["recep_hist_det_glosa"], $row["recep_hist_det_um"], $row["recep_hist_det_cant"],"<a href=../reportes/recepHistoPDF.php?oc=" . $row["recep_hist_ocinter"] . "&nmov=" . $row["recep_hist_numov"] . "&anio=" . $row["recep_hist_anomov"] . " onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=560, width=980 left=20 top=10" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='editar oc' title='Generar Reporte' src='../img/pdf.png' width='25' height='25'></a>");
        }
    }
    echo '</table>';
    echo '<script type="text/javascript">$(document).ready(function () {$("#tabladoc").tablesorter();});</script>';
}

mysql_close($link);
?>