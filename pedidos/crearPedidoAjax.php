<?php
include_once("../include/conn.php");
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}

$link = Conectarse();
$rutpv = $_POST['rut']; //obtenemos el rut del proveedor elegido
$oc = $_POST['oc']; //obtenemos orden de compra si se ha enviado
$opstock = $_POST['tiposto']; //obtenemos tipo stock si se ha enviado
$buscar = $_POST['saldo'];
$op = $_POST['op'];
$anio = $_POST["anio"];
$anio2 = $_POST["anio2"] . " 23:59:59";
$comilladob = '"';
$comillasim = "'";
$ano = date("Y");

switch ($opstock) {
    case 0:
        $stc = "";
        break;
    case 1:
        $stc = " AND stock_cantidad=0 ";
        break;
    case 2:
        $stc = " AND stock_cantidad>0 ";
        break;
    case 3:
        $stc = "AND prd_destacado=1 ";
        break;
    case 4:
        $stc = " AND `tiponivel_codigo`=`stock_codigo` AND `stock_cantidad` <= `tiponivel_critico` ";
        $campo = "`tiponivel_critico`, `tiponivel_codigo`, ";
        $table = "`tiponivel` , ";
        break;
}

if ($oc == null) {
    $varsql = "";
} else {
    $varsql = " AND `saldo_oc`='$oc'";
}
if ($anio != "") {
    $anio1 = " AND saldo_fechaoc BETWEEN '$anio' AND '$anio2' ";
}
$sql = "SELECT `prd_codigo`,`prd_codcm`,`prd_glosa`,`prd_unimed`,`unimed_nombre`,`prd_prov_unimed`,`saldo_oc`,`saldo_rutprov`,`saldo_est`," . $campo . "`prov_nombre`,`prov_rut`,`prov_dv`,`saldo_codigo`,`saldo_fechaoc`,`saldo_preciounit`,`saldo_pendiente`,`stock_cantidad` FROM `productos`,`saldos`," . $table . "`proveedores`,`stock`,`unimed` WHERE prd_estado=1 AND `saldo_est` = 0 AND `prd_codigo` = `saldo_codigo` AND `stock_codigo`= `saldo_codigo` AND `prd_unimed`= `unimed_id` AND `prov_rut`= `saldo_rutprov` AND `saldo_pendiente`>0 AND `saldo_rutprov`='$rutpv' " . $varsql . $stc . $anio1 . "AND (prd_codigo LIKE '%" . $buscar . "%' or prd_glosa LIKE '%" . $buscar . "%'or prd_glosaamp LIKE '%" . $buscar . "%' or saldo_oc LIKE '%" . $buscar . "%') ORDER BY saldo_fechaoc, saldo_oc";
$result = mysql_query($sql, $link);
$result4 = mysql_query($sql, $link);
$result1 = mysql_query("SELECT `prov_nombre`,`prov_rut`,`prov_dv` FROM `proveedores`WHERE `prov_rut`= '$rutpv'", $link);
$row1 = mysql_fetch_array($result1);
$cuenta = mysql_num_rows($result);
$rut = $row1["prov_rut"] . "-" . $row1["prov_dv"];
$linkrut = "<a href=../edicion/modificarProveedor.php?id=" . $row1["prov_rut"] . "&option=1 target='_blank' title='Ver detalle proveedor' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=540, width=800 left=300 top=100" . $comillasim . "); return false;" . $comilladob . ">" . number_format($row1["prov_rut"], 0, '.', '.') . "-" . $row1["prov_dv"] . "</a>";
$nombre = $row1["prov_nombre"];
?>        
<table class="table2a">
    <tr>
        <th colspan="5" class="texto4d">PROVEEDOR:&nbsp;<?php echo $linkrut; ?>&nbsp;&nbsp;&nbsp;<?php echo $nombre; ?></th>
    </tr>
    <tr>
        <th colspan="5" class="texto4d">Productos Disponibles para Generar Solicitud:&nbsp;&nbsp;<?php echo $cuenta; ?></th>
    </tr>
