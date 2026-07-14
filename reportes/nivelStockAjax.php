<?php

$timeant = microtime(true); //calcular el tiempo de ejecucion, capturo tiempo inicial
session_start();
include("../include/conn.php");
$link = Conectarse(); //Variable de conexion
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$chkoc = $_POST["chkoc"];
$chkrec = $_POST["chkrec"];
if ($chkrec == '3') {
    $table = ", nivelstock ";
    $solrec = " AND nivelstock_cod=stock_codigo AND nivelstock_reciente=0 ";
}

/* if($voc=='2'){//con oc nueva
  $vocrec=" AND nivelstock_oc_reciente = '1' ";
  } */

echo '<table class="table2" id="nivelstock">';
echo '<tr class="texto5"><thead>';
echo '<th width = "80">Código Producto</th>';
echo '<th width="380">Glosa</th>';
echo '<th width = "70">Unidad de Medida</th>';
echo '<th width="60" class="color_amarillo">Stock Actual</th>';
echo '<th width = "60">Stock Critico</th>';
echo '<th width="80">Tipo Quiebre</th>';
echo '<th width = "90">Fecha Ultima Solicitud</th>';
echo '<th width="70">Folio Solicitud</th>';
echo '<th width = "60"> Cantidad Solicitada</th>';
echo '<th width="170" data-sorter="false" data-filter="false">Acciones</th></thead></tr>';

switch ($chkoc) {
    case ($chkoc == 1):
        $sql = "SELECT * FROM productos, stock, saldos, tiponivel, unimed " . $table . " WHERE prd_destacado=1 AND prd_codigo=stock_codigo AND prd_unimed=unimed_id AND tiponivel_codigo=stock_codigo AND stock_cantidad <= tiponivel_critico AND stock_codigo=saldo_codigo " . $solrec . " AND saldo_pendiente > 0 AND `saldo_est` = 0 GROUP BY saldo_codigo"; //mostrar solo los que tienen oc
        break;
    case ($chkoc == 2):
        $sql = "SELECT * FROM productos, stock, unimed, tiponivel WHERE stock_codigo NOT IN(SELECT saldo_codigo FROM saldos WHERE saldo_pendiente>0 AND `saldo_est` = 0) AND prd_destacado=1 AND prd_codigo=stock_codigo AND prd_unimed=unimed_id AND tiponivel_codigo=stock_codigo AND stock_cantidad <= tiponivel_critico"; //para mostrar solo los que no tienen oc
        break;
    /* case ($opc == 3):
      $sql = "SELECT * FROM productos, stock, unimed, tiponivel, nivelstock WHERE prd_destacado=1 AND prd_codigo=stock_codigo AND prd_unimed=unimed_id AND tiponivel_codigo=stock_codigo AND stock_cantidad <= tiponivel_critico AND nivelstock_cod=stock_codigo AND nivelstock_reciente='0' "; //mostrar todos con nivelstock mayor a 6 dias
      break; */
    case ($chkoc == 4):
        $sql = "SELECT * FROM productos, stock, unimed, tiponivel " . $table . "WHERE prd_destacado=1 AND prd_codigo=stock_codigo AND prd_unimed=unimed_id AND tiponivel_codigo=stock_codigo AND stock_cantidad <= tiponivel_critico " . $vocrec . " " . $solrec; //mostrar todos
        break;
    default:
        $sql = "SELECT * FROM productos, stock, unimed, tiponivel WHERE prd_destacado=1 AND prd_codigo=stock_codigo AND prd_unimed=unimed_id AND tiponivel_codigo=stock_codigo AND stock_cantidad <= tiponivel_critico"; //mostrar todos
}
//echo $sql;
$_SESSION['query'] = $sql;
$fecha = date("Y-m-d H:i:s");
$fechanum = (strtotime(date("Y-m-d H:i:s")));
$nivelid = $_SESSION['nvstockid']; //saber si ha entrado mas de una vez para actualizar log de cantidad de veces qu tienen quibre los productos
$comilladob = '"';
$comillasim = "'";
$res = MySQL_query($sql, $link) or die(mysql_error());
$contar3 = mysql_num_rows($res);
$cont = 0;

