<?php

$timeant = microtime(true); //calcular el tiempo de ejecucion, capturo tiempo inicial
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
include("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
$buscar = $_POST['saldo'];
$vertodo = $_POST["chkall"];
$comilladob = '"';
$comillasim = "'";
if (empty($buscar) and $vertodo == "true") {
    buscar($buscar);
} else if (!empty($buscar)) {
    buscar($buscar, $subdoc, $fact, $anio1, $fmod2);
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
        $sql1 = "SELECT `recep_hist_numov`,`recep_hist_ocinter`,`recep_hist_chilecompra`,`bodega_nombre`,`tipodoc_glosa`,`recep_hist_obs`,`recep_hist_fechamov`,
        `recep_hist_numdoc`,`recep_hist_rutpv`,`recep_hist_anooc`,`recep_hist_anomov`,`recep_hist_est_glosa`,`prov_rut`,`prov_nombre`,`prov_dv`
        FROM recep_historico RH INNER JOIN proveedores P ON P.prov_rut = RH.recep_hist_rutpv INNER JOIN bodegas B ON B.bodega_codigo = RH.recep_hist_bod
        INNER JOIN recep_historico_estado RE ON RE.recep_hist_est_cod = RH.recep_hist_estado INNER JOIN recep_historico_tipodoc RT ON RT.tipodoc_cod = RH.recep_hist_tipodoc 
        WHERE prov_nombre LIKE '%" . $saldo . "%' or prov_rut LIKE '%" . $saldo . "%' or recep_hist_ocinter LIKE '%" . $saldo . "%' or recep_hist_numdoc LIKE '%" . $saldo . "%' or recep_hist_numov LIKE '%" . $saldo . "%' or recep_hist_chilecompra LIKE '%" . $saldo . "%' or recep_hist_obs LIKE '%" . $saldo . "%'
        ORDER BY recep_hist_fechamov";
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
         <thead><tr><th width="50">OC Mercadopúblico</th><th width="40">OC Interna</th><th width="40">Num. Movim.</th><th width="40">Estado OC</th><th width="40">Bodega</th><th width="70">RUT</th><th width="250">Nombre Proveedor</th><th width="80">Fecha MOV.</th><th width="40">Año MOV.</th><th width="80">N° Documento</th><th width="80">Tipo Documento</th><th width="30" data-sorter="false" data-filter="false">Acciones:</th></tr></thead>';
        while ($row = mysql_fetch_array($data)) {
            printf("<tr><td><b>%s</b></td><td>%s</td><td>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td><td class='texto10'><b>%s</b></td><td>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td><td>%s</td></tr>", "<a href=https://www.mercadopublico.cl/PurchaseOrder/Modules/PO/DetailsPurchaseOrder.aspx?codigooc=" . $row["recep_hist_chilecompra"]." target='_blank' title='Ver OC en MP' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=640, width=980 left=200 top=50" . $comillasim . "); return false;" . $comilladob . ">" . $row["recep_hist_chilecompra"] . "</a>", $row["recep_hist_ocinter"], "<b title='Obs: " . $row["recep_hist_obs"] . "'>" . $row["recep_hist_numov"] . "</b>", $row["recep_hist_est_glosa"], $row["bodega_nombre"], "<a href=../edicion/modificarProveedor.php?id=" . $row["prov_rut"] . "&option=1 target='_blank' title='Ver detalle proveedor' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=540, width=800 left=300 top=100" . $comillasim . "); return false;" . $comilladob . ">" . number_format($row["prov_rut"], 0, '.', '.') . "-" . $row["prov_dv"] . "</a>", $row["prov_nombre"], date("d/m/Y H:i:s", strtotime($row["recep_hist_fechamov"])), $row["recep_hist_anomov"], $row["recep_hist_numdoc"], $row["tipodoc_glosa"], "<a href=../reportes/recepHistoPDF.php?oc=" . $row["recep_hist_ocinter"] . "&nmov=" . $row["recep_hist_numov"] . "&anio=" . $row["recep_hist_anomov"] . " onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=560, width=980 left=20 top=10" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='editar oc' title='Generar Reporte' src='../img/pdf.png' width='25' height='25'></a>");
        }
    }
    echo '</table>';
}
mysql_close($link);
$timedesp = microtime(true); //calculo tiempo final
$time = round($timedesp - $timeant, 2); //resto los tiempos y obtengo la demora de la ejecucucion
echo '<table class="table2a">';
echo '<tr><td class="texto8">La consulta ha tardado: ' . $time . ' Segundos</td></tr>';
echo '</table>';
echo '<script type="text/javascript">$(document).ready(function () {$("#tabladoc").tablesorter();});</script>';

?>