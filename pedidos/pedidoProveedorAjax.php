<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
include("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
$saldo = $_POST['consulta'];
$vertodo = $_POST["chkall"];
$chksaldo = $_POST["chksaldo"];
$chkres = $_POST["chkres"]; //ver recientes
$anio = $_POST["anio"];
$anio2 = $_POST["anio2"];
$comilladob = '"';
$comillasim = "'";

if ($chksaldo == "true") {
    $pend = " AND saldo_pendiente >0 ";
} else {
    $pend = "";
}
if ($anio != "") {
    $anio1 = " AND saldo_fechaoc BETWEEN '$anio' AND '$anio2' ";
}
if (empty($saldo) and $vertodo == "true") {
    buscar($saldo, $pend, $anio1, $anio, $anio2, $chkres);
}
if (!empty($saldo)) {
    buscar($saldo, $pend, $anio1, $anio, $anio2, $chkres);
}

function buscar($saldo, $pend, $anio1, $anio, $anio2, $chkres) {
    global $comilladob, $comillasim;
    $fecha1= date("Y-m-d",strtotime(date("Y-m-d")."- 3 days"));
    if ($chkres == "true") {
        $sql1 = "SELECT prov_nombre, prov_rut, prov_estado, prov_giro, prov_nomcontacto, prov_dv, prov_telefono, saldo_rutprov, saldo_pendiente, saldo_est, saldo_fechaoc FROM proveedores P, saldos WHERE NOT EXISTS(SELECT mov_fecha FROM movimiento M WHERE P.prov_rut = M.mov_rutprv AND mov_fecha > '$fecha1') AND prov_estado='1' AND prov_rut=saldo_rutprov " . $pend . $anio1 . " AND saldo_est=0 AND (prov_rut LIKE '%" . $saldo . "%' or prov_nombre LIKE '%" . $saldo . "%'or prov_giro LIKE '%" . $saldo . "%'or prov_nomcontacto LIKE '%" . $saldo . "%') GROUP BY prov_rut ORDER BY prov_nombre";
    } else {
        $sql1 = "SELECT prov_nombre, prov_rut, prov_estado, prov_giro, prov_nomcontacto, prov_dv, prov_telefono, saldo_rutprov, saldo_pendiente, saldo_est, saldo_fechaoc FROM proveedores, saldos  WHERE prov_estado='1' AND prov_rut=saldo_rutprov " . $pend . $anio1 . " AND saldo_est=0 AND (prov_rut LIKE '%" . $saldo . "%' or prov_nombre LIKE '%" . $saldo . "%'or prov_giro LIKE '%" . $saldo . "%'or prov_nomcontacto LIKE '%" . $saldo . "%') GROUP BY prov_rut ORDER BY prov_nombre";
    }
    //echo $sql1;
    $data = mysql_query($sql1);
    $_SESSION['query'] = $sql1;
    $contar = mysql_num_rows($data);
    if ($contar === 0) {
        echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran los documentos que cumplen con los filtros.'/>&nbsp;&nbsp;No hay datos para Mostrar...Revise si hay algun error en la escritura, o revise bien los filtros aplicados.</p><img class='imgnoenc' src='../img/nores.png' title='sin Resultados.'/>";
        $_SESSION['query'] = "";
    } else {
        if (strlen($saldo) == 0) {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran todos los productos que cumplen con los filtros.'/>&nbsp;&nbsp;Mostrando todos los Registros. (se han encontrado: <b>$contar</b> Resultados)</p>";
        } else {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran todos los productos que cumplen con los filtros.'/>&nbsp;&nbsp;Se han encontrado <b>$contar</b> resultados para: *<b>" . $saldo . "</b>*</p>";
        }
        echo '<table class="table2" id="tabla">
         <thead><tr><th width="100">RUT</th><th width="350">Razon Social</th><th width="200">Giro</th><th width="150">Contacto</th><th width="100">Telefono</th><th width="110">Ultimo Pedido Vigente</th><th width="80" data-sorter="false" data-filter="false">Acciones:</th></tr></thead>';
        while ($row = mysql_fetch_array($data)) {
            if ($anio != "" && $anio2 != "") {
                $fecha = "&fini=" . $anio . "&ffin=" . $anio2;
            }
            $rut = $row["prov_rut"];
            $sql3 = "SELECT mov_id, mov_fecha FROM movimiento WHERE mov_rutprv='$rut' AND mov_estado='1' ORDER BY mov_id DESC LIMIT 1";
            $result3 = mysql_query($sql3);
            $row3 = mysql_fetch_array($result3);
            $ped = $row3["mov_id"];
            $fped = date("d/m/Y", strtotime($row3["mov_fecha"]));
            if ($ped != "") {
                $ped = "<a href='../reportes/pedidoPDF.php?id=".$ped."' target='_blank' title='ver solicitud'>SOL-" . $ped . "</a><br><b class='texto11'>(" . $fped . ")</b>";
            }
            printf("<tr><td><b>%s</b></td><td align='left'>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td><td>%s</td><td><b>%s</b></td></tr>", "<a href=../edicion/modificarProveedor.php?id=" . $rut . "&option=1 target='_blank' title='Ver detalle proveedor' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=540, width=800 left=300 top=100" . $comillasim . "); return false;" . $comilladob . ">" . number_format($rut, 0, '.', '.') . "-" . $row["prov_dv"] . "</a>", $row["prov_nombre"], $row["prov_giro"], $row["prov_nomcontacto"], $row["prov_telefono"], $ped, "<a href=../pedidos/crearPedido.php?id=" . $rut . $fecha . "><img alt='generar pedido' title='Generar Pedido a: " . $row["prov_nombre"] . "' src='../img/oc.png' width='25' height='25'></a>&nbsp;&nbsp;|&nbsp;<a href=../reportes/pedidoProvdetalle.php?rutpv=" . $rut . $fecha."><img alt='Ver Pedidos' title='Ver Pedidos Generados a: " . $row["prov_nombre"] . "' src='../img/box.png' width='25' height='25'></a>");
        }
    }
    echo '</table>';

    echo "<br>";
    if ($contar > 10) {
        echo '<div id="pager" class="pagerform">
        <form>
            <img src="../js/images/first.png" class="first" style="cursor:pointer;" title="Ir a Página Inicial"/>
            <img src="../js/images/prev.png" class="prev" style="cursor:pointer;" title="Ir a Página Anterior"/>
            &nbsp;<input type="text" class="pagedisplay" size="45" readonly style="border:1px solid #808080; font-weight: bold;text-align: center;"/>
            <img src="../js/images/next.png" class="next" style="cursor:pointer;" title="Ir a Página Siguiente"/>
            <img src="../js/images/last.png" class="last" style="cursor:pointer;" title="Ir a Página Final"/>&nbsp;
            <select class="pagesize" style="border:1px solid #808080;" title="Cantidad de elementos Mostrados por Página">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="30">30</option>
                <option value="40">40</option>
                <option value="50">50</option>
                <option value="100">100</option>
		<option value="300" selected="selected">300</option>
                <option value="500">500</option>
            </select>
	<select class="gotoPage" title="Ir a Pagina"></select>
        </form>
    </div>';
    }
    echo '<script type="text/javascript">
        $(document).ready(function (){
            $("#tabla").tablesorter({
            }).tablesorterPager({container: $("#pager"),
		showProcessing: true,
		size: 300,
		output: "Mostrando Registros del {startRow} al {endRow} (Total: {totalRows})"
            });
        });
        </script>';
}

mysql_close($link);
?>