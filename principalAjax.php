<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit();
}
if ($_SESSION['perfil'] > 1) {
    $cargar = 0;
} else {
    $cargar = 1;
}
//include ("include/conn.php");
include ("herramientas/buscaIndicador.php");
set_time_limit(1200);
$link = Conectarse();
$ind = enviarIndicador();
$cont = 0;
$dia = date("j");
$fechaactual = strtotime(date("Y-m-d H:i:s"));
$fecha = date("d/m/Y");
$fechaact = date("Y-m-d");
$anio = date("Y");

$rs = mysql_query("SELECT const_val FROM const_config WHERE const_cat ='pringrup' AND const_est='1'", $link); //constantes de configuracion, reconoce todos los categoria establecimiento guardados
$rows = mysql_fetch_array($rs);
$grupo = $rows["const_val"];

/* * *************************************************************** ALERTA 0 ********************************************************************** */

$sql2 = "SELECT prd_codigo FROM productos, stock, tiponivel WHERE prd_destacado='1' AND prd_codigo=stock_codigo AND tiponivel_codigo=stock_codigo AND stock_cantidad <= tiponivel_critico";
$res2 = MySQL_query($sql2, $link) or die("<h2 class='texto'>Ha Ocurrido un Error: </h2><center><b class='texto1'>" . mysql_error() . "...</b></center>");
$contar = mysql_num_rows($res2);
if ($contar > 0) {//rellenar textos alerta 0
    $alerta = '<tr class="texto5"><td>Hay ' . $contar . ' productos con niveles de Stock Insuficientes</td><td class="texto5b"><a href="reportes/nivelStock.php?op=1">Revisar Alerta</a></td></tr>';
    $cont = $cont + 1;
} else {
    $alerta = "";
}
/* * ************************************************************* ALERTA 1 ********************************************************************** */
if ($cargar > 0) {
    $sql3 = "SELECT carga_fecha FROM carga_log WHERE carga_codigo='1' ORDER by carga_id desc LIMIT 1"; //seleccionar el ultimo registro de atras para adelante de la carga de stock
    $res3 = MySQL_query($sql3, $link) or die(mysql_error());
    $row3 = mysql_fetch_array($res3);
    $fechacarga = strtotime($row3["carga_fecha"]);
    $dif = round(($fechaactual - $fechacarga) / (3600 * 24));
    if ($dif >= 5) {//rellenar textos alerta 1
        $alerta1 = '<tr class="texto5"><td>La base de datos de "STOCKS" tiene mas de 5 dias, se debe actualizar.<br>(Han pasado ' . $dif . ' dias desde la última carga) </td><td class="texto5b"><a href="carga/cargaExcelStock.php">Revisar Alerta</a></td></tr>';
        $cont = $cont + 1;
    } else {
        $alerta1 = "";
    }
}
/* * ************************************************************* ALERTA 2 ********************************************************************** */
if ($cargar > 0) {
    $sql4 = "SELECT carga_fecha FROM carga_log WHERE carga_codigo='2' ORDER by carga_id desc LIMIT 1"; //seleccionar el ultimo registro de atras para adelante de la carga de saldos
    $res4 = MySQL_query($sql4, $link) or die(mysql_error());
    $row4 = mysql_fetch_array($res4);
    $fechacarga1 = strtotime($row4["carga_fecha"]);
    $dif1 = round(($fechaactual - $fechacarga1) / (3600 * 24));
    if ($dif1 >= 5) {//rellenar textos alerta 2
        $alerta2 = '<tr class="texto5"><td>La base de datos de "SALDOS DE ORDEN DE COMPRA" tiene mas de 5 dias, se debe actualizar.<br>(Han pasado ' . $dif1 . ' dias desde la última carga) </td><td class="texto5b"><a href="carga/cargaExcelSaldo.php">Revisar Alerta</a></td></tr>';
        $cont = $cont + 1;
    } else {
        $alerta2 = "";
    }
}
/* * ************************************************************* ALERTA 3 ********************************************************************** */
if ($cargar > 0) {
    $sql6 = "SELECT prd_codigo, tiponivel_codigo, prd_destacado, stock_codigo, stock_cantidad, tiponivel_critico FROM productos, stock, tiponivel WHERE prd_destacado=1 AND prd_codigo=stock_codigo AND tiponivel_codigo=stock_codigo AND stock_cantidad <= tiponivel_critico";
    $res6 = MySQL_query($sql6, $link) or die(mysql_error());
    $cuenta = 0;
    while ($row6 = mysql_fetch_array($res6)) {
        $codigo = $row6["prd_codigo"];
        $sql6a = "SELECT mov_fecha FROM detmovimiento, movimiento WHERE detmov_codigoprd = '$codigo' AND mov_id=detmov_id ORDER by detmov_id desc LIMIT 1"; //seleccionar el ultimo registro de atras para adelante
        $res6a = MySQL_query($sql6a, $link) or die(mysql_error());
        $row6a = mysql_fetch_array($res6a);
        $fechamov = strtotime($row6a["mov_fecha"]);
        $dif6 = round(($fechaactual - $fechamov) / (3600 * 24));
        $analisisid = $_SESSION['nvstockanalisis'];
        if ($analisisid == '1') {
            $sql = "UPDATE nivelstock SET nivelstock_reciente='0' WHERE nivelstock_cod='" . $codigo . "'";
        } else {
            $sql = "SELECT nivelstock_cod FROM nivelstock WHERE nivelstock_reciente='1'";
        }
        if ($dif6 > 6) {
            mysql_query($sql, $link)or die(mysql_error()); //actualizar el nivelstock reciente a 1 si la fecha es valida a menos de 6 dias
        } else {
            $cuenta++;
        }
    }
    if ($cuenta > 0) {//rellenar textos alerta 3
        $alerta3 = '<tr class="texto5"><td>Hay ' . $cuenta . ' productos solicitados hace 5 dias, que aún tienen bajo Stock</td><td class="texto5b"><a href="reportes/solicitudesPendientes.php?op=1">Revisar Alerta</a></td></tr>';
        $cont = $cont + 1;
    } else {
        $alerta3 = "";
    }
}
/* * ************************************************************* ALERTA 4 ********************************************************************** */
$sql6a = "SELECT prd_codigo, prd_diarepo FROM productos WHERE prd_diarepo >='1' ORDER by prd_codigo DESC "; //seleccionar el ultimo registro de atras para adelante
$res6a = MySQL_query($sql6a, $link) or die(mysql_error());
$contar6a = 0;
while ($row6a = mysql_fetch_array($res6a)) {
    if ($row6a["prd_diarepo"] == $dia) {
        $contar6a = $contar6a + 1;
    }
}
if ($contar6a > 0) {//rellenar textos alerta 4 
    $alerta4 = '<tr class="texto5"><td>Hay  ' . $contar6a . ' Productos que tienen fecha de reposición HOY (' . $fecha . '). </td><td class="texto5b"><a href="reportes/listarRepo.php">Revisar Alerta</a></td></tr>';
    $cont = $cont + 1;
} else {
    $alerta4 = "";
}
/* * ************************************************************* ALERTA 5 ********************************************************************** */
$sql5 = "SELECT doc_id FROM documento d WHERE NOT EXISTS (SELECT NULL FROM proveedores P WHERE d.doc_rutpv=P.prov_rut)";
$data5 = mysql_query($sql5);
$contar5 = mysql_num_rows($data5);
if ($contar5 > 0) {//rellenar textos alerta 7
    $alerta5 = '<tr class="texto5"><td>Hay ' . $contar5 . ' Documentos que no tienen Proveedor Asociado</td><td class="texto5b"><a href="exportar/exportaExcelDocNAs.php">Revisar Alerta</a></td></tr>';
    $cont = $cont + 1;
} else {
    $alerta5 = "";
}
/* * ************************************************************* ALERTA 7 ********************************************************************** */
$sql7 = "SELECT doc_id FROM documento WHERE doc_estado=''";
$data7 = mysql_query($sql7);
$contar7 = mysql_num_rows($data7);
if ($contar7 > 0) {//rellenar textos alerta 7
    $alerta7 = '<tr class="texto5"><td>Hay ' . $contar7 . ' Documentos Pendientes de Regularizar</td><td class="texto5b"><a href="herramientas/buscarDocumento.php?op=1">Revisar Alerta</a></td></tr>';
    $cont = $cont + 1;
} else {
    $alerta7 = "";
}
/* * ************************************************************* ALERTA 8 ********************************************************************** */
if($grupo!="1"){
$sql8e = "SELECT grupo_prd_id FROM grupo_productos";
$res8e = MySQL_query($sql8e, $link)or die(mysql_error());
while ($row8e = mysql_fetch_array($res8e)) {
    $cuenta8a = 0;
    $grupo = $row8e["grupo_prd_id"];
    $sql8 = "SELECT grupo_prd_det_codigo FROM grupo_productos_detalle WHERE grupo_prd_id_id='$grupo'";
    $res8 = MySQL_query($sql8, $link)or die(mysql_error());
    while ($row8 = mysql_fetch_array($res8)) {
        $codigo1 = $row8["grupo_prd_det_codigo"];
        $sql8a = "SELECT tiponivel_codigo, tiponivel_critico FROM tiponivel WHERE tiponivel_codigo='$codigo1'";
        $res8a = MySQL_query($sql8a, $link) or die(mysql_error());
        $row8a = MySQL_Fetch_array($res8a);
        $contar8a = mysql_num_rows($res8a);
        $sql8b = "SELECT stock_codigo, stock_cantidad FROM stock WHERE stock_codigo='$codigo1'";
        $res8b = MySQL_query($sql8b, $link) or die(mysql_error());
        $row8b = MySQL_Fetch_array($res8b);
        $stock8b = $row8b["stock_cantidad"];
        if ($contar8a == 0) {
            $cuenta8a++;
        } else {
            $nivel8a = $row8a["tiponivel_critico"];
            if ($stock8b <= $nivel8a) {
                $cuenta8a++;
            }
        }
    }
    if ($cuenta8a > 0) {
        $sql8c = "UPDATE grupo_productos SET grupo_prd_rev='1' WHERE grupo_prd_id='$grupo'";
    } else {
        $sql8c = "UPDATE grupo_productos SET grupo_prd_rev='0' WHERE grupo_prd_id='$grupo'";
    }
    MySQL_query($sql8c, $link) or die(mysql_error());
}
$sql8d = "SELECT grupo_prd_rev FROM grupo_productos WHERE grupo_prd_rev='1'";
$res8d = MySQL_query($sql8d, $link) or die(mysql_error());
$cuenta8 = mysql_num_rows($res8d);
if ($cuenta8 > 0) {//rellenar textos alerta 8
    $alerta8 = '<tr class="texto5"><td>Hay ' . $cuenta8 . ' Grupos de productos que requieren atención</td><td class="texto5b"><a href="herramientas/agruparArticulo.php?op=1">Revisar Alerta</a></td></tr>';
    $cont = $cont + 1;
} else {
    $alerta8 = "";
}
}
/* * ************************************************************* ALERTA 9 ********************************************************************** */
$sql9 = "SELECT seg_fven FROM seguimiento WHERE seg_estado=1";
$res9 = MySQL_query($sql9, $link) or die(mysql_error()); //rellenar textos alerta 9
while ($row9 = mysql_fetch_array($res9)) {
    $venc = strtotime($row9["seg_fven"]);
    if ($venc <= $fechaactual) {
        $cont9 ++;
    }
}
if ($cont9 > 0) {
    $alerta9 = '<tr class="texto5"><td>Hay ' . $cont9 . ' seguimiento(s) de documentos vencidos</td><td class="texto5b"><a href="herramientas/buscarSeguimiento.php?op=1">Revisar Alerta</a></td></tr>';
    $cont = $cont + 1;
} else {
    $alerta9 = "";
}
/* * ************************************************************* ALERTA 10 ********************************************************************** */

