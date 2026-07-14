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
$chimg = $_POST['chimg'];
$chnv = $_POST['chnv'];
$comilladob = '"';
$comillasim = "'";
$_SESSION['query'] = "";

$rs = mysql_query("SELECT * FROM const_config WHERE const_cat ='linkcm' AND const_est='1'", $link); //constantes de configuracion, reconoce todos los categoria link convenio marco guardados
$rowc = mysql_fetch_array($rs);
$idlink = $rowc["const_val"];

if ($chk == "true") {
    $checktipo = " prd_destacado=1 AND ";
} else {
    $checktipo = "";
}
if ($chnv == "true") {
    $chknv = " prd_estado=0 AND ";
} else {
    $chknv = "";
}
if ($chimg == "true") {
    $img = " prd_imagen != '' AND ";
} else {
    $img = "";
}
if (empty($buscar) and $vertodo == "true") {
    buscar($buscar, $checktipo, $img, $chknv);
}
if (!empty($buscar) and strlen($buscar) >= 3) {
    buscar($buscar, $checktipo, $img, $chknv);
}

function buscar($b, $checktipo, $img, $chknv) {
    global $comilladob;
    global $comillasim;
    global $idlink;
    $ark = explode(" ", $b); //inicio filtro anidado, llena arreglo con los caracteres a buscar, considera espacio como separador
    if (count($ark) <= 1) {
        $sql = "SELECT * FROM productos, unimed, estado  WHERE " . $checktipo . $img . $chknv . " prd_unimed = unimed_id AND prd_estado=estado_id"
                . " AND (prd_codigo LIKE '%" . $ark[0] . "%' or prd_glosa LIKE '%" . $ark[0] . "%'or prd_ref LIKE '%" . $ark[0] . "%'or prd_codcm LIKE '%" . $ark[0] . "%' or prd_cenabast LIKE '%" . $ark[0] . "%' or prd_barcode LIKE '%" . $ark[0] . "%' or prd_fecha LIKE '%" . $ark[0] . "%')";
    } else {
        $sql = "SELECT * FROM productos, unimed, estado  WHERE " . $checktipo . $img . $chknv . "  prd_unimed = unimed_id AND prd_estado=estado_id";
        for ($i = 0; $i <= count($ark); $i++) {
            if (!empty($ark[$i])) {
                $sql .= " AND (prd_codigo LIKE '%" . $ark[$i] . "%' or prd_glosa LIKE '%" . $ark[$i] . "%'or prd_ref LIKE '%" . $ark[$i] . "%'or prd_codcm LIKE '%" . $ark[$i] . "%' or prd_cenabast LIKE '%" . $ark[$i] . "%' or prd_barcode LIKE '%" . $ark[$i] . "%' or prd_fecha LIKE '%" . $ark[$i] . "%')";
            }
        }
    }
    $data = mysql_query($sql);
    $_SESSION['query'] = $sql;
    //echo $sql;
    $contar = mysql_num_rows($data);
    if ($contar === 0) {
        echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran los productos que cumplen con los filtros.'/>&nbsp;&nbsp;No hay datos para Mostrar...Revise si hay algun error en la escritura, o revise bien los filtros aplicados.</p><img class='imgnoenc' src='../img/nores.png' title='sin Resultados.'/>";
        $_SESSION['query'] = "";
    } else {
        if ($b == "") {
            echo "<p class='texto2'><img src='../img/info.png' class='imginfo' title='se muestran todos los productos que cumplen con los filtros.'/>&nbsp;&nbsp;Mostrando todos los Registros. (se han encontrado: <b>$contar</b> Resultados)</p>";
        } else {
            echo "<p class='texto2'><img src='../img/info.png' class='imginfo' title='se muestran todos los productos que cumplen con los filtros.'/>&nbsp;&nbsp;Se han encontrado <b>$contar</b> resultados para: *<b>" . $b . "</b>*</p>";
        }
        echo '<table class="table2" id="tabladoc">
         <thead><tr>
             <th>Fam.</th><th width="80">C&oacute;digo Producto</th><th>Nombre Producto</th><th>Unidad Medida</th><th>Referencia Proveedor</th><th>ID Conv. Marco</th><th>C&oacute;digo Cenabast</th><th width="90">Precio Neto</th><th>Estado</th><th width="40">Tipo</th><th width="40">Imagen</th><th width="120" data-sorter="false" data-filter="false">Acciones:</th>
         </tr></thead>';
        while ($row = mysql_fetch_array($data)) {
            $dest = $row["prd_destacado"];
            if ($dest == "1") {
                $estrella = "<img border='0' alt='destacado' title='Producto Destacado' src='../img/estrella.png' width='25' height='25'>";
            } else {
                $estrella = "";
            }
            if ($row["prd_imagen"] == "") {
                $imagen = "";
            } else {
                $imagen = "<a href=../herramientas/visorImagen.php?id=" . $row["prd_codigo"] . "&tipo=1 target='_blank' title='Ver Imagen' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=600, width=760 left=300 top=30" . $comillasim . "); return false;" . $comilladob . "><img class='imgmenu' alt='imagen' title='ver imagen' id='" . $row["prd_codigo"] . "' src='../img/cam.png' onmouseover='vista(this.id);'></a>";
            }
            printf("<tr><td>%s</td><td><b>%s</b></td><td align='left'>%s</td><td>%s</td><td>%s</td><td>%s</td><td><b>%s</b></td><td align='left'>%s</td><td><b>%s</b></td><td><b>%s</b></td><td><b>%s</b></td><td align='center' width='80'>%s</td></tr>", $row["prd_fam"], $row["prd_codigo"], $row["prd_glosa"], $row["unimed_nombre"], $row["prd_ref"], '<a href="' . $idlink . $row["prd_codcm"] . '" target="_blank">' . $row["prd_codcm"] . '</a>', $row["prd_cenabast"], "$ " . number_format($row["prd_precio"], 2, ',', '.'), $row["estado_nombre"], $estrella, $imagen, "<a href=../edicion/ModificarArticulo.php?id=" . $row["prd_codigo"] . "&tipo=1 target='_blank' title='Editar Articulo' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=620, width=820 left=250 top=20" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='editar articulo' title='Editar articulo en ventana externa' src='../img/edit1.png' width='20' height='20'></a>|
                    <a href=../reportes/CartolaArticulo.php?id=" . $row["prd_codigo"] . "><img border='0' alt='movimiento articulo' title='ver movimientos articulo: " . $row["prd_codigo"] . "' src='../img/lupa.png' width='20' height='20'></a>|
                    <a href=../herramientas/buscarNivelStock.php?cod=" . $row["prd_codigo"] . "&op=1 target='_blank'><img border='0' alt='editar Nivel Stock articulo' title='Editar Nivel Stock articulo: " . $row["prd_codigo"] . "' src='../img/edit.png' width='20' height='20'></a>|
                    <a href=../reportes/etiqueta.php?id=" . $row["prd_codigo"] . " target='_blank' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=520, width=760 left=200 top=100" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='crear etiqueta' title='crear etiqueta del articulo' src='../img/printlabel.png' width='20' height='20'></a>");
        }
    }
    echo '</table>';
    echo "<br>";
    if ($contar > 10) {
        echo '<div id="pager" class="pagerform">
        <form>
            <img src="../js/images/first.png" class="first" style="cursor:pointer;" title="Ir a Página Inicial"/>
            <img src="../js/images/prev.png" class="prev" style="cursor:pointer;" title="Ir a Página Anterior"/>
            &nbsp;<input type="text" class="pagedisplay" size="45" readonly style="border:1px solid #808080;font-weight: bold;text-align: center;"/>
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