      		
<?php

include_once("../include/conn.php");
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
$link = Conectarse();
$buscar = $_POST['saldo'];
$vertodo = $_POST["chkall"];
$tipostock = $_POST["tiposto"];
if($tipostock==1){
    $tiposto=" AND stock_cantidad<tiponivel_min ";
}
if($tipostock==2){
    $tiposto=" AND stock_cantidad<tiponivel_critico ";
}
if($tipostock==3){
    $tiposto=" AND stock_cantidad>tiponivel_max ";
}
/*$sql = "SELECT `prd_codigo`, `prd_glosa`, `prd_unimed`,`prd_precio`,`unimed_nombre`,`tiponivel_codigo`,`tiponivel_fam`,`tiponivel_max`,`tiponivel_min`,`tiponivel_critico`,`stock_cantidad` FROM `productos`,`tiponivel`,`unimed`, stock WHERE `prd_codigo` = `tiponivel_codigo` AND `prd_unimed`= `unimed_id` AND prd_codigo=`stock_codigo` AND prd_fam='$fam' " . $varsql . " ORDER BY tiponivel_codigo";
$result = mysql_query($sql, $link);
$cuenta = mysql_num_rows($result);
if ($cuenta == 0) {
    echo '<script>alert("Los Datos ingresados no son validos, Favor intente Nuevamente...");
				' . $opv . ';</script>';
    $_SESSION['query'] = "";
} else {
    $_SESSION['query'] = $sql;
}*/
if (empty($buscar) and $vertodo == "true") {
    buscar($buscar, $tiposto);
} else if (!empty($buscar)) {
    buscar($buscar, $tiposto);
} else {
    echo "<script>alert('Error, ingrese algun dato para Buscar');</script>";
}

function buscar($saldo, $tiposto) {
    global $comilladob;
    global $comillasim;
    if (strlen($saldo) > 20) {//si se escriben menos de 20 caracteres buscara
        $sql = $saldo;
        $_SESSION['query'] = $sql;
    } else {
        $sql = "SELECT `prd_codigo`, `prd_glosa`, `prd_unimed`,`prd_precio`,`unimed_nombre`,`tiponivel_codigo`,`tiponivel_fam`,`tiponivel_max`,`tiponivel_min`,`tiponivel_critico`,`stock_cantidad` FROM `productos`,`tiponivel`,`unimed`, stock WHERE `prd_codigo` = `tiponivel_codigo` AND `prd_unimed`= `unimed_id` AND prd_codigo=`stock_codigo` AND (prd_codigo LIKE '%" . $saldo . "%' OR prd_glosa LIKE '%" . $saldo . "%')".$tiposto." ORDER BY tiponivel_codigo";

    }
    //echo $sql;
    $result = mysql_query($sql);
    $contar = mysql_num_rows($result);
    $_SESSION['query'] = $sql;
    //$contar = mysql_num_rows($data);
    if ($contar === 0) {
        echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran los documentos que cumplen con los filtros.'/>&nbsp;&nbsp;No hay datos para Mostrar</p>";
        $_SESSION['query'] = "";
    } else {
        if ($saldo == "") {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran todos los documentos que cumplen con los filtros.'/>&nbsp;&nbsp;Mostrando todos los Registros. (se han encontrado: <b>$contar</b> Resultados)</p>";
        } else {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran todos los documentos que cumplen con los filtros.'/>&nbsp;&nbsp;Se han encontrado <b>$contar</b> resultados para: *<b>" . $saldo . "</b>*</p>";
        }
        echo '<table id="tabladoc" class="table2e">';
        echo '<thead><tr><th width="80">Código Interno</th>
                    <th width="450">Glosa</th>
                    <th width="80">Unidad de Medida</th>
                    <th width="70">Stock Actual</th>
                    <th width="70">Stock Crítico</th>
                    <th width="70">Stock Mínimo</th>
                    <th width="90">Stock Máximo</th>
                    <th width="10">Modif.</th>
                    <th width="130">Precio Unitario</th>
                    <th width="30" data-sorter="false" data-filter="false">Acciones</th>
                </tr></thead>';
        $cont = 0;
        while ($row = mysql_fetch_array($result)) {
            printf("<tr><td><b>%s</b></td><td align='left'>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s<div id='$cont'></div></td><td><b>$  %s</b></td><td>%s</td></tr>", '<input type="textbox" class="caja2" size="7" id="txtcod_' . $cont . '" value="' . $row["prd_codigo"] . '" readonly>', $row["prd_glosa"], $row["unimed_nombre"], $row["stock_cantidad"], '<input type="textbox" class="caja_rojo" size="6" value="' . $row["tiponivel_critico"] . '" id="txtcri_' . $cont . '" onChange="actualizar('.$cont.')">', '<input type="textbox" class="caja_azul" size="6" value="' . $row["tiponivel_min"] . '" id="txtmin_' . $cont . '" onChange="actualizar('.$cont.')">', '<input type="textbox" class="caja_color" size="6" value="' . $row["tiponivel_max"] . '" id="txtmax_' . $cont . '" onChange="actualizar('.$cont.')">',"",number_format($row["prd_precio"], 2, '.', ''), "<a href=../edicion/borrarNivelArticulo.php?id=" . $row["prd_codigo"] . "&opc=" . $opc . " onclick='return borrar()'><img border='0' alt='borrar nivel stock articulo' title='Borrar Nivel stock articulo: " . $row["prd_codigo"] . "' src='../img/trash.png' width='23' height='23'></a>");
            $cont++;
        }
    }
}
mysql_close($link);
$_SESSION["pedido"] = 1;
 echo '<script type="text/javascript">$(document).ready(function () {$("#tabladoc").tablesorter();});</script>';
?>
                