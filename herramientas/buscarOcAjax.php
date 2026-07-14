<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
include("../include/conn.php");
set_time_limit(900);
$link = Conectarse(); //Variable de coneccion
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
    if (strlen($saldo) > 20) {//si se escriben menos de 20 caracteres buscara
        $sql1 = $saldo;
        $_SESSION['query'] = $sql1;
   } else {
        $sql1 = "SELECT DISTINCT saldo_rutprov,prov_dv,prov_nombre,saldo_oc,saldo_fechaoc, saldo_fechacarga "
                . "FROM proveedores, saldos "
                . "WHERE prov_estado='1' "
                . "AND saldo_rutprov=prov_rut " . $anio1
                . "AND (prov_nombre LIKE '%" . $saldo . "%' or prov_rut LIKE '%" . $saldo . "%' or saldo_oc LIKE '%" . $saldo . "%')"
                . "GROUP BY saldo_oc ORDER BY saldo_fechaoc DESC,saldo_oc ";
    }
	 /*} else {
        $sql1 = "SELECT DISTINCT saldo_rutprov,prov_dv,prov_nombre,saldo_oc,saldo_fechaoc, saldo_fechacarga "
                . "FROM proveedores, saldos, oc_obs "
                . "WHERE prov_estado='1' "
                . "AND saldo_rutprov=prov_rut AND saldo_oc = oc_obs_oc " . $anio1
                . "AND (prov_nombre LIKE '%" . $saldo . "%' or prov_rut LIKE '%" . $saldo . "%' or saldo_oc LIKE '%" . $saldo . "%' or oc_obs_det LIKE '%" . $saldo . "%')"
                . "GROUP BY saldo_oc ORDER BY saldo_fechaoc DESC,saldo_oc ";
    }*/
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
         <thead><tr><th width="160">OC Mercadopúblico</th><th width="100">RUT</th><th width="250">Nombre Proveedor</th><th width="80">Fecha OC</th><th width="80">Fecha creación en SigBod</th><th width="80">Valor Total</th><th width="100" data-sorter="false" data-filter="false">Acciones:</th></tr></thead>';
        while ($row = mysql_fetch_array($data)) {
            $oc = $row['saldo_oc'];
            $sql2 = "SELECT SUM(saldo_solicitado * saldo_preciounit) AS total FROM saldos WHERE saldo_oc='$oc'";
            $data2 = mysql_query($sql2);
            $row2 = mysql_fetch_array($data2);
            printf("<tr><td><b>%s</b></td><td><b>%s</b></td><td>%s</td><td><b>%s</b></td><td>%s</td><td><b>$%s</b></td><td>%s</td></tr>", "<a href=https://www.mercadopublico.cl/PurchaseOrder/Modules/PO/DetailsPurchaseOrder.aspx?codigooc=" . $row['saldo_oc'] . " target='_blank' title='Ver OC en MP' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=640, width=980 left=200 top=50" . $comillasim . "); return false;" . $comilladob . ">" . $oc . "</a>", "<a href=../edicion/modificarProveedor.php?id=" . $row["saldo_rutprov"] . "&option=1 target='_blank' title='Ver detalle proveedor' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=540, width=800 left=300 top=100" . $comillasim . "); return false;" . $comilladob . ">" . number_format($row["saldo_rutprov"], 0, '.', '.') . "-" . $row["prov_dv"] . "</a>", $row["prov_nombre"], date("d/m/Y", strtotime($row["saldo_fechaoc"])), date("d/m/Y", strtotime($row["saldo_fechacarga"])), number_format(($row2["total"] * 1.19), 0, '', ''), "<a href=../edicion/modificarSaldo.php?id=" . $row["saldo_rutprov"] . "&oc=" . $row["saldo_oc"] . "><img border='0' alt='editar oc' title='Editar OC: " . $row["saldo_oc"] . "' src='../img/edit.png' width='25' height='25'></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=../edicion/borrarOc.php?oc=" . $row["saldo_oc"] . " onclick='return borrar()'><img border='0' alt='borrar oc' title='Borrar OC: " . $row["saldo_oc"] . "' src='../img/trash.png' width='23' height='23'></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=../reportes/ocPDF.php?id=" . $row["saldo_rutprov"] . "&oc=" . $row["saldo_oc"] . " target='_blank'><img border='0' alt='editar oc' title='Generar Reporte OC: " . $row["saldo_oc"] . "' src='../img/pdf.png' width='25' height='25'></a>");
        }
    }
    echo '</table>';
    echo '<script type="text/javascript">$(document).ready(function () {$("#tabladoc").tablesorter();});</script>';
}

mysql_close($link);
?>