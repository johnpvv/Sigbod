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
$anio = $_POST['anio'];
if($anio=="0"){
    echo '<script>alert("Error, debe seleccionar el Año...");</script>';
    exit();
}
$id = $_POST['id'];
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
if (empty($buscar) and $vertodo == "true") {
    buscar($buscar, $checktipo, $noasig, $anio, $id);
}
if (!empty($buscar) and strlen($buscar) >= 3) {
    buscar($buscar, $checktipo, $noasig, $anio, $id);
}

function buscar($b, $checktipo, $noasig, $anio, $id) {
    global $comilladob;
    global $comillasim;
    if ($noasig == 1) {
        $sql = "SELECT * FROM pac S WHERE NOT EXISTS (SELECT NULL FROM productos P WHERE  s.pac_codigo=p.prd_codigo)" . $checktipo . " AND pac_ano='$anio'";
    } else {
        $sql = "SELECT prd_codigo, prd_fam, prd_glosa, prd_unimed, prd_codcm, prd_barcode, prd_cenabast, stock_cantidad, stock_codigo, prd_precio, unimed_nombre, pac_codigo,pac_cantidad FROM productos P 
        INNER JOIN stock S ON S.stock_codigo= P.prd_codigo " . $checktipo . " 
        INNER JOIN pac PC ON PC.pac_codigo= P.prd_codigo AND pac_ano='$anio' 
        INNER JOIN unimed U ON U.unimed_id=P.prd_unimed  WHERE prd_estado=1 AND (prd_codigo LIKE '%" . $b . "%' or prd_glosa LIKE '%" . $b . "%'or prd_ref LIKE '%" . $b . "%'or prd_codcm LIKE '%" . $b . "%' or prd_barcode LIKE '%" . $b . "%' or prd_cenabast LIKE '%" . $b . "%' )";
    }
    //echo $sql;
    $data = mysql_query($sql);
    $_SESSION['query'] = $sql;
    $contar = mysql_num_rows($data);
    if ($contar === 0) {
        echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran los registros que cumplen con los filtros.'/>&nbsp;&nbsp;No hay datos para Mostrar</p>";
        $_SESSION['query'] = "";
    } else {
        if ($b == "") {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran todos los registros que cumplen con los filtros.'/>&nbsp;&nbsp;Mostrando todos los Registros. (se han encontrado: <b>$contar</b> Resultados)</p>";
        } else {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran todos los registros que cumplen con los filtros.'/>&nbsp;&nbsp;Se han encontrado <b>$contar</b> resultados para: *<b>" . $b . "</b>*</p>";
        }
        echo '<table class="table2" align="center" id="tabladoc">
         <thead><tr><th width="80">C&oacute;digo Producto</th><th>Glosa</th><th>Unidad Medida</th><th>Codigo Cenabast</th><th width="80">Stock Actual</th><th width="120">Precio Neto</th><th width="80">PAC Total</th><th width="80" data-sorter="false" data-filter="false">Acciones:</th></tr></thead>';
        while ($row = mysql_fetch_array($data)) {
            $cod = $row["pac_codigo"];
            printf("<tr><td><b>%s</b></td><td align='left'>%s</td><td>%s</td><td>%s</td><td style='background-color:#F7A7F5;'><b>%s</b></td><td>%s</td><td style='background-color:#DDFF33;'><i><b>%s</b></i></td><td align='center'>%s</td></tr>", $cod, $row["prd_glosa"], $row["unimed_nombre"], $row["prd_cenabast"], $row["stock_cantidad"], "$ " . number_format($row["prd_precio"], 0, ',', '.'), "<input type='textbox' class='caja2' size='7' id='" . $cod . "' value='" . $row["pac_cantidad"] . "' title='" . $row["pac_cantidad"] . "' onchange='cambiaPac(this.id,this.value);' readonly>&nbsp;<span id='div_" . $cod . "' style='height:15px;'></span>", "<a href=../edicion/ModificarArticulo.php?id=" . $cod . "&tipo=1 target='_blank' title='Editar Articulo' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=620, width=800 left=250 top=30" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='editar articulo' title='Editar articulo en ventana externa' src='../img/edit1.png' width='25' height='25'></a>&nbsp;&nbsp;|&nbsp;<img border='0' alt='editar PAC' title='Editar PAC' src='../img/edit2.png' class='imgcentro' onclick='cambiarValor(" . $comilladob . $cod . $comilladob . ");'>");
            $sum = $sum + ($row["prd_precio"] * $row["pac_cantidad"]);
            $sto = $sto + $row["pac_cantidad"];
        }
    }
    echo '</table>';
    if ($contar == 0) {
        $total = "";
    } else {
        $total = "<table class='table2'><tr><td colspan='8' class='texto8'>Suma de PAC: " . number_format($sto, 0, ',', '.') . " Unidades.&nbsp;&nbsp; Total Valorizado con IVA:&nbsp;&nbsp;&nbsp;&nbsp;<b>$" . number_format(($sum * 1.19), 0, ',', '.') . "&nbsp;&nbsp;</b></td></tr></table>";
    }
    echo $total;
    echo '<script type="text/javascript">$(document).ready(function () {$("#tabladoc").tablesorter();});</script>';
}

mysql_close($link);
?>