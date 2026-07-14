<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
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
    $est = "";
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
    buscar($buscar, $url, $pend,$anio1,$est,$noasig);
}
if (!empty($buscar)) {
    buscar($buscar, $url, $pend,$anio1,$est,$noasig);
}

function buscar($saldo, $url, $pend, $anio1,$est,$noasig) {
    $comilladob = '"';
    $comillasim = "'";
    if ($noasig == 1) {
        $sql1 = "SELECT * FROM saldos S WHERE NOT EXISTS (SELECT NULL FROM productos P WHERE  s.saldo_codigo=p.prd_codigo)".$pend.$anio1.$est;
    } else {
		$ark = explode(" ", $saldo); //inicio filtro anidado, llena arreglo con los caracteres a buscar, considera espacio como separador
        if (count($ark) <= 1) {
        $sql1 = "SELECT saldo_codigo,prd_glosa,prd_barcode,unimed_nombre,saldo_rutprov,prov_dv,prov_nombre,saldo_oc,saldo_pendiente,saldo_est,saldo_solicitado,saldo_recibido,saldo_preciounit,saldo_fechaoc,prd_precio "
                . "FROM proveedores, saldos, unimed, productos "
                . "WHERE prd_codigo=saldo_codigo AND prd_unimed=unimed_id AND saldo_rutprov=prov_rut ".$pend.$anio1.$est
                . "AND (saldo_codigo LIKE '%" . $ark[0] . "%' or prd_barcode LIKE '%" . $ark[0] . "%' or prd_glosa LIKE '%" . $ark[0] . "%'or prov_nombre LIKE '%" . $ark[0] . "%' or prov_rut LIKE '%" . $ark[0] . "%' OR saldo_oc LIKE '%" . $ark[0] . "%')"
                . "ORDER BY saldo_fechaoc DESC,saldo_oc";
		} else {
			$sql1 = "SELECT saldo_codigo,prd_glosa,prd_barcode,unimed_nombre,saldo_rutprov,prov_dv,prov_nombre,saldo_oc,saldo_pendiente,saldo_est,saldo_solicitado,saldo_recibido,saldo_preciounit,saldo_fechaoc,prd_precio "
                . "FROM proveedores, saldos, unimed, productos "
                . "WHERE prd_codigo=saldo_codigo AND prd_unimed=unimed_id AND saldo_rutprov=prov_rut ".$pend.$anio1.$est;
				for ($i = 0; $i <= count($ark); $i++) {
                if (!empty($ark[$i])) {
					$sql1.= "AND (saldo_codigo LIKE '%" . $ark[$i] . "%' or prd_barcode LIKE '%" . $ark[$i] . "%' or prd_glosa LIKE '%" . $ark[$i] . "%'or prov_nombre LIKE '%" . $ark[$i] . "%' or prov_rut LIKE '%" . $ark[$i] . "%' OR saldo_oc LIKE '%" . $ark[$i] . "%') ";
				}            
				}
				$sql1 .= "ORDER BY saldo_fechaoc DESC,saldo_oc";
        }
	}
    $data = mysql_query($sql1);
    //echo $sql1;
    $_SESSION['query'] = $sql1;
    $contar = mysql_num_rows($data);
    if ($contar === 0) {
        echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran los datos que cumplen con los filtros.'/>&nbsp;&nbsp;No hay datos para Mostrar...Revise si hay algun error en la escritura, o revise bien los filtros aplicados.</p><img class='imgnoenc' src='../img/nores.png' title='sin Resultados.'/>";
        $_SESSION['query'] = "";
    } else {
        if (strlen($saldo) == 0) {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran todos los productos que cumplen con los filtros.'/>&nbsp;&nbsp;Mostrando todos los Registros. (se han encontrado: <b>$contar</b> Resultados)</p>";
        } else {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran todos los productos que cumplen con los filtros.'/>&nbsp;&nbsp;Se han encontrado <b>$contar</b> resultados para: *<b>" . $saldo . "</b>*</p>";
        }
        echo '<table class="table2" align="center" id="tabla">
         <thead><tr><th width="60">Codigo</th><th width="190">Glosa</th><th width="40">U. M.</th><th width="180">Proveedor</th><th width="90">OC Mercadop.</th><th width="50">Saldo Solicitado</th><th width="50">Saldo Recibido</th><th width="50">Saldo Pendiente</th><th width="50">Precio en OC</th><th width="50">Fecha OC</th><th width="40">Estado Item</th><th width="90" data-sorter="false" data-filter="false">Acciones:</th></tr></thead>';
        while ($row = mysql_fetch_array($data)) {
			$estado=$row["saldo_est"];
			if ($estado == "0") {
				$es = "<b style='color:blue;'>Vigente</b>";
			} else {
				$es = "<b style='color:red;'>Nulo</b>";
			}
			$verdoc = "<a href=../herramientas/generarDocOC.php?id=" . $row["saldo_oc"] . "&rut=" . $row["saldo_rutprov"] . "&op=1 target='_blank' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=560, width=1320 left=10 top=10" . $comillasim . "); return false;" . $comilladob . "><img src='../img/fact.png' width='20' height='20' title='Ver Documentos Registrados'></a>";
            $cart = "<a href=../reportes/cartolaArticulo.php?id=" . $row["saldo_codigo"] . "&op=1 onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=480, width=1320 left=10 top=10" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='movimiento articulo' title='ver movimientos articulo: " . $row["saldo_codigo"] . "' src='../img/lupan.png' width='20' height='20'></a>&nbsp;|";
            $modoc ="<a href=../edicion/modificarSaldo.php?id=" . $row["saldo_rutprov"] . "&oc=" . $row["saldo_oc"] . "&op=1 onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=640, width=1320 left=10 top=10" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='Modificar Saldo' title='Modificar Saldo Orden de Compra' src='../img/edit.png' width='20' height='20'></a>&nbsp;|";
			printf("<tr><td><b>%s</b></td><td align='left'>%s</td><td>%s</td><td align='left'><b>%s</b></td><td><b>%s</b></td><td>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", $row["saldo_codigo"]." <a href='#' id='".$row["saldo_codigo"]."' onclick='buscaCod(this.id);' title='Buscar por este código'>▲</a>", $row["prd_glosa"], $row["unimed_nombre"], "<a href=../edicion/modificarProveedor.php?id=" . $row["saldo_rutprov"] . "&option=1 target='_blank' title='Ver detalle proveedor' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=540, width=800 left=300 top=100" . $comillasim . "); return false;" . $comilladob . ">" . number_format($row["saldo_rutprov"], 0, '.', '.') . "-" . $row["prov_dv"] . "</a>&nbsp; ". $row["prov_nombre"], $row["saldo_oc"], $row["saldo_solicitado"], $row["saldo_recibido"], $row["saldo_pendiente"],'$' . number_format($row["saldo_preciounit"], 0, '.', ','), date("d/m/Y", strtotime($row["saldo_fechaoc"])),$es, $modoc . $cart . $verdoc);
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
    if ($contar  > 10) {
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