$res2 = mysql_query("SELECT saldo_codigo AS C, sum(saldo_pendiente) AS T FROM saldos WHERE  `saldo_est` = 0 group by saldo_codigo", $link);
while ($row2 = mysql_fetch_array($res2)) {//llenar arreglo con los saldos y dejarlo preparado para consultar las oc con saldo
    $saldo[$row2["C"]] = $row2["T"];
}
while ($row = mysql_fetch_array($res)) {
    $stock = $row["stock_cantidad"];
    $codigo = $row["prd_codigo"];
    $sql5 = "SELECT nivelstock_cuentatotal,nivelstock_fecha,nivelstock_contador FROM nivelstock WHERE nivelstock_cod='$codigo'";
    $res5 = MySQL_query($sql5, $link) or die(mysql_error());
    $row5 = MySQL_Fetch_array($res5);
    $contar5 = mysql_num_rows($res5);
    $contador = $row5["nivelstock_contador"];
    $fechaant = strtotime($row5["nivelstock_fecha"]);
    $dif = round(($fechanum - $fechaant) / (3600 * 24));
    if ($dif >= 15) {
        mysql_query("UPDATE nivelstock SET nivelstock_cuentatotal='" . ($row5["nivelstock_cuentatotal"] + 1) . "',nivelstock_cant='$stock', nivelstock_contador='0', nivelstock_fecha='$fecha' WHERE nivelstock_cod='$codigo '", $link)or die(mysql_error());
        $contador = 0;
    }
    if ($contar5 == 0) {
        mysql_query("INSERT INTO nivelstock (nivelstock_cod,nivelstock_cant,nivelstock_contador,nivelstock_cuentatotal,nivelstock_fecha) VALUES ('$codigo','$stock','1','1','$fecha')", $link)or die(mysql_error());
        $tipo = '<b style = "color:#ee8400;">Ahora</b>';
    } else {
        if ($contador < 15) {
            $tipo = '<b style = "color:green;">Nuevo</b><br><b style = "color:black; font-size: 9px;">' . $contador . ' Aviso(s)</b>';
        } else if ($contador >= 15 && $contador <= 30) {
            $tipo = '<b style = "color:blue;">Reciente</b><br><b style = "color:black; font-size: 9px;">' . $contador . ' Aviso(s)</b>';
        } else {
            $tipo = '<b style = "color:red;">Antiguo</b><br><b style = "color:black; font-size: 9px;">' . $contador . ' Aviso(s)</b>';
        }
        if ($nivelid == 1) {
            mysql_query("UPDATE nivelstock SET nivelstock_cant ='$stock', nivelstock_contador ='" . ($contador + 1) . "',nivelstock_fecha ='$fecha' WHERE nivelstock_cod='$codigo'", $link)or die(mysql_error());
            $_SESSION["nvstockid"] = 0;
        }
    }
    $sql1 = "SELECT detmov_id,mov_fecha,detmov_cantidad FROM detmovimiento, movimiento WHERE detmov_codigoprd = '$codigo' AND mov_id=detmov_id ORDER by detmov_id desc LIMIT 1"; //seleccionar el ultimo registro de atras para adelante
    $res1 = MySQL_query($sql1, $link) or die(mysql_error());
    $contar = mysql_num_rows($res1);
    $nv = "<a href=../herramientas/buscarNivelStock.php?cod=" . $codigo . "&op=1 target='_blank '><img border='0  ' alt='editar nivel' title='editar nivel stock' src='../img/edit.png' width='20' height='20'></a>";
    if ($saldo[$codigo] == 0) {
        $codoc = "<img border='0' alt='error' title='No Hay Ordenes de compra para el codigo " . $codigo . "' src='../img/error.png' width='20' height='20'>";
    } else {
        $codoc = "<a href=../pedidos/PedidoArticulo.php?codprd=" . $codigo . "&op=1 target='_blank'><img border='0' alt='solicitar solo el articulo' title='Crear Solicitud' src='../img/pedido.png' width='18' height='20'></a>";
    }
    if ($contar == 0) {
        $cant = "Sin Datos";
        $fecha_mov = "<b>Sin Datos</b>";
        $id_mov = "<b>Sin Datos</b>";
        $sol = "<img border='0' alt='Documento no Encontrado' title='Documento no Encontrado' src='../img/noenc.png' width='24' height='20'>";
        $cart = "<img border='0' alt='sin movimientos' title='Sin Movimientos que mostrar' src='../img/info.png' width='20' height='20'></a>";
        $modcod = "<a href=../edicion/ModificarArticulo.php?id=" . $row["prd_codigo"] . "&tipo=1 target='_blank' title='Editar Articulo' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=620, width=800 left=250 top=30" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='editar articulo' title='Editar articulo en ventana externa' src='../img/edit1.png' width='20' height='20'></a>";
    } else {
        $row1 = MySQL_Fetch_array($res1);
        $cant = number_format($row1["detmov_cantidad"], 0, ',', '.');
        $fecha_mov = date("d/m/Y h:i A", strtotime($row1["mov_fecha"]));
        $id_mov = "SOL-" . $row1["detmov_id"];
        $sol = "<a href=pedidoPDF.php?id=" . str_replace("SOL-", "", $id_mov) . " target=' _blank'><img border='  0' alt='Ver solicitud' title='Ver Solicitud' src='../img/pdf.png' width='24' height='22'></a>";
        $cart = "<a href=cartolaArticulo.php?id=" . $row["prd_codigo"] . "><img border='0' alt='movimiento articulo' title='ver movimientos articulo: " . $row["prd_codigo"] . "' src='../img/lupan.png' width='20' height='20'></a>";
        $modcod = "<a href=../edicion/ModificarArticulo.php?id=" . $row["prd_codigo"] . "&tipo=1 target='_blank' title='Editar Articulo' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=620, width=800 left=250 top=30" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='editar articulo' title='Editar articulo en ventana externa' src='../img/edit1.png' width='20' height='20'></a>";
    }
    printf("<tr><td><b>%s</b></td><td align='left'>%s</td><td>%s</td><td class='color_amarillo'><b>%s</b></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td></tr>", $codigo, $row["prd_glosa"], $row["unimed_nombre"], $stock, $row["tiponivel_critico"], $tipo, $fecha_mov, $id_mov, $cant, $sol . "&nbsp;|&nbsp;" . $codoc . "&nbsp;|&nbsp;" . $cart . "&nbsp;|&nbsp;" . $nv . "&nbsp;|&nbsp;" . $modcod);

    $cont = $cont + 1;
}
$timedesp = microtime(true); //calculo tiempo final
$time = round($timedesp - $timeant, 2); //resto los tiempos y obtengo la demora de la ejecucucion

echo '</tr>';
echo '<table class="table2">';
echo '<tr>';
echo '<td colspan="10" class="texto8b">Se han encontrado: ' . $cont . ' Registros. (La consulta ha tardado: ' . $time . ' Segundos)</td>';
echo '</tr>';
echo '</table>';
echo '<script type="text/javascript">$(document).ready(function () {$("#nivelstock").tablesorter();});</script>';
mysql_close($link);
?>
