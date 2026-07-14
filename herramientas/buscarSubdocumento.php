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
$id = $_GET['id'];
$comilladob = '"';
$comillasim = "'";
$sql1 = "SELECT * FROM proveedores, documento, subdocumento WHERE doc_rutpv=prov_rut AND doc_id=subdoc_id AND doc_id='$id' ORDER BY subdoc_numdoc DESC";
$data = mysql_query($sql1);
$data1 = mysql_query($sql1);
$row1 = mysql_fetch_array($data);
$sql2 = "SELECT * FROM usuarios WHERE usuario_rut='" . $row1["doc_usuario"] . "'";
$data2 = mysql_query($sql2);
$row2 = mysql_fetch_array($data2);
$sql3 = "SELECT * FROM usuarios WHERE usuario_rut='" . $row1["doc_umodif"] . "'";
$data3 = mysql_query($sql3);
$row3 = mysql_fetch_array($data3);
$link1 = $row1["doc_url"];
if ($link1 <> "") {
    $url = "<a href=$link1 target='_blank' title='Ver DTE' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=560, width=840 left=250 top=30" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='doc' title='Ver DTE' src='../img/ver.png' class='imgcopy'></a>";
    $traza = "<a href=https://escritorio.acepta.com/traza/?url=" . $link1 . " target='_blank' title='Ver Traza' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=640, width=840 left=250 top=10" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='doc' title='Ver Traza en Acepta' src='../img/track3.png' class='imgcopy'></a>";
} else {
    $url = "";
    $traza = "";
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
                $('#ticket').hide();
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
            function actualizar(x) {
                var est= $("#est").val();
                $.ajax({//hace la busqueda
                    type: "POST",
                    url: "buscarSubdocumentoAjax.php",
                    data: {est: est,id:x},
                    beforeSend: function () {//imagen de carga                            
                        $("#divc").html("<img src='../img/loader2.gif' width='25' height='25' />");//imagen de carga
                    },
                    error: function () {
                        alert("error peticion ajax");
                    },
                    success: function (data) {
                        $("#divc").empty();
                        $("#divc").append(data);
                    }
                });
            }
        </script> 
        <script type="text/javascript">
            $(function () {
                $("#subdoc").tablesorter();
            });
        </script>		
        <title>SigBod - Visualizar Subdocumentos</title>
    </head>
    <body class="fondo">        
        <table class="table2b">
            <tr>
                <td class="texto" colspan="2">Detalle de Subdocumentos</td>
            </tr>
            <tr><td class="texto_ancho">N° Registro Principal:</td><td class="texto9a"><b><?= $row1["doc_id"] ?></b></td></tr>
            <tr><td class="texto8">N° Documento Principal:</td><td class="texto9"><b class="texto12"><?= $row1["doc_ndoc"] ?></b>&nbsp;&nbsp;&nbsp;<?= $url; ?></b>&nbsp;&nbsp;&nbsp;&nbsp;<b><?= $traza; ?></b></td></tr>
            <tr><td class="texto8">Tipo Documento Principal:</td><td class="texto9a"><?= $row1["doc_tipodoc"] ?></td></tr>
            <tr><td class="texto8">Proveedor:</td><td class="texto9a"><b><?= $row1["prov_rut"] . "-" . $row1["prov_dv"] . "</b>&nbsp;&nbsp;" . $row1["prov_nombre"] ?></td></tr>
            <tr><td class="texto8">Fecha Documento Principal:</td><td class="texto9a"><?php
                    if ($row1["doc_fechadoc"] != "0000-00-00") {
                        echo date("d/m/Y", strtotime($row1["doc_fechadoc"]));
                    } else {
                        echo "";
                    }
                    ?></td></tr>
            <tr><td class="texto8">Orden de Compra:</td><td class="texto9a"><b><?= $row1["doc_noc"] ?></b></td></tr>
            <tr><td class="texto8">Origen:</td><td class="texto9a"><?php
                    if ($row1["doc_origen"] != "") {
                        echo $row1["doc_origen"];
                    } else {
                        echo "";
                    }
                    ?><?php
                    if ($row1["doc_norigen"] != 0) {
                        echo " N°" . $row1["doc_norigen"];
                    } else {
                        echo "";
                    }
                    ?><?php
                    if ($row1["doc_fechaorigen"] != "0000-00-00") {
                        echo ",&nbsp;&nbsp;Fecha:&nbsp;" . date("d/m/Y", strtotime($row1["doc_fechaorigen"]));
                    } else {
                        echo "";
                    }
                    ?></b></td></tr>
            <tr><td class="texto8">Monto:</td><td class="texto9a"><b>$ <?= number_format($row1["doc_montototal"], 0, ',', '.') ?></b></td></tr>
            <tr><td class="texto8">Creado Por:</td><td class="texto9a"><?= $row2["usuario_nombre"] ?>&nbsp;<?= $row2["usuario_apellidos"] ?>,&nbsp;&nbsp;<?= date("d/m/Y, H:i:s", strtotime($row1["doc_fechacarga"])) ?></td></tr>
            <tr><td class="texto8">Modificado Por:</td><td class="texto9a"><?= $row3["usuario_nombre"] ?>&nbsp;<?= $row3["usuario_apellidos"] ?><?php
                    if ($row1["doc_fechamod"] != "0000-00-00 00:00:00") {
                        echo ",&nbsp;&nbsp;" . date("d/m/Y, H:i:s", strtotime($row1["doc_fechamod"]));
                    } else {
                        echo "";
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td class="texto8">Estado Actual:</td>
                <td class="texto9a">
                    <select name="est" id="est" class="caja_color" onchange="actualizar(<?=$row1["doc_id"]?>)">
                        <?php
                        $option = $row1["doc_estado"];
                        $sql4 = "SELECT distinct(doc_estado) FROM documento ORDER BY doc_estado";
                        $res4 = MySQL_query($sql4, $link)or die(mysql_error());
                        while ($row4 = mysql_fetch_array($res4)) {
                            if ($row4["doc_estado"] == $option) {
                                $sel = "selected";
                            } else {
                                $sel = "";
                            }
                            if ($row4["doc_estado"] == "") {
                                $est = "Pendiente";
                            } else {
                                $est = $row4["doc_estado"];
                            }
                            echo "<option value='" . $row4["doc_estado"] . "' " . $sel . ">‌" . $est . "</option>";
                        }
                        ?>
                    </select>
                    &nbsp;<span id="divc" class="imgcopy"></span>
                </td>
            </tr>
            <tr>
                <td class="texto8">Observaciones:</td><td class="texto9a"><b><?= $row1["doc_obs"] ?></b></td>
            </tr>
        </table>
        <table class="table2b" id="subdoc">
            <thead>
                <tr>
                    <th>N° Subdocumento</th>
                    <th>Tipo Subdocumento</th>
                    <th width="90">RUT</th>
                    <th width="120">Nombre Proveedor</th>
                    <th>Fecha Subdocumento</th>
                    <th>Monto Total Subdocumento</th>
                    <th data-sorter="false" data-filter="false">Adjunto</th>
                </tr>
            </thead>
            <?php
            $cadena = "JgBlAG0AXwBmAF8AZABlAHMAZABlAD0AMQAxAC8AMAA1AC8AMgAwADIAMQAmAGUAbQBfAGYAXwBoAGEAcwB0AGEAPQAxADEALwAwADUALwAyADAAMgAxACYAZQBtAF8AYQBjAHQAPQAmAGUAbQBfAG4AdQBtAD0AJgBlAG0AXwBjAG8AZAA9ACYAYgBjAGgAawBFAGwAaQA9ACYAZABkAHcAVABpAHAAbwBBAGMAdABhAD0AMAAmAGQAZABsAEIAbwBkAGUAZwBhAD0AMQA1ACYAVAB4AHQATABhAGIAPQA=";
            while ($row = mysql_fetch_array($data1)) {
                if ($row["subdoc_tipo"] == "Recep. de Bodega") {

                    if ($row["subdoc_fecha"] <= "2020-07-28") {
                        $link1 = '<a href=http://10.9.83.120/recepcion/informes/verMinRecepcionExp.aspx?id_rec=' . $row["subdoc_numdoc"] . ' target="_blank">' . $row["subdoc_numdoc"] . '<a>';
                    } else {
                        $link1 = '<a href=http://10.6.24.11/SOFYA/contenido/Bodega/GestionActaBodega.aspx?key=' . $row["subdoc_numdoc"] . '&cadena=' . $cadena . ' target="_blank">' . $row["subdoc_numdoc"] . '<a>';
                    }
                } else {
                    $link1 = $row["subdoc_numdoc"];
                }
                if ($row["doc_fechadoc"] != "0000-00-00") {
                    $fechasubdoc = date("d/m/Y", strtotime($row["subdoc_fecha"]));
                } else {
                    $fechasubdoc = "";
                }
                if ($row["subdoc_adjunto"] == "") {
                    $adj = "<img style='padding: 6px;' class='imgcopy' alt='vacio' title='No Hay Archivos Adjuntos' src='../img/error.png'>";
                } else {
                    $adj = "<a href=" . $row["subdoc_adjunto"] . " target='_blank' title='Ver Imagen' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=720, width=840 left=300 top=10" . $comillasim . "); return false;" . $comilladob . "><img style='padding: 6px;' class='imgcopy' alt='archivo' title='Ver archivo adjunto' src='../img/lupa.png'></a>";
                }
                printf("<tr><td><b>%s</b></td><td><b>%s</b></td><td>%s</td><td>%s</td><td>%s</td><td><b>$</b>%s</td><td>%s</td></tr>", $link1, $row["subdoc_tipo"], number_format($row["prov_rut"], 0, '.', '.') . "-" . $row["prov_dv"], $row["prov_nombre"], $fechasubdoc, number_format($row["subdoc_monto"], 0, ',', '.'), $adj);
            }
            mysql_close($link);
            ?>
        </table>
        <br>
    <center><button class="boton" onclick="window.close()">Salir&nbsp;<img class="img" alt="Salir" title="Salir" src="../img/salir.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button id="capturar" class="boton" onclick="window.open('../reportes/verRegistroPDF.php?id=<?= $id ?>', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=960,height=640 left=150,top=20')">Generar PDF&nbsp;&nbsp;<img class="img" alt="pdf" title="Exporta los datos actuales a formato PDF" src="../img/pdf1.png"></button></center>        
    <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
</body>
</html>