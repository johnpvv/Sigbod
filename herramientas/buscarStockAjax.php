<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
include("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
$buscar = $_POST['b'];
$chk = $_POST['chk'];
$vertodo = $_POST["chkall"];
$chkver = $_POST['chkver'];
$anio = $_POST["anio"];
$anio2 = $_POST["anio2"] . " 23:59:59";
$comilladob = '"';
$comillasim = "'";
$_SESSION['query'] = "";
if ($chkver == "true") {
    $noasig = 1;
} else {
    $noasig = 0;
}
if ($chk == "true") {
    $checktipo = " AND S.stock_cantidad>0 ";
} else {
    $checktipo = "";
}
if ($anio != "") {
    $anio1 = "AND stock_fecha BETWEEN '$anio' AND '$anio2' ";
}
if (empty($buscar) and $vertodo == "true") {
    buscar($buscar, $checktipo, $noasig, $anio1);
}
if (!empty($buscar) and strlen($buscar) >= 3) {
    buscar($buscar, $checktipo, $noasig, $anio1);
}

function buscar($b, $checktipo, $noasig, $anio1) {
    global $comilladob;
    global $comillasim;
    if ($noasig == 1) {
        $sql = "SELECT * FROM stock S WHERE NOT EXISTS (SELECT NULL FROM productos P WHERE  s.stock_codigo=p.prd_codigo)" . $checktipo . $anio1;
    } else {
        $sql = "SELECT prd_codigo, prd_fam, prd_glosa, prd_unimed, prd_codcm, prd_barcode, prd_cenabast, stock_cantidad, stock_codigo, stock_fecha, prd_precio, unimed_nombre FROM productos P 
        INNER JOIN stock S ON S.stock_codigo= P.prd_codigo " . $checktipo . $anio1 . " 
        INNER JOIN unimed U ON U.unimed_id=P.prd_unimed  WHERE prd_estado=1 AND (prd_codigo LIKE '%" . $b . "%' or prd_glosa LIKE '%" . $b . "%'or prd_ref LIKE '%" . $b . "%'or prd_codcm LIKE '%" . $b . "%' or prd_barcode LIKE '%" . $b . "%' or prd_cenabast LIKE '%" . $b . "%' )";
    }
    $data = mysql_query($sql);
    $_SESSION['query'] = $sql;
    $contar = mysql_num_rows($data);
    if ($contar === 0) {
        echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran los documentos que cumplen con los filtros.'/>&nbsp;&nbsp;No hay datos para Mostrar...Revise si hay algun error en la escritura, o revise bien los filtros aplicados.</p><img class='imgnoenc' src='../img/nores.png' title='sin Resultados.'/>";
        $_SESSION['query'] = "";
    } else {
        if ($b == "") {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran todos los documentos que cumplen con los filtros.'/>&nbsp;&nbsp;Mostrando todos los Registros. (se han encontrado: <b>$contar</b> Resultados)</p>";
        } else {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran todos los documentos que cumplen con los filtros.'/>&nbsp;&nbsp;Se han encontrado <b>$contar</b> resultados para: *<b>" . $b . "</b>*</p>";
        }
        echo '<table class="table2" align="center" id="tabladoc">
         <thead><tr>
             <th width="80">C&oacute;digo Producto</th>
             <th>Glosa</th>
             <th>Unidad Medida</th>
             <th>Codigo C.M.</th>
             <th>Codigo Cenabast</th>
             <th width="80">Stock Actual</th>
             <th width="120">Precio Neto</th>
             <th width="80">Fecha Modif.</th>
             <th width="60" data-sorter="false" data-filter="false">Acciones:</th>
         </tr></thead>';
        while ($row = mysql_fetch_array($data)) {
            printf("<tr><td><b>%s</b></td><td align='left'>%s</td><td>%s</td><td>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td><td><i><b>%s</b></i></td><td align='center'>%s</td></tr>", $row["stock_codigo"], $row["prd_glosa"], $row["unimed_nombre"], '<a href="http://www.mercadopublico.cl/TiendaFicha/Ficha?idProducto=' . $row["prd_codcm"] . '" target="_blank">' . $row["prd_codcm"] . '</a>', $row["prd_cenabast"], $row["stock_cantidad"], "$ " . number_format($row["prd_precio"], 2, ',', '.'), date("d/m/Y", strtotime($row["stock_fecha"])), "<a href=../edicion/ModificarArticulo.php?id=" . $row["stock_codigo"] . "&tipo=1 target='_blank' title='Editar Articulo' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=620, width=800 left=250 top=30" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='editar articulo' title='Editar articulo en ventana externa' src='../img/edit1.png' width='25' height='25'></a>&nbsp;"
                    . "<a href=../edicion/modificarStock.php?id=" . $row["stock_codigo"] . "&tipo=1 target='_blank' title='Editar Stock' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=240, width=600 left=300 top=80" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='editar stock' title='Editar stock en ventana externa' src='../img/edit.png' width='25' height='25'></a>");

            $sum = $sum + ($row["prd_precio"] * $row["stock_cantidad"]);
            $sto = $sto + $row["stock_cantidad"];
        }
    }
    echo '</table>';
    if ($contar == 0) {
        $total = "";
    } else {
        $total = "<table class='table2'><tr><td colspan='8' class='texto8'>Suma de Stock: " . number_format($sto, 0, ',', '.') . " Unidades.&nbsp;&nbsp; Total Valorizado con IVA:&nbsp;&nbsp;&nbsp;&nbsp;<b>$" . number_format(($sum * 1.19), 2, ',', '.') . "&nbsp;&nbsp;</b></td></tr></table>";
    }
    echo $total;
    echo '<script type="text/javascript">$(document).ready(function () {$("#tabladoc").tablesorter();});</script>';
}

mysql_close($link);
?>