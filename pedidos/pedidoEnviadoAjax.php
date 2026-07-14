<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
include("../include/conn.php");
$link = Conectarse(); //Variable de conexion
$buscar = $_POST['b'];
$vertodo = $_POST["chkall"];
$val = $_POST["chkval"];
$anio = $_POST["anio"];
$anio2 = $_POST["anio2"] . " 23:59:59";
$comilladob = '"';
$comillasim = "'";
if ($val == "true") {
    $val = " AND mov_estado=1 ";
} else {
    $val = "";
}
if ($anio != "") {
    $anio1 = " AND mov_fecha BETWEEN '$anio' AND '$anio2' ";
}
if (empty($buscar) and $vertodo == "true") {
    buscar($buscar, $val, $anio1);
} else if (!empty($buscar) and strlen($buscar) >= 3) {
    buscar($buscar, $val, $anio1);
} else {
    echo "<script>alert('Error, ingrese algun dato para Buscar');</script>";
}

function buscar($b, $val, $anio1) {
    global $comilladob;
    global $comillasim;
    $sql1 = "SELECT mov_id, mov_estado, prov_rut, prov_dv, prov_nombre, prov_nomcontacto, mov_fecha, mov_cuenta, mov_valorneto, mov_observacion, estado_nombre FROM proveedores, movimiento, estado WHERE (mov_rutprv=prov_rut AND mov_estado=estado_id" . $val . $anio1 . ") AND (prov_rut LIKE '%" . $b . "%' OR prov_nombre LIKE '%" . $b . "%' OR prov_giro LIKE '%" . $b . "%' OR prov_nomcontacto LIKE '%" . $b . "%' OR mov_id LIKE '%" . $b . "%' OR mov_observacion LIKE '%" . $b . "%') ORDER BY mov_id DESC";
    $data = mysql_query($sql1);
    $_SESSION['query'] = $sql1;
    $contar = mysql_num_rows($data);
    if ($contar === 0) {
        echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran los pedidos que cumplen con los filtros.'/>&nbsp;&nbsp;No hay datos para Mostrar...Revise si hay algun error en la escritura, o revise bien los filtros aplicados.</p><img class='imgnoenc' src='../img/nores.png' title='sin Resultados.'/>";
        $_SESSION['query'] = "";
    } else {
        if ($b == "") {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran las solicitudes generadas y enviadas.'/>&nbsp;&nbsp;Mostrando todos los Registros. (se han encontrado: <b>$contar</b> Resultados)</p>";
        } else {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran las solicitudes generadas y enviadas.'/>&nbsp;&nbsp;Se han encontrado <b>$contar</b> resultados para: *<b>" . $b . "</b>*</p>";
        }
        echo '<table class="table2" id="tabladoc">';
        echo '<thead><tr><th>Folio</th><th width="100">RUT</th><th width="180">Razon Social</th><th>Contacto</th><th>Fecha Movimiento</th><th>Articulos Solicitados</th><th>Valor Neto</th><th>Observaciones</th><th>Estado Solicitud</th><th width="70" data-sorter="false" data-filter="false">Acciones:</th></tr></thead>';
        while ($row = mysql_fetch_array($data)) {
            $estado = $row["mov_estado"];
            if ($estado == "0") {
                $estado = " class='color_rojo'";
            } else {
                $estado = " class='texto5'";
            }
            printf("<tr><td><b>%s</b></td><td><b>%s</b></td><td align='left'>%s</td><td><b>%s</b></td><td><b>%s</b></td><td><b>%s</b></td><td>%s</td><td>%s</td><td" . $estado . ">%s</td><td>%s</td></tr>", $row["mov_id"], "<a href=../edicion/modificarProveedor.php?id=" . $row["prov_rut"] . "&option=1 target='_blank' title='Ver detalle proveedor' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=550, width=800 left=300 top=100" . $comillasim . "); return false;" . $comilladob . ">" . number_format($row["prov_rut"], 0, '.', '.') . "-" . $row["prov_dv"] . "</a>", $row["prov_nombre"], $row["prov_nomcontacto"], date("d/m/Y H:i:s", strtotime($row["mov_fecha"])), $row["mov_cuenta"], '$' . number_format($row["mov_valorneto"], 0, '.', ','), $row["mov_observacion"], $row["estado_nombre"], "<a href=../reportes/pedidoPDF.php?id=" . $row["mov_id"] . " target='_blank'><img border='0' alt='imprimir reporte' title='imprimir reporte pedido a: " . $row["prov_nombre"] . "' src='../img/pdf.png' width='27' height='25'></a>"
                    . "&nbsp;|&nbsp;<a href=../pedidos/editarPedido.php?id=" . $row["mov_id"] . "><img border='0' alt='editar articulo' title='Editar Solicitud N° " . $row["mov_id"] . "' src='../img/edit.png' width='25' height='25'></a>");
        }
        echo '</table>';
    }

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
            $("#tabladoc").tablesorter({
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