</table>
<?php
if ($cuenta == 0) {
    echo '<table class="table2a">';
    echo '<thead><tr class="texto5">';
    echo '<tr class="texto5"><th>No se han encontrado registros con los filtros Seleccionados.</th></tr>';
    echo '</table><br>';
    echo "<script> activaBtn(0); </script>";
    $_SESSION['query'] = "";
} else {
    echo "<script> activaBtn(1); </script>";
    echo '<form id="form" name="form" action="../pedidos/mostrarPedido.php" method="post">
        <table class="table2a" id="tabla">
        <thead><tr class="texto5">
        <th width="80">Código Interno</th>
        <th>Código CM</th>
        <th width="300">Glosa</th>
        <th>U.M.</th>
        <th width="135">Orden de Compra</th>
        <th>N° Pedido Previo</th> 
        <th>Fecha OC</th>
        <th>Precio en OC</th>                 
        <th width="50">Cant. Pend. OC</th>
        <th class="color_amarillo">Stock</th>
	<th class="color_celeste">PAC Prom.</th>
        <th width="70">Cantidad a Pedir</th>
        <th width="30" data-sorter="false" data-filter="false">Todo<input type="checkbox" name="chktodo" id="chktodo" onclick="selectall(document.forms[0])" /></th>
        </tr></thead>';
    $cont = 0;
    while ($row = mysql_fetch_array($result)) {
        $cod = $row["prd_codigo"];
        $oc1 = $row["saldo_oc"];
        $sql2 = "SELECT pac_cantidad, pac_ano FROM pac WHERE pac_codigo='$cod' AND pac_ano='$ano'";
        $result2 = mysql_query($sql2, $link);
        $row2 = mysql_fetch_array($result2);
        $pac = $row2["pac_cantidad"] / 12;
        $sql3 = "SELECT detmov_id,mov_fecha FROM detmovimiento, movimiento WHERE detmov_clave='$oc1$cod' AND detmov_id=mov_id AND mov_estado ='1' ORDER BY detmov_id DESC LIMIT 1";
        $result3 = mysql_query($sql3, $link);
        $row3 = mysql_fetch_array($result3);
        $ped = $row3["detmov_id"];
        $fped = date("d/m/Y", strtotime($row3["mov_fecha"]));
        if ($ped != "") {
            $ped = "SOL-" . $ped . "<br><b class='texto11'>(" . $fped . ")</b>";
        }
        $stock = $row["stock_cantidad"];
        $cart = "<a href=../reportes/cartolaArticulo.php?id=" . $cod . "&op=1 onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=480, width=1320 left=10 top=10" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='movimiento articulo' title='ver movimientos articulo: " . $cod . "' src='../img/lupan.png' width='20' height='20'></a>";
        if ($stock == 0) {
            $clase = "color_rojo";
        } else {
            $clase = "color_amarillo";
        }
        $udp = $row["prd_prov_unimed"];
        $res4 = MySQL_query("SELECT * FROM unimed WHERE unimed_id='$udp'", $link)or die(mysql_error());
        $row4 = mysql_fetch_array($res4);
        $uniprov = $row4["unimed_nombre"];
        if ($uniprov == "--Elija Unidad--") {
            $uniprov = "Sin Datos";
        }
        printf("<tr><td class='texto5'>%s</td><td>%s</td><td align='left'><b>%s</b></td><td>%s</td><td><b>%s</b></td><td><i>%s</i></td><td>%s</td><td><b>$</b>%s</td><td>%s</td><td class='" . $clase . "'>%s</td><td class='color_celeste'><i>%s</i></td><td>%s</td><td>%s</td></tr>", '<input type="textbox" class="caja2" title="La presentacion del Proveedor es: ' . $uniprov . '" readonly size="7" name="txtcod_' . $cont . '" value="' . $cod . '"/>' . $cart, '<a href="http://www.mercadopublico.cl/TiendaFicha/Ficha?idProducto=' . $row["prd_codcm"] . '" target="_blank" title="Revisar codigo en CM">' . $row["prd_codcm"] . '</a>', $row["prd_glosa"], $row["unimed_nombre"], '<input type="hidden" name="txtoc_' . $cont . '" value="' . $row["saldo_oc"] . '"/>' . "<a href=../edicion/modificarSaldo.php?id=" . $rutpv . "&oc=" . $row["saldo_oc"] . "&op=1 onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=720, width=1320 left=10 top=10" . $comillasim . "); return false;" . $comilladob . ">" . $row["saldo_oc"] . "</a>", $ped, date("d/m/Y", strtotime($row["saldo_fechaoc"])), '<input type="hidden" name="txtprec_' . $cont . '" value="' . number_format($row["saldo_preciounit"], 3, '.', '') . '"/>' . number_format($row["saldo_preciounit"], 1, ',', ''), '<span id="sp_' . $cont . '" >' . $row["saldo_pendiente"] . '</span>', number_format($stock, 0, '.', '.'), number_format($pac, 0, '.', '.'), '<input type="number" size="8" class="caja_select" min="0" max="' . $row["saldo_pendiente"] . '" value="' . $row["saldo_pendiente"] . '" name="caja_' . $cont . '" id="caja_' . $cont . '" onChange="validarMax(this.id);">', "<input type='checkbox' name='check_$cont' id='check_$cont' onclick='selDatos(this.id)'>");
        $cont++;
    }
    mysql_free_result($result);
    mysql_close($link);
    $_SESSION["pedido"] = 1;
    echo'</table>';
    echo '<table class="table2a">
    <tr>
    <td class="texto1">
    <span class="centrar_vert">Observaciones del Pedido:</span>
    <textarea name="obsped" id="obs" class="caja_color" cols="108" rows="4" style="overflow: auto;">Despacho horario AM. Los Precios incluidos son <b>REFERENCIALES</b>. Horario de Recepción: 08:30 a 16:00, Viernes hasta las 15:00.<br>Favor aplicar descuentos a órdenes de Convenio Marco si corresponde.<br>Gracias.</textarea></td>
    </tr>
    </table>
    <input type="hidden" name="contador" value="' . $cont . '" />
    <input type="hidden" name="rutpv" value="' . $rut . '" />
    <input type="hidden" name="nompv" value="' . $nombre . '" />
    <input type="hidden" name="opc" value="' . $op . '" />
    <br/>
</form>
<script type="text/javascript">
    $(function () {
        $("#tabla").tablesorter();
    });
</script>';
}
?>