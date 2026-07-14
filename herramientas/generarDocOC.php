<!DOCTYPE html>
<!-- fecha creacion 22/10/2019 hp
Sigbod John Vaccarella
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
include("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
$ido = $_GET['id'];
$rut = $_GET['rut'];
$sql1 = "SELECT * FROM documento, proveedores WHERE doc_noc='$ido' AND doc_rutpv='$rut' AND prov_rut=doc_rutpv ORDER BY doc_ndoc DESC";
$data = mysql_query($sql1);
$contar = mysql_num_rows($data);
$comilladob = '"';
$comillasim = "'";
$sql3 = "SELECT SUM((saldo_pendiente * saldo_preciounit)*1.19) AS total FROM saldos WHERE saldo_oc='$ido'";
$data3 = mysql_query($sql3);
$row3 = mysql_fetch_array($data3);
$suma3 = $row3["total"];
$sql4 = "SELECT SUM((saldo_solicitado * saldo_preciounit)*1.19) AS total FROM saldos WHERE saldo_oc='$ido'";
$data4 = mysql_query($sql4);
$row4 = mysql_fetch_array($data4);
$suma4 = $row4["total"];
$sql5 = "SELECT SUM((saldo_recibido * saldo_preciounit)*1.19) AS total FROM saldos WHERE saldo_oc='$ido'";
$data5 = mysql_query($sql5);
$row5 = mysql_fetch_array($data5);
$suma5 = $row5["total"];
$sql6 = "SELECT count(*) AS total FROM saldos WHERE saldo_oc='$ido'";
$data6 = mysql_query($sql6);
$row6 = mysql_fetch_array($data6);
$suma6 = $row6["total"];
if ($suma3 == 0) {
    $estado = '<b style="color:red;">(Recepción Completa)</b>';
} else {
    if ($suma5 == 0) {
        $estado = '<b style="color:blue;">(Sin Recepciones hasta ahora)</b>';
    } else {
        $estado = '<b style="color:green;">(Recepción Parcial)</b>';
    }
}
?>
<html lang="es">
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />          
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>
        <script src="../js/tablesorter.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $(window).scroll(function () {
                    if ($(this).scrollTop() > 100) {
                        $('#scroll').fadeIn();
                    } else {
                        $('#scroll').fadeOut();
                    }
                });
                $('#scroll').click(function () {
                    $("html, body").animate({scrollTop: 0}, 600);
                    return false;
                });
            });
        </script>

        <script type="text/javascript">
            $(function () {
                $("#subdoc").tablesorter();
            });
        </script>
        <script type="text/javascript">
            function cambiaImg(id) {
                var idx = id.replace("link_", "img_");
                $("#" + id).click(function () {
                    var nuevaImagen = "../img/fact1.png";
                    $("#" + idx).attr("src", nuevaImagen);  // cambia para saber que se revisó
                });
            }
        </script>
		<script type="text/javascript">
            function actualizar() {
                location.reload(true);
            }
            setInterval("actualizar()", 90000);//Función para actualizar cada 90 segundos(90000 milisegundos)
        </script>
        <title>SigBod - Detalle Documentos OC</title>
    </head>
    <body class="fondo">        
        <table class="table2e">
            <tr>
                <td class="texto" colspan="2">DETALLE DE DOCUMENTOS POR ORDEN DE COMPRA</td>
            </tr>
            <tr><td class="texto_ancho">N° Registros Asociados:</td><td class="texto9"><b class="texto12"><?= $contar ?></b></td></tr>
            <tr><td class="texto8">Orden de Compra:</td><td class="texto0a"><?= $ido ?>, Estado: <?= $estado ?></td></tr>
            <tr><td colspan="2" class="texto8a"></td></tr>
        </table>
        <table class="table2e" id="subdoc">
            <?php
            if ($contar == 0) {
                echo "<tr><td>No hay Registros para Mostrar.</td></tr>";
            } else {
                echo '<thead><tr><th width="30">N° Reg.</th><th>Num. Doc.</th><th width="50">Tipo Doc.</th><th width="140">Proveedor</th><th>Fecha Doc.</th><th width="50">Fecha creación</th><th width="50">Fecha Modif.</th><th>Monto Total</th><th width="40">Cant. Subdoc.</th><th width="40">Origen Doc.</th><th width="150">Observaciones</th><th>Estado</th><th>Mail</th><th style="width:130px; word-wrap: break-word;" data-sorter="false" data-filter="false">Acciones:</th></tr></thead>';
                while ($row = mysql_fetch_array($data)) {
                    $id = $row["doc_id"];
                    if ($row["doc_subdoc_cuenta"] > 0) {
                        $subdoc = "<a href=../herramientas/buscarSubdocumento.php?id=" . $id . "&tipo=1 target='_blank' title='Visualizar Detalles' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=560, width=840 left=250 top=30" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='Ver subdoc' title='Visualizar Subdocumentos' src='../img/lupa.png' width='20' height='20'></a>|";
                        $sql2 = "SELECT subdoc_tipo, subdoc_monto, subdoc_id FROM subdocumento WHERE subdoc_id='" . $row['doc_id'] . "'";
                        $data2 = mysql_query($sql2);
                        while ($row2 = mysql_fetch_array($data2)) {
                            if ($row2["subdoc_tipo"] == "Recep. de Bodega") {
                                $rec = $rec + $row2["subdoc_monto"];
                            }
                            if ($row2["subdoc_tipo"] == "Nota de Credito") {
                                $dev = $dev + $row2["subdoc_monto"];
                            }
                        }
                    } else {
                        $subdoc = "<img border='0' alt='Documento no Encontrado' title='Subdocumento no Encontrado' src='../img/noenc.png' width='20' height='20'>|";
                    }
                    if ($row["doc_fechadoc"] != "0000-00-00") {
                        $fechadoc = date("d/m/Y", strtotime($row["doc_fechadoc"]));
                    } else {
                        $fechadoc = "";
                    }
                    if ($row["doc_fechamod"] != "0000-00-00 00:00:00") {
                        $fechamod = date("d/m/Y", strtotime($row["doc_fechamod"]));
                    } else {
                        $fechamod = "";
                    }
                    if ($row["doc_origen"] != "") {
                        $origen = $row["doc_origen"];
                    } else {
                        $origen = "";
                    }if ($row["doc_norigen"] != 0) {
                        $origen = $origen . " N°" . $row["doc_norigen"];
                    } else {
                        $origen = $origen . "";
                    } if ($row["doc_fechaorigen"] != "0000-00-00") {
                        $origen = $origen . ", Fecha: " . date("d/m/Y", strtotime($row["doc_fechaorigen"]));
                    } else {
                        $origen = $origen . "";
                    }
                    if ($row["doc_estado"] == "") {
                        $pend = $pend + $row["doc_montototal"];
                    }
                    $suma = $suma + $row["doc_montototal"];
                    $link1 = $row["doc_url"];
                    $mail = "|<a href=../herramientas/enviarMailFact.php?id=" . $id . "&rt=" . $rut . " target='_blank' title='Enviar Mail' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=200, width=480 left=450 top=20" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='Enviar mail' title='Enviar mail para solicitar respaldos de recepcion' src='../img/mail.png' width='20' height='20'></a>";
                    if ($link1 <> "") {
                        $url = "|<a href=$link1 target='_blank' title='Ver DTE' id='link_" . $id . "'  onfocus='cambiaImg(this.id);' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=560, width=840 left=250 top=30" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='doc' title='Ver DTE' src='../img/fact.png' width='19' height='20' id='img_" . $id . "'></a>";
                        $traza = "|<a href=https://escritorio.acepta.com/traza/?url=" . $link1 . " target='_blank' title='Ver Traza' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=640, width=840 left=250 top=10" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='doc' title='Ver Traza en Acepta' src='../img/track.png' width='20' height='20'></a>";
                    } else {
                        $url = "";
                        $traza = "";
                    }
                    $rs = mysql_query("SELECT mail_doc,mail_fecha,mail_id FROM mail WHERE mail_doc ='$id' ORDER BY mail_id DESC LIMIT 1", $link);//saber si se ha enviado mail al proveedor por respaldos
                    $row2 = mysql_fetch_array($rs);
                    $mailen = $row2["mail_doc"];
                    if ($mailen != "") {
                        $mailen = "<a href=../reportes/envioMail.php?id=".$id." target='_blank' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=400, width=840 left=250 top=30" . $comillasim . "); return false;" . $comilladob . "><img width='25' height='23' src='../img/mailenv.png' title='Correo ya enviado a proveedor, el: " . date("d/m/Y, H:i", strtotime($row2["mail_fecha"])) . "'></a>";
                    } else {
                        $mailen = "";
                    }
                    printf("<tr><td><i>%s</i></td><td><b>%s</b></td><td>%s</td><td align='left'><b>%s</b></td><td>%s</td><td><b>%s</b></td><td>%s</td><td><b>$</b>%s</td><td>%s</td><td>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td><td>%s</td></tr>", $id, $row["doc_ndoc"], $row["doc_tipodoc"], "<a href=../edicion/modificarProveedor.php?id=" . $row["prov_rut"] . "&option=1 target='_blank' title='Ver detalle proveedor' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=540, width=800 left=300 top=100" . $comillasim . "); return false;" . $comilladob . ">" . number_format($row["prov_rut"], 0, '.', '.') . "-" . $row["prov_dv"] . "</a>&nbsp;" . " " . $row["prov_nombre"], $fechadoc, date("d/m/Y, H:i", strtotime($row["doc_fechacarga"])), $fechamod, number_format($row["doc_montototal"], 0, ',', '.'), $row["doc_subdoc_cuenta"], $origen, $row["doc_obs"], $row["doc_estado"], $mailen, $subdoc . "<a href=../reportes/verRegistroPDF.php?id=" . $id . " target='_blank'><img border='0' alt='reporte' title='Generar Reporte : " . $id . "' src='../img/pdf.png' width='21' height='20'></a>" . $url . $traza . $mail);
                }
            }
            echo '</table>';
            mysql_close($link);
            if ($contar != 0) {
                echo "<table class='table2'><tr><td class='texto8'><i>RESUMEN DOCUMENTACIÓN:</i></td>
			<td class='texto8'>Total Valorizado:&nbsp;&nbsp;$" . number_format($suma, 0, ',', '.') . " </td>
			<td class='texto8'>Total Recepcionado:&nbsp;&nbsp;$" . number_format(($rec), 0, ',', '.') . "</td>
			<td class='texto8'>Total Anulado:&nbsp;&nbsp;$" . number_format(($dev), 0, ',', '.') . "</td>
			<td class='texto8'>Total Pendiente:&nbsp;&nbsp;$" . number_format(($pend), 0, ',', '.') . " </td>
			<tr><td colspan='5'></td></tr>
			</tr>			
			<tr><td class='texto8'><i>RESUMEN ORDEN DE COMPRA:</i></td>
			<td class='texto8'>Total Solicitado&nbsp;&nbsp;$" . number_format(($suma4), 0, ',', '.') . " </td>
			<td class='texto8'>Total Recepcionado:&nbsp;&nbsp;$" . number_format(($suma5), 0, ',', '.') . "</td>
			<td class='texto8'>Total Items:&nbsp;&nbsp;" . number_format(($suma6), 0, ',', '.') . "</td>
			<td class='texto8'>Total Pendiente&nbsp;&nbsp;$" . number_format(($suma3), 0, ',', '.') . " </td>
			</tr>
			</table>";
            }
            ?>
            <br>
            <center><button class="boton" onclick="window.close()">Salir&nbsp;<img class="img" alt="Salir" title="Salir" src="../img/salir.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button id="capturar" class="boton" onclick="window.open('../reportes/verDocOCPDF.php?id=<?= $ido ?>', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=960,height=640 left=150,top=20')">Generar PDF&nbsp;&nbsp;<img class="img" alt="pdf" title="Exporta los datos actuales a formato PDF" src="../img/pdf1.png"></button></center>        
            <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>