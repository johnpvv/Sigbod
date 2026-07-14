<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
include("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
$chk = $_POST['chk'];
$buscar = $_POST['saldo'];
$vertodo = $_POST["chkall"];
$anio = $_POST['anio'];
$_SESSION['query'] = "";
if ($chk == "true") {
    $checkestado = " prov_estado=1 AND";
} else {
    $checkestado = "";
}
if ($anio != "") {
    $anio1 = " prov_fechacreacion = '$anio' AND";
} else {
    $anio1 = "";
}
if (empty($buscar) and $vertodo == "true") {
    buscar($buscar, $checkestado, $anio1);
}
if (!empty($buscar) and strlen($buscar) >= 2) {
    buscar($buscar, $checkestado, $anio1);
}

function buscar($saldo, $checkestado, $anio1) {
    $ark = explode(" ", $saldo); //inicio filtro anidado, llena arreglo con los caracteres a buscar, considera espacio como separador
    if (count($ark) <= 1) {
        $sql = "SELECT * FROM proveedores WHERE " . $checkestado . $anio1 . " (prov_rut LIKE '%" . $ark[0] . "%' or prov_nombre LIKE '%" . $ark[0] . "%'or prov_giro LIKE '%" . $ark[0] . "%'or prov_nomcontacto LIKE '%" . $ark[0] . "%' or prov_fechacreacion LIKE '%" . $ark[0] . "%') ORDER BY prov_nombre";
    } else {
        $sql = "SELECT * FROM proveedores WHERE " . $checkestado . $anio1;
        for ($i = 0; $i <= count($ark); $i++) {
            if (!empty($ark[$i])) {
                $sql .= " (prov_rut LIKE '%" . $ark[$i] . "%' or prov_nombre LIKE '%" . $ark[$i] . "%'or prov_giro LIKE '%" . $ark[$i] . "%'or prov_nomcontacto LIKE '%" . $ark[$i] . "%')";
                if ($i < count($ark) - 1) {
                    $sql .= " AND ";
                } else {
                    $sql .= "";
                }
            }
        }
        $sql .= " ORDER BY prov_nombre";
    }
    //echo $sql;
    $data = mysql_query($sql);
    $_SESSION['query'] = $sql;
    $contar = mysql_num_rows($data);
    if ($contar === 0) {
        echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran los documentos que cumplen con los filtros.'/>&nbsp;&nbsp;No hay datos para Mostrar...Revise si hay algun error en la escritura, o revise bien los filtros aplicados.</p><img class='imgnoenc' src='../img/nores.png' title='sin Resultados.'/>";
        $_SESSION['query'] = "";
    } else {
        if ($saldo == "") {
            echo "<p class='texto2'><img src='../img/info.png' class='imginfo' title='se muestran todos los datos que cumplen con los filtros.'/>&nbsp;&nbsp;Mostrando todos los Registros. (se han encontrado: <b>$contar</b> Resultados)</p>";
        } else {
            echo "<p class='texto2'><img src='../img/info.png' class='imginfo' title='se muestran todos los datos que cumplen con los filtros.'/>&nbsp;&nbsp;Se han encontrado <b>$contar</b> resultados para: *<b>" . $saldo . "</b>*</p>";
        }
        echo '<table class="table2" id="tablaprov">
         <thead>
         <tr>
             <th width="100">RUT</th><th>Razon Social</th><th>Giro</th><th>Contacto</th><th>Telefono</th><th width="80">Estado</th><th width="100">Fecha Modificaci&oacute;n</th><th width="70" data-sorter="false" data-filter="false">Acciones:</th>
         </tr></thead>';
        while ($row = mysql_fetch_array($data)) {
            $estado = $row["prov_estado"];
            if ($estado == "0") {
                $estado = "No Vigente";
            } else {
                $estado = "Vigente";
            }
            printf("<tr><td><b>%s</b></td><td align='left'>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td><td><b>%s</b></td><td>%s</td><td>%s</td></tr>", number_format($row["prov_rut"], 0, '.', '.') . "-" . $row["prov_dv"], $row["prov_nombre"], $row["prov_giro"], $row["prov_nomcontacto"], $row["prov_telefono"], $estado, date("d/m/Y", strtotime($row["prov_fechacreacion"])), "<a href=../edicion/ModificarProveedor.php?id=" . $row["prov_rut"] . "&option=2><img border='0' alt='editar proveedor' title='Editar proveedor: " . $row["prov_nombre"] . "' src='../img/edit.png' width='25' height='25'></a>");
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
                <option value="100" selected="selected">100</option>
		<option value="300">300</option>
            </select>
	<select class="gotoPage" title="Ir a Pagina"></select>
        </form>
    </div>';
    }
    echo '<script type="text/javascript">
        $(document).ready(function (){
            $("#tablaprov").tablesorter({
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