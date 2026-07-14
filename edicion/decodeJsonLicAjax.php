<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
ini_set('display_errors', false);
include_once("../include/conn.php");
set_time_limit(15);
$id = $_POST["id"];
$op = $_POST["op"];
$comilladob = '"';
$comillasim = "'";
$link = Conectarse(); //Variable de coneccion
$rs = mysql_query("SELECT * FROM const_config WHERE const_cat ='ticket' AND const_est='1'", $link); //constantes de configuracion, reconoce todos los categoria ticket guardados
$row1 = mysql_fetch_array($rs);
$ticket = $row1["const_val"];

$url = 'https://api.mercadopublico.cl/servicios/v1/publico/licitaciones.json?codigo=' . $id . '&ticket=' . $ticket; //v2 y https desde julio 2022
//echo $url;
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

if ($num == 0) {
    echo "<tr><td class='texto6a' colspan='2'>No se ha encontrado la Licitacion seleccionada, o se encuentra cancelada en el portal...</td></tr>";
    echo '</table><br>';
    echo '<center>' . $btn . '</center>';
    exit;
} else {
    echo "<tr><td class='texto8g' colspan='2'>1.- Información General de la Licitación:</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>ID Mercado Público:</td><td class='texto9'><b><a href=https://www.mercadopublico.cl/Procurement/Modules/RFB/DetailsAcquisition.aspx?idlicitacion=" . $data["Listado"][0]["CodigoExterno"] . " title='Ver Licitacion'>" . $data["Listado"][0]["CodigoExterno"] . "</a></b></td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Organismo Comprador:</td><td class='texto9'>" . $data["Listado"][0]["Comprador"]["NombreOrganismo"] . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Dirección Organismo Comprador:</td><td class='texto9'>" . $data["Listado"][0]["Comprador"]["DireccionUnidad"] . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Comuna Unidad:</td><td class='texto9'>" . $data["Listado"][0]["Comprador"]["ComunaUnidad"] . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Región:</td><td class='texto9'>" . $data["Listado"][0]["Comprador"]["RegionUnidad"] . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Nombre Unidad:</td><td class='texto9'>" . $data["Listado"][0]["Comprador"]["NombreUnidad"] . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Nombre Licitación:</td><td class='texto9'>" . $data["Listado"][0]["Nombre"] . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Descripción Licitación:</td><td class='texto9'>" . $data["Listado"][0]["Descripcion"] . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Nombre Usuario:</td><td class='texto9'><b>" . $data["Listado"][0]["Comprador"]["NombreUsuario"] . "</b></td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Cargo Usuario:</td><td class='texto9'>" . $data["Listado"][0]["Comprador"]["CargoUsuario"] . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Moneda:</td><td class='texto9'>" . $data["Listado"][0]["Moneda"] . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Fecha de Publicacion:</td><td class='texto9'>" . date('d/m/Y h:i A', strtotime(str_replace("T", " ", $data["Listado"][0]["Fechas"]["FechaPublicacion"]))) . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Fecha de Respuesta Preguntas:</td><td class='texto9'>" . date('d/m/Y h:i A', strtotime(str_replace("T", " ", $data["Listado"][0]["Fechas"]["FechaPubRespuestas"]))) . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Fecha de Cierre:</td><td class='texto9'>" . date('d/m/Y h:i A', strtotime(str_replace("T", " ", $data["Listado"][0]["Fechas"]["FechaCierre"]))) . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Estado Licitación:</td><td class='texto9'><b>" . $data["Listado"][0]["Estado"] . "</b></td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Fecha de Adjudicación:</td><td class='texto9'><b>" . date('d/m/Y h:i A', strtotime(str_replace("T", " ", $data["Listado"][0]["Fechas"]["FechaAdjudicacion"]))) . "</b></td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Oferentes de la Licitación:</td><td class='texto9'>" . $data["Listado"][0]["Adjudicacion"]["NumeroOferentes"] . "</td></tr>";
    if ($data["Listado"][0]["Adjudicacion"]["UrlActa"] == "") {
        echo '<tr><td class="texto8f" style="width:320px;">Acta de Adjudicación:</td><td class="texto9"><img border="0" alt="Documento no Encontrado" title="Acta no Encontrada" src="../img/noenc.png" width="20" height="20"></td></tr>';
    } else {
        echo "<tr><td class='texto8f' style='width:320px;'>Acta de Adjudicación:</td><td class='texto9'><b><a href=" . $data["Listado"][0]["Adjudicacion"]["UrlActa"] . " target='_blank' title='Ver Acta' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=550, width=800 left=300 top=100" . $comillasim . "); return false;" . $comilladob . ">Ver Acta Adjudicacion</a></b></td></tr>";
    }
    echo "<tr><td class='texto8f' style='width:320px;'>Monto Estimado de la Licitación:</td><td class='texto9'>$ " . number_format($data["Listado"][0]["MontoEstimado"], 0, '', '.') . "</td></tr>";
    $item = $data["Listado"][0]["Items"]["Cantidad"];
    echo "<tr><td class='texto8f' style='width:320px;'>Artículos Licitados:</td><td class='texto9'>" . $item . "&nbsp;&nbsp;&nbsp;<img src='../img/eye.png' class='imgcopy' style='width:33px; height:30px;' title='mostrar/ocultar los Productos asociados'  onclick=" . $comilladob . "mostrarDet();" . $comilladob . "></td></tr>";
    echo "<tr><td class='texto8g' colspan='2'><table class='table3' id='table' style='display:none;'></td></tr>";
    echo "<tr><td class='texto8j'>N°</td><td class='texto8j'>Codigo ONU</td><td class='texto8j'>Categoría/Producto</td><td class='texto8j'>Descripcion</td><td class='texto8j'>Cantidad Licitada</td><td class='texto8j'>U.M.</td><td class='texto8j'>Proveedor Adjudicado</td><td class='texto8j'>Cantidad Adjudicada</td><td class='texto8j'>Precio Neto Adjudicado</td><td class='texto8j'>Total Neto Linea</td></tr>";
    for ($i = 0; $i < $item; $i++) {
        if ($data["Listado"][0]["Items"]["Listado"][$i]["Adjudicacion"]["RutProveedor"] == "") {
            $adj = "<td style='width:180px;text-align:center;color:red;'>No Adjudicado</td>";
            $color = "color:red;";
        } else {
			$rut1=substr(str_replace(".","",$data["Listado"][0]["Items"]["Listado"][$i]["Adjudicacion"]["RutProveedor"]), 0, -2);//limpia los puntos del rut, y corta el guion y digito
            //$adj = "<td style='width:180px;text-align:center;color:blue;'>" . $data["Listado"][0]["Items"]["Listado"][$i]["Adjudicacion"]["RutProveedor"] . "<br>" . $data["Listado"][0]["Items"]["Listado"][$i]["Adjudicacion"]["NombreProveedor"] . "</td>";
            $adj = "<td style='width:180px;text-align:center;color:blue;'><a href=../edicion/modificarProveedor.php?id=" . $rut1 . "&option=1 target='_blank' title='Ver detalle proveedor' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=550, width=800 left=300 top=100" . $comillasim . "); return false;" . $comilladob . ">" . $data["Listado"][0]["Items"]["Listado"][$i]["Adjudicacion"]["RutProveedor"] . "</a><br>" . $data["Listado"][0]["Items"]["Listado"][$i]["Adjudicacion"]["NombreProveedor"] . "</td>";
			$color = "color:black;";
		}
		$o=$i+1;
		echo "<tr class='texto9a' style='border:0px;" . $color . "'><td style='text-align: center;'><span class='textoredondo3'>" . $o . "</span></td>";
        echo "<td style='width:100px;" . $color . "'>" . $data["Listado"][0]["Items"]["Listado"][$i]["CodigoProducto"] . "</td>";
        echo "<td style='width:100px;'>" . $data["Listado"][0]["Items"]["Listado"][$i]["Categoria"] . " / " . $data["Listado"][0]["Items"]["Listado"][$i]["NombreProducto"] . "</td>";
        echo "<td style='width:100px;'>" . $data["Listado"][0]["Items"]["Listado"][$i]["Descripcion"] . "</td>";
        echo "<td style='text-align: center;'>" . $data["Listado"][0]["Items"]["Listado"][$i]["Cantidad"] . "</td>";
        echo "<td style='width:80px;text-align: center;'>" . $data["Listado"][0]["Items"]["Listado"][$i]["UnidadMedida"] . "</td>";
        echo $adj;
        echo "<td style='width:100px;text-align: center;color:blue;'>" . $data["Listado"][0]["Items"]["Listado"][$i]["Adjudicacion"]["Cantidad"] . "</td>";
        echo "<td style='width:100px;text-align: center;'>$" . number_format($data["Listado"][0]["Items"]["Listado"][$i]["Adjudicacion"]["MontoUnitario"], 0, '', '.') . "</td>";
        $lin = ($data["Listado"][0]["Items"]["Listado"][$i]["Adjudicacion"]["Cantidad"] * $data["Listado"][0]["Items"]["Listado"][$i]["Adjudicacion"]["MontoUnitario"]);
        echo "<td style='width:100px;text-align: center;'>$" . number_format($lin, 0, '', '.') . "</td></tr>";
        $suma = $suma + $lin;
    }
    echo "</table>";
    echo "<tr><td class='texto8f' style='width:320px;'>Total Neto Licitación:</td><td class='texto9'>$ " . number_format($suma, 0, '', '.') . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Valor Impuesto Licitación:</td><td class='texto9'>$ " . number_format(($suma * 0.19), 0, '', '.') . "</td></tr>";
    echo "<tr><td class='texto8f' style='width:320px;'>Total Adjudicado Licitación:</td><td class='texto9'><b>$ " . number_format(($suma * 1.19), 0, '', '.') . "</b></td></tr>";
}
echo '</table><br>';
mysql_close($link);
echo '<center>' . $btn . '&nbsp;&nbsp;&nbsp;&nbsp;<button class="boton" type="button" onclick="window.print();">Imprimir&nbsp;<img class="img" alt="imprimir pantalla" title="imprimir pantalla" src="../img/print.png"></button></center>';
?>