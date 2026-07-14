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
switch ($vertodo) {
    case 0:
        // echo "<script>alert('Error, ingrese algun dato para Buscar');</script>";
        break;
    case 1:
        $var = " T.suma = 0";
        break;
    case 2:
        $var = " T.suma > 0 AND T.rec > 0";
        break;
    case 3:
        $var = " T.rec = 0";
        break;
    case 4:
        $var = " T.suma >= 0";
        break;
}
if ($vertodo > 0) {
    buscar($buscar, $anio1, $var);
}

function buscar($saldo, $anio1, $var) {
    global $comilladob;
    global $comillasim;
    if (strlen($saldo) > 20) {//si se escriben menos de 20 caracteres buscara
        $sql1 = $saldo;
        $_SESSION['query'] = $sql1;
    } else {
        $sql1 = "SELECT saldo_oc, saldo_rutprov, T.suma, T.rec, prov_nombre, prov_dv, T.saldo_fechacarga, T.saldo_fechaoc
                FROM (SELECT saldo_oc,saldo_rutprov, saldo_fechaoc, saldo_fechacarga, SUM(saldo_pendiente) AS suma, SUM(saldo_recibido) AS rec FROM saldos  GROUP BY saldo_oc) AS T, proveedores
                WHERE " . $var . "  AND prov_rut=saldo_rutprov " . $anio1 . " AND (prov_nombre LIKE '%" . $saldo . "%' or prov_rut LIKE '%" . $saldo . "%' or saldo_oc LIKE '%" . $saldo . "%') "
                . "ORDER BY saldo_fechaoc DESC";
    }
    $data = mysql_query($sql1);
    $_SESSION['query'] = $sql1;
    $contar = mysql_num_rows($data);
    // echo $sql1;
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
         <thead><tr><th width="140">OC Mercadopúblico</th><th width="80">RUT</th><th width="250">Nombre Proveedor</th><th width="80">Fecha OC</th><th width="80">Fecha creación en SigBod</th><th width="80">Valor Total</th><th width="100">Estado Recepciones</th><th width="80">Estado Items</th><th width="100" data-sorter="false" data-filter="false">Acciones:</th></tr></thead>';
        while ($row = mysql_fetch_array($data)) {
            $oc = $row['saldo_oc'];
            $sql2 = "SELECT SUM(saldo_solicitado * saldo_preciounit) AS total, count(saldo_oc) AS cuenta, SUM(saldo_est) AS est FROM saldos WHERE saldo_oc='$oc'";
            $data2 = mysql_query($sql2);
            $row2 = mysql_fetch_array($data2);
            $suma = $row["suma"];
            $rec = $row["rec"];
            if ($suma == 0) {
                $estado = "Recepción Completa";
            } else if ($rec == 0) {
                $estado = "Sin Recepciones";
            } else {
                $estado = "Recepción Parcial";
            }
            $cuenta = $row2["cuenta"];
            $est = $row2["est"];
            if ($est == 0) {
                $estoc = "Vigentes";
                $style = " style='color:blue;'";
            } else if ($est == $cuenta) {
                if ($rec == 0) {
                    $estoc = "Anulada";
                    $style = " style='color:red;'";
                } else {
                    $estoc = "Saldo Pendiente Anulado";
                    $style = " style='color:red;'";
                }
            } else {
                $estoc = "Anulación Parcial";
                $style = " style='color:green;'";
            }
            printf("<tr><td><b>%s</b></td><td><b>%s</b></td><td>%s</td><td><b>%s</b></td><td>%s</td><td><b>$%s</b></td><td><b>%s</b></td><td><b" . $style . ">%s</b></td><td>%s</td></tr>", "<a href=https://www.mercadopublico.cl/PurchaseOrder/Modules/PO/DetailsPurchaseOrder.aspx?codigooc=" . $row['saldo_oc'] . " target='_blank' title='Ver OC en MP' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=640, width=980 left=200 top=50" . $comillasim . "); return false;" . $comilladob . ">" . $oc . "</a>", "<a href=../edicion/modificarProveedor.php?id=" . $row["saldo_rutprov"] . "&option=1 target='_blank' title='Ver detalle proveedor' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=540, width=800 left=300 top=100" . $comillasim . "); return false;" . $comilladob . ">" . number_format($row["saldo_rutprov"], 0, '.', '.') . "-" . $row["prov_dv"] . "</a>", $row["prov_nombre"], date("d/m/Y", strtotime($row["saldo_fechaoc"])), date("d/m/Y", strtotime($row["saldo_fechacarga"])), number_format(($row2["total"] * 1.19), 0, '', ','), $estado, $estoc, "<a href=../edicion/modificarSaldo.php?id=" . $row["saldo_rutprov"] . "&oc=" . $row["saldo_oc"] . "><img border='0' alt='editar oc' title='Editar OC: " . $row["saldo_oc"] . "' src='../img/edit1.png' width='25' height='25'></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=../reportes/ocPDF.php?id=" . $row["saldo_rutprov"] . "&oc=" . $row["saldo_oc"] . " target='_blank'><img border='0' alt='editar oc' title='Generar Reporte OC: " . $row["saldo_oc"] . "' src='../img/pdf.png' width='25' height='25'></a>");
        }
    }
    echo '</table>';
    echo '<script type="text/javascript">$(document).ready(function () {$("#tabladoc").tablesorter();});</script>';
}

mysql_close($link);
?>