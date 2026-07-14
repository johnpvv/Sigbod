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
} else if (!empty($buscar)) {
    buscar($buscar, $anio1);
} else {
    echo "<script>alert('Error, ingrese algun dato para Buscar');</script>";
}

function buscar($saldo, $anio1) {
    global $comilladob;
    global $comillasim;
    $sql1 = "SELECT D.doc_noc, SUM(doc_montototal) AS SUMA, prov_rut, prov_nombre, prov_dv, IFNULL((SELECT round(SUM((saldo_solicitado * saldo_preciounit)*1.19),0) FROM saldos S "
            . "WHERE S.saldo_oc = D.doc_noc " . $anio1 . "),0) AS total FROM documento D, proveedores WHERE D.doc_estado <>'Anulado' AND prov_rut=D.doc_rutpv AND (prov_nombre LIKE '%" . $saldo . "%' OR prov_rut LIKE '%" . $saldo . "%' OR doc_noc LIKE '%" . $saldo . "%')"
            . "GROUP BY D.doc_noc HAVING SUM(D.doc_montototal) > total AND total > 0";
    $data = mysql_query($sql1);
    $_SESSION['queryoc'] = $sql1;
    $contar = mysql_num_rows($data);
    //echo $sql1;
    if ($contar === 0) {
        echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran los documentos que cumplen con los filtros.'/>&nbsp;&nbsp;No hay datos para Mostrar</p>";
        $_SESSION['queryoc'] = "";
    } else {
        if ($saldo == "") {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran los documentos que cumplen con los filtros.'/>&nbsp;&nbsp;Se han encontrado: <b>$contar</b> Resultados</p>";
        } else {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran todos los documentos que cumplen con los filtros.'/>&nbsp;&nbsp;Se han encontrado <b>$contar</b> resultados para: *<b>" . $saldo . "</b>*</p>";
        }
        echo '<table id="tabladoc" class="table2">
         <thead><tr><th width="160">OC Mercadopúblico</th><th width="100">RUT</th><th width="250">Nombre Proveedor</th><th width="80">Fecha OC</th><th width="80">Valor Total OC</th><th width="100">Valorizado Documentos</th><th width="80">Diferencia Monetaria</th><th width="100" data-sorter="false" data-filter="false">Acciones:</th></tr></thead>';
        while ($row = mysql_fetch_array($data)) {
            $oc = $row['doc_noc'];
            $sql2 = "SELECT SUM(saldo_solicitado * saldo_preciounit) AS totaloc, saldo_oc, saldo_rutprov, saldo_fechaoc FROM saldos WHERE saldo_oc='$oc'";
            $data2 = mysql_query($sql2);
            $row2 = mysql_fetch_array($data2);
            $suma = $row["SUMA"];
            $valoroc = $row2["totaloc"] * 1.19;
            $dif = $suma - $valoroc;
            printf("<tr><td><b>%s</b></td><td><b>%s</b></td><td>%s</td><td><b>%s</b></td><td><b>$%s</b></td><td><b>$%s</b></td><td><b>$%s</b></td><td>%s</td></tr>", "<a href=https://www.mercadopublico.cl/PurchaseOrder/Modules/PO/DetailsPurchaseOrder.aspx?codigooc=" . $oc . " target='_blank' title='Ver OC en MP' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=640, width=980 left=200 top=50" . $comillasim . "); return false;" . $comilladob . ">" . $oc . "</a>", "<a href=../edicion/modificarProveedor.php?id=" . $row["prov_rut"] . "&option=1 target='_blank' title='Ver detalle proveedor' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=540, width=800 left=300 top=100" . $comillasim . "); return false;" . $comilladob . ">" . number_format($row["prov_rut"], 0, '.', '.') . "-" . $row["prov_dv"] . "</a>", $row["prov_nombre"], date("d/m/Y", strtotime($row2["saldo_fechaoc"])), number_format(($valoroc), 0, '', ','), number_format(($suma), 0, '', ','), number_format(($dif), 0, '', ','), "<a href=../edicion/modificarSaldo.php?id=" . $row["prov_rut"] . "&oc=" . $oc . "><img border='0' alt='editar oc' title='Editar OC: " . $oc . "' src='../img/edit1.png' width='25' height='25'></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=../reportes/ocPDF.php?id=" . $row["prov_rut"] . "&oc=" . $oc . " target='_blank'><img border='0' alt='editar oc' title='Generar Reporte OC: " . $oc . "' src='../img/pdf.png' width='25' height='25'></a>");
        }
    }
    echo '</table>';
    echo '<script type="text/javascript">$(document).ready(function () {$("#tabladoc").tablesorter();});</script>';
}

mysql_close($link);
?>