<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
if ($_SESSION['perfil'] > 1) {
    echo '<script>alert("Su Perfil no permite la visualizacion de esta pantalla, Favor contacte al Administrador.");history.back(-1);</script>';
    exit();
}
?>
<html>
    <head>
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/tablesorter.min.js" type="text/javascript"></script>
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>
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
            function agregarSubFila() {
                var table = document.getElementById("tabladoc");
                var cont = table.rows.length;
                table.insertRow(cont).innerHTML = '<tr>' +
                        '<td colspan="4">Nombre Manual:&nbsp;&nbsp;' +
                        '<input type="text" name="nombrearch" id="nombrearch" class="caja_texto" size="75" required>&nbsp;&nbsp;&nbsp;' +
                        'Adjuntar Archivo:&nbsp;&nbsp;' +
                        '<input type="file" class="boton1b" name="arch" accept=".pdf" required>&nbsp;&nbsp;' +
                        '<button type="submit" class="boton1a" id="grabar" title="grabar manual"><b>&nbsp;Grabar Manual&nbsp;</b></button>&nbsp;</td></tr>';
                $("#agregar").prop("disabled", true);
            }
        </script>
        <script type="text/javascript">
            function graba() {
                var data = new FormData(document.getElementById("frm")); // <-- 'this' is your form element
                $.ajax({
                    url: 'cargaManualesAjax.php',
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    beforeSend: function () {
                        $.blockUI({
                            message: "<p class='centrar_cuadro_alertas'>Grabando, por favor espere...<img src='../img/loading.gif' width='200' height='140' /></p>"
                        });
                    },
                    error: function () {
                        alert("error peticion ajax");
                    },
                    success: function (data) {
                        $.unblockUI();
                        $("#resultado").empty();
                        $("#resultado").append(data);
                        location.href = 'cargaManuales.php';
                    }
                });
            }
        </script>
        <script type="text/javascript">
            function actualizar(x) {
                var nom = $("#nom" + x).val();
                var est = $("#est" + x).val();
                $.ajax({
                    url: 'actualizaManualesAjax.php',
                    type: 'POST',
                    data: {nom: nom, est: est, id: x},
                    beforeSend: function () {
                        $.blockUI({
                            message: "<p class='centrar_cuadro_alertas'>Grabando, por favor espere...<img src='../img/loading.gif' width='200' height='140' /></p>"
                        });
                    },
                    error: function () {
                        alert("error peticion ajax");
                    },
                    success: function (data) {
                        $.unblockUI();
                        $("#resultado").empty();
                        $("#resultado").append(data);
                        window.setTimeout(function () {
                            location.href = 'cargaManuales.php';
                        }, 500);
                    }
                });
            }
        </script>
        <title>SigBod - Manuales</title>  
    <html lang="es">
    </head>
    <body class="fondo">
        <form id="frm" name="frm"  method="post" onsubmit="graba()" enctype="multipart/form-data" action="#">
            <table class="table2a">
                <tr>
                    <td colspan="2" class="texto">Editar y Agregar Manuales y Textos</td>
                </tr>
                <tr>
                    <td>
                        <?php
                        include_once("../include/conn.php");
                        $link = Conectarse();
                        $comilladob = '"';
                        $comillasim = "'";
                        $id = $_GET["id"];
                        $rs = mysql_query("SELECT * FROM manuales", $link);
                        $contar = mysql_num_rows($rs);
                        if ($contar === 0) {
                            echo "No se han encontrado Documentos.";
                        } else {
                            echo '<table id="tabladoc" class="table2d">';
                            echo '<thead><tr><th style="width:50px;">N° Manual.</th><th>Nombre Manual</th><th style="width:50px;">Estado Manual</th><th style="width:50px; word-wrap: break-word;" data-sorter="false" data-filter="false">Acciones:</th></tr></thead>';
                            while ($row = mysql_fetch_array($rs)) {
                                $idbd = $row["manual_id"];
                                $link1 = $row["manual_url"];
                                if ($id === $idbd) {
                                    printf("<tr><td><i>%s</i></td><td align='left'><b>%s</b></td><td>%s</td><td>%s</td></tr>", $idbd, "<input type='text' class='caja' id='nom" . $idbd . "' value='" . $row["manual_nombre"] . "' size='100' required autofocus>", "<input type='text' id='est" . $idbd . "' class='caja' value='" . $row["manual_estado"] . "' size='3' required>", "<img class='boton1a' style='width:18px;height:18px;margin-top:5px' id='" . $idbd . "' alt='actualizar' title='actualizar' src='../img/check.png' onclick='actualizar(this.id);'>");
                                } else {
                                    printf("<tr><td><i>%s</i></td><td align='left'><b>%s</b></td><td>%s</td><td>%s</td></tr>", $idbd, $row["manual_nombre"], $row["manual_estado"], "<a href=" . $link1 . " target='_blank' title='Ver Manual' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=720, width=960 left=10 top=10" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='ver documento' title='ver Documento' src='../img/pdf.png' width='25' height='24'></a>&nbsp;|&nbsp;<a href=cargaManuales.php?id=" . $idbd . "><img border='0' alt='editar manual' title='editar Manual: " . $idbd . "' src='../img/edit1.png' width='20' height='20'></a>");
                                }
                            }
                        }
                        echo '</table>';
                        echo '<script type="text/javascript">$(document).ready(function () {$("#tabladoc").tablesorter();});</script>';
                        mysql_close($link);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <button type="button" class="boton1a" id="agregar" onclick="agregarSubFila();" title="Agregar archivo"><b>&nbsp;+&nbsp;</b></button>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><button class="boton" type="button" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;&nbsp;<img class='img' alt='volver' title='Volver a pantalla anterior' src='../img/casa.png'></button></td>
                </tr>
            </table>
        </form>
        <div id="resultado"></div>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>