<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
include("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
$buscar = $_POST['saldo'];
$vertodo = $_POST["chkall"];
$anio=$_POST["anio"];
$anio2=$_POST["anio2"]." 23:59:59";
$fmod=$_POST["fmod"];
$comilladob = '"';
$comillasim = "'";
//$anio2= date("Y-m-d",strtotime($anio."+ 1 days"));
$fmod1=date("Y-m-d",strtotime($fmod."+ 1 days"));
if($anio!=""){
	$anio1= "AND doc_fechacarga BETWEEN '$anio' AND '$anio2' ";
}
if($fmod!=""){
	$fmod2= "AND doc_fechamod BETWEEN '$fmod' AND '$fmod1' ";
}

if (empty($buscar) and $vertodo == "true") {
    buscar($buscar,$anio1,$fmod2);
}
else if (!empty($buscar)) {
    buscar($buscar,$anio1,$fmod2);
}
else {
    echo "<script>alert('Error, ingrese algun dato para Buscar');</script>";
}
function buscar($saldo,$anio1,$fmod2) {
    global $comilladob;
    global $comillasim;
    if (strlen($saldo) > 20) {//si se escriben menos de 20 caracteres buscara
        $sql1 = $saldo;
        $_SESSION['query'] = $sql1;
        
    } else {
        $sql1 = "SELECT DISTINCT COUNT(doc_noc) AS total, SUM(doc_montototal) AS suma, prov_rut, prov_nombre, doc_noc, doc_ndoc, prov_dv "
                . "FROM proveedores, documento "
                . "WHERE doc_rutpv=prov_rut ".$anio1." ".$fmod2." "
                . "AND (prov_nombre LIKE '%" . $saldo . "%' or prov_rut LIKE '%" . $saldo . "%' or doc_ndoc LIKE '%" . $saldo . "%' or doc_noc LIKE '%" . $saldo . "%' )"
                . "GROUP BY doc_noc ASC";
    }
    //echo $sql1;
    $data = mysql_query($sql1);
    $_SESSION['query'] = $sql1;
    $contar = mysql_num_rows($data);
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
        echo '<thead><tr><th style="width:120px;">RUT Proveedor</th><th>Nombre Proveedor</th><th style="width:150px;">Orden de Compra</th><th style="width:110px;">N° Documentos Registrados</th><th style="width:120px;">Total Valorizado</th><th style="width:110px; word-wrap: break-word;" data-sorter="false" data-filter="false">Acciones:</th></tr></thead>';
        while ($row = mysql_fetch_array($data)) {
             printf("<tr><td><b>%s</b></td><td align='left'><b>%s</b></td><td><b>%s</b></td><td><b>%s</b></td><td><b>$ %s</b></td><td>%s</td></tr>", "<a href=../edicion/modificarProveedor.php?id=" . $row["prov_rut"] . "&option=1 target='_blank' title='Ver detalle proveedor' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=540, width=800 left=300 top=100" . $comillasim . "); return false;" . $comilladob . ">" . number_format($row["prov_rut"], 0, '.', '.') . "-" . $row["prov_dv"] . "</a>", $row["prov_nombre"], $row["doc_noc"], $row["total"], number_format($row["suma"], 0, ',', '.'), "<a href=generarDocOC.php?id=" . $row["doc_noc"] . "&rut=".$row["prov_rut"]." target='_blank'><img border='0' alt='reporte' title='Generar Reporte OC: " . $row["doc_noc"] . "' src='../img/check.png' width='25' height='25'></a>");
        }
    }
    echo '</table>';
echo '<script type="text/javascript">$(document).ready(function () {$("#tabladoc").tablesorter();});</script>';
}
mysql_close($link);
?>