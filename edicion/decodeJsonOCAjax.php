<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
ini_set('display_errors', false);
include_once("../include/conn.php");
include("../include/NumeroAletras.php"); // convertir numeros a letras
set_time_limit(15);
$link = Conectarse(); //Variable de coneccion
$oc1 = $_POST["oc"];
$op = $_POST["op"];
$dt = $_POST["dt"];
$comilladob = '"';
$comillasim = "'";
$datetime = date("Y-m-d H:i:s");
$usuario = $_SESSION["usuario"];

if ($dt === "1") {
    $btnc = "<button class='boton' type='button' onClick='window.location=".$comilladob."../herramientas/crearOC.php?oc=" . $oc1 . "&op=1".$comilladob."'>Crear OC en Sistema &nbsp;<img class='img' alt='crear' title='Crear OC' src='../img/download.png'></button>";
} else {
    $btnc="";
}
$rs = mysql_query("SELECT * FROM const_config WHERE const_cat ='ticket' AND const_est='1'", $link); //constantes de configuracion, reconoce todos los categoria ticket guardados
$row1 = mysql_fetch_array($rs);
$ticket = $row1["const_val"];

$url = 'https://api.mercadopublico.cl/servicios/v2/publico/ordenesdecompra.json?codigo=' . $oc1 . '&ticket=' . $ticket; //v2 y https desde julio 2022
//echo $url;
//$url="https://api.mercadopublico.cl/servicios/v1/publico/licitaciones.json?codigo=1057049-270-LR20&ticket=13206BF8-F5C7-445F-9958-E9512331E4C0";// ver licitaciones
if (ini_get('allow_url_fopen')) {//Es necesario tener habilitada la directiva allow_url_fopen para usar file_get_contents
    $json = file_get_contents($url);
} else {//De otra forma utilizamos cURL  
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $json = curl_exec($curl);
    curl_close($curl);
}
$data = json_decode($json, true);
if ($op === "1") {
    $btn = '<button class="boton" type="button" onClick="window.close();">Salir&nbsp;<img class="img" alt="Salir" title="Cerrar Ventana" src="../img/salir.png"></button>';
} else {
    $btn = '<button class="boton" type="button" onClick="history.go(-1);">Volver&nbsp;<img class="img" alt="volver" title="Volver a pantalla anterior" src="../img/undo.png"></button>';
}
echo '<table class="table3" align="center">';
$num = $data["Cantidad"];
//echo $url;
if ($num == 0) {
    echo "<tr><td class='texto6a' colspan='2'>No se ha encontrado la orden de compra seleccionada, o se encuentra cancelada en el portal...</td></tr>";
    echo '</table><br>';
    echo '<center>' . $btn . '</center>';
    exit;
} else {
    $tipo = $data["Listado"][0]["Tipo"];
    if ($tipo != "SE") {
        $tipooc = '<b style="color:blue;">' . $data["Listado"][0]["TipoOrdenCompra"] . '</b>';
    } else {
        //$tipooc = "<b><a href=https://www.mercadopublico.cl/Procurement/Modules/RFB/DetailsAcquisition.aspx?idlicitacion=" . $data["Listado"][0]["CodigoLicitacion"] . " target='_blank' title='Ver Licitacion'>" . $data["Listado"][0]["CodigoLicitacion"] . "</a></b>";
        $tipooc = "<b><a href=decodeJsonLic.php?id=" . $data["Listado"][0]["CodigoLicitacion"] . "&op=1 target='_blank' title='Ver Licitacion' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=550, width=1068 left=350 top=100" . $comillasim . "); return false;" . $comilladob . ">" . $data["Listado"][0]["CodigoLicitacion"] . "</a></b>";
		$nlic=$data["Listado"][0]["CodigoLicitacion"];
	}
    echo "<tr><td class='texto8g' colspan='2'>1.- Información General de la Orden de Compra:</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>N° Licitación o Tipo:</td><td class='texto9'>" . $tipooc . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Organismo Comprador:</td><td class='texto9'>" . $data["Listado"][0]["Comprador"]["NombreOrganismo"] . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Dirección Organismo Comprador:</td><td class='texto9'>" . $data["Listado"][0]["Comprador"]["DireccionUnidad"] . "</td></tr>";
    $obs = $data["Listado"][0]["Nombre"];	
	echo "<tr><td class='texto8f' style='width:320px;'>Nombre Orden de Compra:</td><td class='texto9'>" . $obs . "</td></tr>";
	$obs = str_replace(array('\\', ':', '*', '?', '"', '<', '>', '|', "'", "#"), ' ', trim($obs)); //reemplazar caracteres no validos
    echo "<tr><td class='texto8f' style='width:320px;'>Descripción Orden de Compra:</td><td class='texto9'>" . $data["Listado"][0]["Descripcion"] . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Fecha de Creación:</td><td class='texto9'>" . date('d/m/Y h:i A', strtotime(str_replace("T", " ", $data["Listado"][0]["Fechas"]["FechaCreacion"]))) . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Fecha de Envío:</td><td class='texto9'><b>" . date('d/m/Y h:i A', strtotime(str_replace("T", " ", $data["Listado"][0]["Fechas"]["FechaEnvio"]))) . "</b></td></tr>";
    if ($data["Listado"][0]["Fechas"]["FechaAceptacion"] == "") {
        echo "<tr><td class='texto8f' style='width:320px;'>Fecha de Aceptación:</td><td class='texto9'>Sin Info.</td></tr>";
    } else {
        echo "<tr><td class='texto8f' style='width:320px;'>Fecha de Aceptación:</td><td class='texto9'>" . date('d/m/Y h:i A', strtotime(str_replace("T", " ", $data["Listado"][0]["Fechas"]["FechaAceptacion"]))) . "</td></tr>";
    }
    if ($data["Listado"][0]["Fechas"]["FechaCancelacion"] == "") {
        echo "<tr><td class='texto8f' style='width:320px;'>Fecha de Cancelación:</td><td class='texto9'>Sin Info.</td></tr>";
    } else {
        echo "<tr><td class='texto8f' style='width:320px;'>Fecha de Cancelación:</td><td class='texto9' style='color: red;'><b>" . date('d/m/Y h:i A', strtotime(str_replace("T", " ", $data["Listado"][0]["Fechas"]["FechaCancelacion"]))) . "</b></td></tr>";
    }
    echo "<tr><td class='texto8f' style='width:320px;'>Estado Proveedor Orden de Compra:</td><td class='texto9'><b>" . $data["Listado"][0]["CodigoEstado"] . "</b>: " . $data["Listado"][0]["Estado"] . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Estado Establecimiento Orden de Compra:</td><td class='texto9'><b>" . $data["Listado"][0]["CodigoEstadoProveedor"] . "</b>: " . $data["Listado"][0]["EstadoProveedor"] . "</td></tr>";
    $item = $data["Listado"][0]["Items"]["Cantidad"];
    echo "<tr><td class='texto8f' style='width:320px;'>Artículos Comprados:</td><td class='texto9'>" . $item . "&nbsp;&nbsp;&nbsp;<img src='../img/eye.png' class='imgcopy' style='width:33px; height:30px;' title='mostrar/ocultar los Productos asociados'  onclick=" . $comilladob . "mostrarDet();" . $comilladob . "></td></tr>";
    echo "<tr><td class='texto8g' colspan='2'><table class='table3' id='table' style='display:none;'></td></tr>";
    echo "<tr><td class='texto8j'>Codigo ONU</td><td class='texto8j'>Producto/Servicio</td><td class='texto8j'>Cantidad</td><td class='texto8j'>U.M.</td><td class='texto8j'>Especificación Comprador</td><td class='texto8j'>Especificación Proveedor</td><td class='texto8j'>Prec. Unit</td><td class='texto8j'>Desctos.</td><td class='texto8j'>Cargos</td><td class='texto8j'>Tot. Impuestos</td><td class='texto8j'>Total Final</td>";
    for ($i = 0; $i < $item; $i++) {
        echo "<tr class='texto9a' style='border:0px;'><td>" . $data["Listado"][0]["Items"]["Listado"][$i]["CodigoProducto"] . "</td>";
        echo "<td style='width:100px;'>" . $data["Listado"][0]["Items"]["Listado"][$i]["Producto"] . "</td>";
        echo "<td style='text-align: center;'>" . $data["Listado"][0]["Items"]["Listado"][$i]["Cantidad"] . "</td>";
        echo "<td>" . $data["Listado"][0]["Items"]["Listado"][$i]["Unidad"] . "</td>";
        echo "<td style='width:100px;'>" . $data["Listado"][0]["Items"]["Listado"][$i]["EspecificacionComprador"] . "</td>";
        echo "<td style='width:100px;'>" . $data["Listado"][0]["Items"]["Listado"][$i]["EspecificacionProveedor"] . "</td>";
        echo "<td>$" . number_format($data["Listado"][0]["Items"]["Listado"][$i]["PrecioNeto"], 0, '', '.') . "</td>";
        echo "<td>$" . number_format($data["Listado"][0]["Items"]["Listado"][$i]["TotalDescuentos"], 0, '', '.') . "</td>";
        echo "<td>$" . number_format($data["Listado"][0]["Items"]["Listado"][$i]["TotalCargos"], 0, '', '.') . "</td>";
        echo "<td>$" . number_format($data["Listado"][0]["Items"]["Listado"][$i]["TotalImpuestos"], 0, '', '.') . "</td>";
        echo "<td>$" . number_format($data["Listado"][0]["Items"]["Listado"][$i]["Total"], 0, '', '.') . "</td></tr>";
    }
    echo "</table>";
    echo "<tr><td class='texto8f' style='width:320px;'>Total Neto Orden de Compra:</td><td class='texto9'>$" . number_format($data["Listado"][0]["TotalNeto"], 0, '', '.') . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>% Impuesto Orden de Compra:</td><td class='texto9'>" . $data["Listado"][0]["PorcentajeIva"] . "%</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Total Impuesto Orden de Compra:</td><td class='texto9'>$" . number_format($data["Listado"][0]["Impuestos"], 0, '', '.') . "</td></tr>";
    $dscto = number_format($data["Listado"][0]["Descuentos"], 0, '', '');
    if ($dscto != 0) {
        $dscto = (($dscto / $data["Listado"][0]["TotalNeto"]) * 100);
        $verdscto = "<tr><td class='texto8f' style='width:320px;'>Porcentaje Descuento Orden de Compra:</td><td class='texto9'>" . $dscto . "%</td></tr>";
    }
    echo "<tr><td class='texto8f' style='width:320px;'>Total Descuento Orden de Compra:</td><td class='texto9'>$" . number_format($data["Listado"][0]["Descuentos"], 0, '', '.') . "</td></tr>";
    echo $verdscto;
    echo "<tr><td class='texto8f' style='width:320px;'>Total Valorizado Orden de Compra:</td><td class='texto9'><b>$" . number_format($data["Listado"][0]["Total"], 0, '', '.') . "</b> (". convertir(number_format($data["Listado"][0]["Total"], 0, '', ''))." PESOS.)</td></tr>";
    
    echo "<tr><td class='texto8g' colspan='2'>2.- Información del Proveedor:</td></tr>";
    $mailc = strtolower(trim($data["Listado"][0]["Proveedor"]["MailContacto"]));
    $fono = "+" . preg_replace('([^A-Za-z0-9])', "", trim($data["Listado"][0]["Proveedor"]["FonoContacto"]));
    //$fono = str_replace("0","",$fono,0-4);
    $nom = preg_replace("/\s+/", " ", ucwords(mb_strtolower(trim($data["Listado"][0]["Proveedor"]["NombreContacto"]))));
    $nomprv = preg_replace("/\s+/", " ", mb_strtoupper(trim($data["Listado"][0]["Proveedor"]["Nombre"])));
    $dir = ucwords(mb_strtolower(trim($data["Listado"][0]["Proveedor"]["Direccion"])));
    $dir = str_replace(array('\\', '/', ':', '*', '?', '"', '<', '>', '|', "'", "#"), ' ', $dir); //reemplazar caracteres no validos
    $com = ucwords(mb_strtolower(trim($data["Listado"][0]["Proveedor"]["Comuna"])));
    $giro = ucwords(mb_strtolower(trim($data["Listado"][0]["Proveedor"]["Actividad"])));
    $dirfin = $dir . ", " . $com;
    $rut = substr(str_replace(".", "", $data["Listado"][0]["Proveedor"]["RutSucursal"]), 0, -2);
    $dv = substr(str_replace(".", "", $data["Listado"][0]["Proveedor"]["RutSucursal"]), -1);
    echo "<tr><td class='texto8f' style='width:320px;'>RUT Proveedor:</td><td class='texto9'>" . $data["Listado"][0]["Proveedor"]["RutSucursal"] . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Nombre Proveedor:</td><td class='texto9'>" . $nomprv . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Giro Proveedor:</td><td class='texto9'>" . $giro . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Dirección Proveedor:</td><td class='texto9'>" . $dir . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Comuna Proveedor:</td><td class='texto9'>" . $com . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Contacto Proveedor:</td><td class='texto9'>" . $nom . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Cargo Contacto:</td><td class='texto9'>" . $data["Listado"][0]["Proveedor"]["CargoContacto"] . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Mail Proveedor:</td><input type='hidden' value='" . $mailc . "' id='mail'><td class='texto9'>" . $mailc . "&nbsp;&nbsp;&nbsp;<img src='../img/copy1.png' class='imgcopy' title='copiar al portapapeles'  onclick=" . $comilladob . "copiarPorta('#mail');" . $comilladob . ">&nbsp;<span id='div' style='height: 15px;'></td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Teléfono Proveedor:</td><td class='texto9'>" . $fono . "&nbsp;&nbsp;&nbsp;&nbsp;<a href=tel:" . $fono . "><img class='imgnormal' alt='tel' title='Llamar' src='../img/tel1.png'></a></td></tr>";
    echo "<tr><td class='texto8g' colspan='2'>3.- Información del Comprador:</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Nombre Comprador:</td><td class='texto9'>" . $data["Listado"][0]["Comprador"]["NombreContacto"] . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Cargo Comprador:</td><td class='texto9'>" . $data["Listado"][0]["Comprador"]["CargoContacto"] . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Mail Comprador:</td><td class='texto9'>" . $data["Listado"][0]["Comprador"]["MailContacto"] . "</td></tr>";
    $fonocom = "+" . preg_replace('([^A-Za-z0-9])', "", trim($data["Listado"][0]["Comprador"]["FonoContacto"]));
    echo "<tr><td class='texto8f' style='width:320px;'>Fono Comprador:</td><td class='texto9'>" . $fonocom . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Unidad Comprador:</td><td class='texto9'>" . $data["Listado"][0]["Comprador"]["NombreUnidad"] . "</td></tr>";
    echo "<tr><td class='texto8g' colspan='2'>4.- Información Adicional:</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Forma de Pago:</td><td class='texto9'>" . $data["Listado"][0]["DatosPago"]["FormaPago"] . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Dirección de Entrega:</td><td class='texto9'>" . $data["Listado"][0]["DatosPago"]["DireccionDespacho"] . "</td></tr>";
	    if ($data["Listado"][0]["DatosPago"]["FechaEntregaProductos"] == "0001-01-01T00:00:00") {
        echo "<tr><td class='texto8f' style='width:320px;'>Fecha de Entrega:</td><td class='texto9'>Sin Info.</td></tr>";
    } else {
        echo "<tr><td class='texto8f' style='width:320px;'>Fecha de Entrega:</td><td class='texto9'>" . date('d/m/Y', strtotime(str_replace("T", " ", $data["Listado"][0]["DatosPago"]["FechaEntregaProductos"]))) . "</td></tr>";
		//echo $data["Listado"][0]["Fechas"]["FechaAceptacion"];
		//echo "<tr><td class='texto8f' style='width:320px;'>Fecha de Entrega:</td><td class='texto9'>" . $data["Listado"][0]["DatosPago"]["FechaEntregaProductos"]. "</td></tr>";
	}	
    echo "<tr><td class='texto8f' style='width:320px;'>Proyecto PAC:</td><td class='texto9'>" . $data["Listado"][0]["PacAsociados"]["Listado"][0]["Proyecto"] . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Compra Validada Por:</td><td class='texto9'>" . $data["Listado"][0]["AutorizadoresOrdenCompra"]["Listado"][0]["Nombre"] . "</td></tr>";
}
echo '</table><br>';
$fecha = date("y-m-d H:i:s");
$i = 0;
$sql = "SELECT prov_direccion, prov_telefono, prov_nomcontacto, prov_email, prov_giro FROM proveedores WHERE prov_rut='$rut'";
$res1 = mysql_query($sql, $link);
$cont = mysql_num_rows($res1);
if ($cont == 0) {//crear proveedor si no existe
    $sql1 = "INSERT INTO `proveedores`(`prov_rut`, `prov_dv`, `prov_nombre`, `prov_giro`, `prov_direccion`, `prov_nomcontacto`, `prov_email`, `prov_telefono`, `prov_estado`, `prov_fechacreacion`) 
	VALUES ('$rut','$dv','$nomprv','$giro','$dir','$nom','$mailc','$fono','1','$fecha')";
    mysql_query($sql1, $link);
} else {//actualizar datos de proveedor si no existen
    $row = MySQL_Fetch_array($res1);
    $sql2 = "UPDATE proveedores SET";
    if ($row["prov_direccion"] == "") {
        $sql2 .= " prov_direccion='$dirfin', ";
        $i++;
    }
    if ($row["prov_giro"] == "") {
        $sql2 .= " prov_giro='$giro', ";
        $i++;
    }
    if ($row["prov_email"] == "") {
        $sql2 .= " prov_email='$mailc', ";
        $i++;
    }
    if ($row["prov_telefono"] == "" && strlen($fono) > 5) {
        $sql2 .= " prov_telefono='$fono', ";
        $i++;
    }
    if ($row["prov_nomcontacto"] == "") {
        $sql2 .= " prov_nomcontacto='$nom', ";
        $i++;
    }
    $sql2 .= "prov_fechacreacion='$fecha' WHERE prov_rut='$rut'";
    if ($i != 0) {
        mysql_query($sql2, $link);
    }
}
$sql2 = "SELECT saldo_oc FROM saldos WHERE saldo_oc='$oc1'";
$res2 = mysql_query($sql2, $link);
$cont2 = mysql_num_rows($res2);

if ($cont2 != 0) {//ver si existe oc para guardar la observacion
$sql3 = "SELECT oc_obs_det,oc_obs_id FROM oc_obs WHERE oc_obs_oc='$oc1'";
$res3 = mysql_query($sql3, $link);
$cont3 = mysql_num_rows($res3);
	if ($cont3 == 0) {//crear observacion si no existe
		$sql4 = "INSERT INTO oc_obs (oc_obs_oc, oc_obs_det, oc_obs_fecha, oc_obs_user, oc_obs_lic) VALUES ('$oc1', '$obs','$datetime','$usuario','$nlic')";
		MySQL_query($sql4, $link);
	}
}
mysql_close($link);
echo '<center>' . $btn . '&nbsp;&nbsp;&nbsp;&nbsp;<button class="boton" type="button" onclick="window.print();">Imprimir&nbsp;<img class="img" alt="imprimir pantalla" title="imprimir pantalla" src="../img/print.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;' . $btnc . '</center>';
?>