if ($cargar > 0) {
    $sql10 = "SELECT stock_codigo FROM stock S WHERE NOT EXISTS (SELECT NULL FROM productos P WHERE s.stock_codigo=p.prd_codigo)"; //seleccionar el ultimo registro de atras para adelante de la carga de stock
    $res10 = MySQL_query($sql10, $link) or die(mysql_error());
    $cuenta10 = mysql_num_rows($res10);
    if ($cuenta10 > 0) {//rellenar textos alerta 10
        $alerta10 = '<tr class="texto5"><td>Hay ' . $cuenta10 . ' producto(s) que se han agregado al Stock del sistema, <br> pero no se ha(n) registrado correctamente en la base de Articulos.</td><td class="texto5b"><a href="herramientas/buscarStock.php?op=1">Revisar Alerta</a></td></tr>';
        $cont = $cont + 1;
    } else {
        $alerta10 = "";
    }
}
/* * ************************************************************* ALERTA 11 ********************************************************************** */
if ($cargar > 0) {
    $anio = date("Y");
    $sql11 = "SELECT stock_codigo FROM stock, pac WHERE pac_codigo = stock_codigo AND pac_ano = '$anio' AND ROUND((pac_cantidad / 12),0) > stock_cantidad";
    $res11 = MySQL_query($sql11, $link) or die(mysql_error());
    $cuenta11 = mysql_num_rows($res11);
    if ($cuenta11 > 0) {//rellenar textos alerta 11
        $alerta11 = '<tr class="texto5"><td>Hay ' . $cuenta11 . ' Producto(s) que tiene(n) Stock Menor a lo necesario para el PAC del Mes.</td><td class="texto5b"><a href="pedidos/pedidoArticuloPAC.php?op=1">Revisar Alerta</a></td></tr>';
        $cont = $cont + 1;
    } else {
        $alerta11 = "";
    }
}
/* * ************************************************************* ALERTA 12 ********************************************************************** */
$fdoc = date("Y-m-d", strtotime($fechaact . "- 7 days"));
$fdoc1 = date("Y-m-d", strtotime($fechaact . "- 6 days"));
$data12 = MySQL_query("call sp_fact_reclamar('$fdoc','$fdoc1')", $link)or die(mysql_error());
$row12 = mysql_fetch_array($data12);
$contar12 = $row12["C"];
if ($contar12 > 0) {//rellenar textos alerta 12
    $alerta12 = '<tr class="texto5"><td>Hay ' . $contar12 . ' Documentos (Factura(s)) Pendientes de Revisar para Reclamar en Acepta (8 días)</td><td class="texto5b"><a href="herramientas/buscarDocumento.php?op=1&rec=1">Revisar Alerta</a></td></tr>';
    $cont = $cont + 1;
} else {
    $alerta12 = "";
}
/* * ************************************************************ SIN ALERTAS ********************************************************************* */
if ($cont === 0) {//rellenar textos si no hay alertas
    $sinalerta = '<tr class="texto5"><td colspan="2">No hay Alertas que Mostrar en este momento.</td></tr>';
} else {
    $sinalerta = "";
}
/* * *********************************************************** Indicadores economicos ********************************************************************** */
$indicador = "<tr><td colspan='2' class='texto8j'><a href='reportes/IndicadorEc.php?anio=" . $anio . "'>▲</a>&nbsp;Indicadores Económicos de Hoy:"
        . "&nbsp;&nbsp;&nbsp;&nbsp;<img class='imgind' alt='indicador' title='indicador' src='img/dolar.png'>&nbsp;&nbsp; Dolar: $" . $ind["dolar"] . " "
        . "&nbsp;&nbsp;&nbsp;<img class='imgind' alt='indicador' title='indicador' src='img/uf.png'>&nbsp;&nbsp; U.F.: $" . $ind["uf"] . " "
        . "&nbsp;&nbsp;&nbsp;<img class='imgind' alt='indicador' title='indicador' src='img/utm.png'>&nbsp;&nbsp; U.T.M.: $" . $ind["utm"] . " "
        . "&nbsp;&nbsp;&nbsp;<img class='imgind' alt='indicador' title='indicador' src='img/euro.png'>&nbsp;&nbsp; Euro: $" . $ind["euro"] . "</td></tr>";

/* * ************************************************************* Imprimir Alertas ********************************************************************** */
$_SESSION['nvstockanalisis'] = 0;
echo '<table class="table2b">';
echo '<tr>';
echo '<td colspan="2" class="texto8g"><img class="imgiz" alt="Campana Alerta" title="Alertas" src="img/exclam.png">&nbsp;ALERTAS DEL SISTEMA:</td>';
echo '</tr>';
echo $alerta;
echo $alerta12;
echo $alerta7;
echo $alerta3;
echo $alerta4;
echo $alerta5;
echo $alerta1;
echo $alerta2;
echo $alerta11;
echo $alerta8;
echo $alerta9;
echo $alerta10;
echo $sinalerta;
echo $indicador;
echo '</table>';
//is_connected();
mysql_close($link);
?>
