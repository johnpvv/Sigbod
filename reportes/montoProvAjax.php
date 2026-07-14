<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
include("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
set_time_limit(900);
$buscar = $_POST['saldo'];
$vertodo = $_POST["chkall"];
$comilladob = '"';
$comillasim = "'";
$anio = $_POST["anio"];
$anio2 = $_POST["anio2"] . " 23:59:59";

if ($anio != "") {
    $anio1 = " AND saldo_fechaoc BETWEEN '$anio' AND '$anio2' ";
}
if (empty($buscar) and $vertodo == "true") {
    buscar($buscar, $anio1);
}
if (!empty($buscar)) {
    buscar($buscar, $anio1);
}

function buscar($saldo, $anio1) {
    global $comilladob;
    global $comillasim;
    global $anio;
    global $anio2;
    if (strlen($saldo) > 20) {//si se escriben menos de 20 caracteres buscara
        $sql1 = $saldo;
        $_SESSION['query'] = $sql1;
    } else {
        $sql1 = "SELECT saldo_rutprov,  prov_nombre, prov_dv, SUM(saldo_solicitado * saldo_preciounit) AS suma FROM saldos, proveedores WHERE prov_rut=saldo_rutprov " . $anio1 . " AND (prov_nombre LIKE '%" . $saldo . "%' or prov_rut LIKE '%" . $saldo . "%' or saldo_oc LIKE '%" . $saldo . "%') GROUP BY saldo_rutprov ORDER BY suma DESC";
    }
    $data = mysql_query($sql1);
    $_SESSION['query'] = $sql1;
    $contar = mysql_num_rows($data);
    //echo $sql1;
    if ($contar === 0) {
        echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran los documentos que cumplen con los filtros.'/>&nbsp;&nbsp;No hay datos para Mostrar</p>";
        $_SESSION['query'] = "";
    } else {
        if ($saldo == "") {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran los documentos que cumplen con los filtros.'/>&nbsp;&nbsp;(se han encontrado: <b>$contar</b> Resultados)</p>";
        } else {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran todos los documentos que cumplen con los filtros.'/>&nbsp;&nbsp;Se han encontrado <b>$contar</b> resultados para: *<b>" . $saldo . "</b>*</p>";
        }
        echo '<table id="tabladoc" class="table2">
         <thead><tr><th width="100">RUT</th><th width="300">Nombre Proveedor</th><th width="100">Valor Neto Transado</th><th width="100">Valor Total Transado</th><th width="50">N° de OCs Encontradas</th><th width="100" data-sorter="false" data-filter="false">Acciones:</th></tr></thead>';
        while ($row = mysql_fetch_array($data)) {
            $prov = $row['saldo_rutprov'];
            $sql2 = "SELECT COUNT(DISTINCT(saldo_oc)) AS total FROM saldos WHERE saldo_rutprov='$prov'" . $anio1 . " GROUP BY saldo_oc";
            $data2 = mysql_query($sql2);
            $cnt= mysql_num_rows($data2);
            printf("<tr><td><b>%s</b></td><td><b>%s</b></td><td>$ %s</td><td><b>$ %s</b></td><td>%s</td><td>%s</td></tr>", "<a href=../edicion/modificarProveedor.php?id=" . $row["saldo_rutprov"] . "&option=1 target='_blank' title='Ver detalle proveedor' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=540, width=800 left=300 top=100" . $comillasim . "); return false;" . $comilladob . ">" . number_format($row["saldo_rutprov"], 0, '.', '.') . "-" . $row["prov_dv"] . "</a>", $row["prov_nombre"], number_format($row["suma"], 0, '', ','), number_format(($row["suma"] * 1.19), 0, '', ','), $cnt, "<a href=../herramientas/saldoOc.php?rut=" . $row["saldo_rutprov"] . "&fini=" . $anio . "&ffin=".$anio2."><img border='0' alt='ver oc' title='ver OCS por detalle' src='../img/ped1.png' width='25' height='25'></a>&nbsp;|&nbsp;<a href=../herramientas/buscarOcSaldo.php?rut=" . $row["saldo_rutprov"] . "&fini=" . $anio . "&ffin=".$anio2."><img border='0' alt='ver oc por Montos' title='ver OCS por Montos' src='../img/report.png' width='24' height='25'></a>");
        }
    }
    echo '</table>';
    echo '<script type="text/javascript">$(document).ready(function () {$("#tabladoc").tablesorter();});</script>';
}

mysql_close($link);
?>