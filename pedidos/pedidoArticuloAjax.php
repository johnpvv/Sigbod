<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
include("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
$buscar = $_POST['saldo'];
$vertodo = $_POST["chkall"];
$chksaldo = $_POST["chksaldo"];
$chkest = $_POST["chkest"];
$chknul = $_POST["chknul"];
$anio=$_POST["anio"];
$anio2=$_POST["anio2"]." 23:59:59";
$opc = $_POST["opc"];
if ($opc == "1") {
    $url = "&op=1";
} else {
    $url = "";
}
if ($chknul == "true") {
    $noasig = 1;
} else {
    $noasig = 0;
}
if ($chkest == "true") {
    $est = " AND saldo_est=1 ";
} else {
    $est = " AND saldo_est=0 ";
}

if($anio!=""){
	$anio1= " AND saldo_fechaoc BETWEEN '$anio' AND '$anio2' ";
}
if ($chksaldo == "true") {
    $pend = " AND saldo_pendiente >0 ";
} else {
    $pend = "";
}
if (empty($buscar) and $vertodo == "true") {
    buscar($buscar, $url, $pend,$anio1,$anio,$anio2,$est,$noasig);
}
if (!empty($buscar)) {
    buscar($buscar, $url, $pend,$anio1,$anio,$anio2,$est,$noasig);
}

function buscar($saldo, $url, $pend, $anio1,$anio,$anio2,$est,$noasig) {
    $comilladob = '"';
    $comillasim = "'";
    if ($noasig == 1) {
        $sql1 = "SELECT * FROM saldos S WHERE NOT EXISTS (SELECT NULL FROM productos P WHERE  s.saldo_codigo=p.prd_codigo)".$pend.$anio1.$est;
    } else {
        $sql1 = "SELECT saldo_codigo,prd_glosa,prd_barcode,unimed_nombre,saldo_rutprov,prov_dv,prov_nombre,saldo_oc,saldo_pendiente,saldo_preciounit,saldo_fechaoc,prd_precio "
                . "FROM proveedores, saldos, unimed, productos "
                . "WHERE (prov_estado='1' AND prd_estado='1') AND prd_codigo=saldo_codigo AND prd_unimed=unimed_id AND saldo_rutprov=prov_rut ".$pend.$anio1.$est
                . "AND (saldo_codigo LIKE '%" . $saldo . "%' or prd_barcode LIKE '%" . $saldo . "%' or prd_codcm LIKE '%" . $saldo . "%' or prd_glosa LIKE '%" . $saldo . "%'or prov_nombre LIKE '%" . $saldo . "%' or prov_rut LIKE '%" . $saldo . "%' OR saldo_oc LIKE '%" . $saldo . "%')"
                . "ORDER BY saldo_fechaoc DESC,saldo_oc";
    }
    $data = mysql_query($sql1);
   // echo $sql1;
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
        echo '<table class="table2" align="center" id="tabla">
         <thead><tr><th width="70">Codigo</th><th width="250">Glosa</th><th width="50">U. M.</th><th width="80">RUT</th><th width="220">Nombre Proveedor</th><th width="130">OC Mercadop.</th><th width="50">Saldo Pendiente</th><th width="50">Fecha OC</th><th width="120" data-sorter="false" data-filter="false">Acciones:</th></tr></thead>';
        while ($row = mysql_fetch_array($data)) {
            if ($anio != "" && $anio2 != "") {
                $fecha = "&fini=" . $anio . "&ffin=" . $anio2;
            }
            if ($row["saldo_pendiente"] == 0) {
                $link1 = "<img border='0' alt='error' title='No Hay Saldo suficiente en esta OC' src='../img/exclam.png' width='20' height='20'>";
                $link2 = "&nbsp;<img border='0' alt='error' title='Sin Saldo para solicitar este producto' src='../img/error.png' width='20' height='20'>";
            } else {
                $link1 = "<a href=../pedidos/crearPedido.php?id=" . $row["saldo_rutprov"] . $url . $fecha."><img border='0' alt='generar pedido' title='Generar Pedido a: " . $row["prov_nombre"] . "' src='../img/mano.png' width='20' height='20'></a>";
                $link2 = "<a href=../pedidos/crearPedido.php?id=" . $row["saldo_rutprov"] . "&oc=" . $row["saldo_oc"] . $url . $fecha."><img border='0' alt='solicitar solo el articulo' title='Solicitar solo el articulo: " . $row["saldo_codigo"] . "' src='../img/transport.png' width='20' height='20'></a>";
            }
            $cart = "<a href=../reportes/cartolaArticulo.php?id=" . $row["saldo_codigo"] . "&op=1".$fecha." onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=480, width=1320 left=10 top=50" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='movimiento articulo' title='ver movimientos articulo: " . $row["saldo_codigo"] . "' src='../img/lupan.png' width='20' height='20'></a>";
            printf("<tr><td><b>%s </b></td><td align='left'>%s</td><td>%s</td><td><b>%s</b></td><td><b>%s</b></td><td>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td></tr>", $row["saldo_codigo"]." <a href='#' id='".$row["saldo_codigo"]."' onclick='buscaCod(this.id);' title='Buscar por este código'>▲</a>", $row["prd_glosa"], $row["unimed_nombre"], "<a href=../edicion/modificarProveedor.php?id=" . $row["saldo_rutprov"] . "&option=1 target='_blank' title='Ver detalle proveedor' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=540, width=800 left=300 top=100" . $comillasim . "); return false;" . $comilladob . ">" . number_format($row["saldo_rutprov"], 0, '.', '.') . "-" . $row["prov_dv"] . "</a>", $row["prov_nombre"], $row["saldo_oc"], $row["saldo_pendiente"], date("d/m/Y", strtotime($row["saldo_fechaoc"])), $link1 . "&nbsp;|" . $link2 . "|&nbsp;<a href=../edicion/modificarSaldo.php?id=" . $row["saldo_rutprov"] . "&oc=" . $row["saldo_oc"] . "><img border='0' alt='Modificar Saldo' title='Modificar Saldo Orden de Compra' src='../img/edit.png' width='20' height='20'></a>&nbsp;|" . $cart);
            $sum = $sum + $row["saldo_pendiente"];
        }
    }
    echo '</table>';
    if ($contar == 0) {
        $total = "";
    } else {
        $total = "<table class='table2'><tr><td colspan='8' class='texto8'>Total Unidades Pendientes: " . number_format($sum, 0, ',', '.') . " Unidades.</td></tr></table>";
    }
    echo $total;
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
                <option value="100" selected="selected">100</option>
		<option value="300">300</option>
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
		size: 100,
		output: "Mostrando Registros del {startRow} al {endRow} (Total: {totalRows})"
            });
        });
        </script>';
}

mysql_close($link);
?>