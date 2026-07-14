<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
ini_set('display_errors', false);
set_time_limit(3000);
ini_set("memory_limit", "1024M"); //tamaño de buffer
include("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
$buscar = $_POST['b'];
$vertodo = $_POST["chkall"];
$opstock = $_POST['tiposto']; //obtenemos tipo stock si se ha enviado
$anio = $_POST['anio'];
$anio2 = $_POST['anio2'];
if ($anio == "0") {
    echo '<script>alert("Error, debe seleccionar el Año...");</script>';
    exit();
}
switch ($opstock) {
    case 0:
        $stc = "";
        break;
    case 1:
        $stc = " AND S.stock_cantidad=0 ";
        break;
    case 2:
        $stc = " AND S.stock_cantidad>0 ";
        break;
    case 3:
        $stc = "AND prd_destacado=1 ";
        break;
    case 4:
        $niv = " INNER JOIN tiponivel T ON T.tiponivel_codigo= S.stock_codigo AND S.stock_cantidad <= T.tiponivel_critico ";
        $tp = " , tiponivel_critico ";
        break;
    case 5:
        $stc = "AND ROUND((PC.pac_cantidad / 12),0) > S.stock_cantidad ";
        $ver = 0;
        break;
    case 6:
        $stc = "AND ROUND((PC.pac_cantidad / 12),0) > S.stock_cantidad ";
        $ver = 1;
        break;
		case 7:
        $stc = "AND ROUND(S.stock_cantidad / (PC.pac_cantidad / 12),0) < 0.5 ";
        $ver = 1;
        break;
}
$comilladob = '"';
$comillasim = "'";
if (empty($buscar) and $vertodo == "true") {
    buscar($buscar, $anio, $anio2, $stc, $niv, $tp, $link, $ver);
}
if (!empty($buscar) and strlen($buscar) >= 3) {
    buscar($buscar, $anio, $anio2, $stc, $niv, $tp, $link, $ver);
}

function buscar($b, $anio, $anio2, $stc, $niv, $tp, $link, $ver) {
    global $comilladob;
    global $comillasim;
    $saldo = [];
    if ($ver == 1) {
        $sql = "SELECT prd_codigo, prd_fam, prd_glosa, prd_unimed, prd_codcm, prd_barcode, prd_cenabast, stock_cantidad, prd_precio, unimed_nombre, pac_codigo,pac_cantidad" . $tp . " 
        FROM productos P
        INNER JOIN stock S ON S.stock_codigo= P.prd_codigo
        INNER JOIN pac PC ON PC.pac_codigo= P.prd_codigo AND pac_ano='$anio' " . $stc . "
        INNER JOIN unimed U ON U.unimed_id=P.prd_unimed " . $niv . "
        INNER JOIN saldos Sd ON P.prd_codigo = Sd.saldo_codigo AND Sd.saldo_pendiente > 0 AND saldo_est = 0 AND saldo_fechaoc > '".$anio2."-01-01'    
        WHERE prd_estado=1 AND (prd_codigo LIKE '%" . $b . "%' or prd_glosa LIKE '%" . $b . "%'or prd_ref LIKE '%" . $b . "%'or prd_codcm LIKE '%" . $b . "%' or prd_barcode LIKE '%" . $b . "%' or prd_cenabast LIKE '%" . $b . "%' )
        GROUP BY prd_codigo ORDER BY prd_codigo";
    } else {
        $sql = "SELECT prd_codigo, prd_fam, prd_glosa, prd_unimed, prd_codcm, prd_barcode, prd_cenabast, stock_cantidad, prd_precio, unimed_nombre, pac_codigo,pac_cantidad" . $tp . " 
        FROM productos P
        INNER JOIN stock S ON S.stock_codigo= P.prd_codigo
        INNER JOIN pac PC ON PC.pac_codigo= P.prd_codigo AND pac_ano='$anio' " . $stc . "
        INNER JOIN unimed U ON U.unimed_id=P.prd_unimed " . $niv . "
        WHERE prd_estado=1 AND (prd_codigo LIKE '%" . $b . "%' or prd_glosa LIKE '%" . $b . "%'or prd_ref LIKE '%" . $b . "%'or prd_codcm LIKE '%" . $b . "%' or prd_barcode LIKE '%" . $b . "%' or prd_cenabast LIKE '%" . $b . "%' )
	GROUP BY prd_codigo ORDER BY prd_codigo";
    }
//echo $sql;
    $data = mysql_query($sql,$link);
    $_SESSION['query'] = $sql;
    $contar = mysql_num_rows($data);
    if ($contar === 0) {
        echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran los registros que cumplen con los filtros.'/>&nbsp;&nbsp;No hay datos para Mostrar" . $ocd . "</p>";
        $_SESSION['query'] = "";
    } else {
        if ($b == "") {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran todos los registros que cumplen con los filtros.'/>&nbsp;&nbsp;Mostrando todos los Registros. (se han encontrado: <b>$contar</b> Resultados)" . $ocd . "</p>";
        } else {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran todos los registros que cumplen con los filtros.'/>&nbsp;&nbsp;Se han encontrado <b>$contar</b> resultados para: *<b>" . $b . "</b>*" . $ocd . "</p>";
        }
        echo '<table class="table2" align="center" id="tabladoc">
         <thead><tr><th width="80">C&oacute;digo Producto</th><th>Glosa</th><th>Unidad Medida</th><th>Codigo Cenabast</th><th width="80">Stock Actual</th><th width="80">Stock Crítico</th><th width="80">Precio Neto</th><th width="80">PAC Mensual</th><th width="40">% Faltante:</th><th width="120" data-sorter="false" data-filter="false">Acciones:</th></tr></thead>';
        $res2 = mysql_query("SELECT saldo_codigo AS C, sum(saldo_pendiente) AS T FROM saldos WHERE  `saldo_est` = 0 group by saldo_codigo", $link);
        while ($row2 = mysql_fetch_array($res2)) {//llenar arreglo con los saldos y dejarlo preparado para consultar las oc con saldo
            $saldo[$row2["C"]] = $row2["T"];
        }
        mysql_free_result($res2);
        while ($row = mysql_fetch_array($data)) {
            $cod = $row["pac_codigo"];
            if ($saldo[$cod] == 0) {
                $codoc = "<img border='0' alt='error' title='No Hay Ordenes de compra para el codigo " . $cod . "' src='../img/error.png' width='20' height='20'>";
            } else {
                $codoc = "<a href=../pedidos/PedidoArticulo.php?codprd=" . $cod . "&op=1 target='_blank'><img border='0' alt='solicitar solo el articulo' title='Crear Solicitud' src='../img/pedido.png' width='18' height='20'></a>";
            }
            $tiponiv = $row["tiponivel_critico"];
            $pac = $row["pac_cantidad"] / 12;
            $porc = 100 - (($row["stock_cantidad"] / $pac) * 100);
            if ($pac == 0) {
                $porc = 0;
            }
            if ($porc > 0) {
                $col = " style='color:red;'";
            } else {
                $col = "";
            }
            $cart = "<a href=../reportes/cartolaArticulo.php?id=" . $cod . "&tipo=1 target='_blank'><img border='0' alt='movimiento articulo' title='ver movimientos articulo: " . $cod . "' src='../img/lupan.png' width='20' height='20'></a>";
            $modcod = "<a href=../edicion/ModificarArticulo.php?id=" . $cod . "&tipo=1 target='_blank' title='Editar Articulo' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=620, width=800 left=250 top=30" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='editar articulo' title='Editar articulo en ventana externa' src='../img/edit1.png' width='20' height='20'></a>";
            $nv = "<a href=../herramientas/buscarNivelStock.php?cod=" . $cod . "&op=1 target='_blank '><img border='0  ' alt='editar nivel' title='editar nivel stock' src='../img/edit.png' width='20' height='20'></a>";
            printf("<tr " . $veroc . "><td><b>%s</b></td><td align='left'>%s</td><td>%s</td><td>%s</td><td style='background-color:#F7A7F5;'><b>%s</b></td><td>%s</td><td>%s</td><td style='background-color:#DDFF33;'><i><b>%s</b></i></td><td align='center' $col>%s</td><td align='center'>%s</td></tr>", $cod, $row["prd_glosa"], $row["unimed_nombre"], $row["prd_cenabast"], $row["stock_cantidad"], $tiponiv, "$ " . number_format($row["prd_precio"], 0, ',', '.'), number_format($pac, 0, '.', ','), number_format($porc, 0, '.', ',') . "%", $codoc . "&nbsp;|&nbsp;" . $cart . "&nbsp;|&nbsp;" . $nv . "&nbsp;|&nbsp;" . $modcod);
            $sum = $sum + ($row["prd_precio"] * $pac);
            $sto = $sto + $pac;
        }
    }
    unset($saldo);
    echo '</table>';
    if ($contar == 0) {
        $total = "";
    } else {
        $total = "<table class='table2'><tr><td colspan='8' class='texto8'>Suma de PAC: " . number_format($sto, 0, ',', '.') . " Unidades.&nbsp;&nbsp; Total Valorizado con IVA:&nbsp;&nbsp;&nbsp;&nbsp;<b>$" . number_format(($sum * 1.19), 0, ',', '.') . "&nbsp;&nbsp;</b></td></tr></table>";
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