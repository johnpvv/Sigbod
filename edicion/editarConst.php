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
                        '<td></td>' +
                        '<td><input type="text" class="caja" name="cat" id="cat" class="caja_texto" size="15" required autofocus></td>' +
                        '<td><input type="text" class="caja" name="nom" id="nom" class="caja_texto" size="55" required></td>' +
                        '<td><input type="text" class="caja" name="val" id="val" class="caja_texto" size="30" required></td>' +
                        '<td><input type="text" class="caja" name="est" id="est" class="caja_texto" size="10" required></td>' +
                        '<button type="submit" class="boton1a" id="grabar" title="grabar"><b>&nbsp;Grabar Datos&nbsp;</b></button>&nbsp;</td></tr>';
                $("#agregar").prop("disabled", true);
            }
        </script>
        <script type="text/javascript">
            function graba() {
                var data = new FormData(document.getElementById("frm")); // <-- 'this' is your form element
                $.ajax({
                    url: 'editarConstAjax.php',
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
                        location.href = 'editarConst.php';
                    }
                });
            }
        </script>
        <script type="text/javascript">
            function actualizar(x) {
                var nom = $("#nom" + x).val();
                var cat = $("#cat" + x).val();
                var val = $("#val" + x).val();
                var est = $("#est" + x).val();
                $.ajax({
                    url: 'editarConstAct.php',
                    type: 'POST',
                    data: {nom: nom, est: est, id: x, val: val, cat: cat},
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
                            location.href = 'editarConst.php';
                        }, 500);
                    }
                });
            }
        </script>
        <title>SigBod - Constantes</title>  
    <html lang="es">
    </head>
    <body class="fondo">
        <form id="frm" name="frm"  method="post" onsubmit="graba()" enctype="multipart/form-data" action="#">
            <table class="table2a">
                <tr>
                    <td colspan="2" class="texto">Editar y Agregar Constantes de Configuración</td>
                </tr>
                <tr>
                    <td>
                        <?php
                        include_once("../include/conn.php");
                        $link = Conectarse();
                        $comilladob = '"';
                        $comillasim = "'";
                        $id = $_GET["id"];
                        $rs = mysql_query("SELECT * FROM const_config", $link);
                        $contar = mysql_num_rows($rs);
                        echo '<table id="tabladoc" class="table2d">';
                        echo '<thead><tr><th style="width:15px;">N°</th><th style="width:150px;">Nombre Categoría</th><th>Nombre Constante</th><th>Valor Constante</th><th style="width:50px;">Estado Constante</th><th style="width:100px; word-wrap: break-word;" data-sorter="false" data-filter="false">Acciones:</th></tr></thead>';
                        if ($contar === 0) {
                            echo "<tr><td colspan='6'>No se han encontrado Datos.</td></tr>";
                        } else {
                            while ($row = mysql_fetch_array($rs)) {
                                $idbd = $row["const_id"];
                                if ($id === $idbd) {
                                    printf("<tr><td><i>%s</i></td><td align='left'><b>%s</b></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", $idbd, "<input type='text' class='caja' id='cat" . $idbd . "' name='cat" . $idbd . "' value='" . $row["const_cat"] . "' size='15' required autofocus>", "<input type='text' id='nom" . $idbd . "' name='nom" . $idbd . "' class='caja' value='" . $row["const_nom"] . "' size='55' required>", "<input type='text' id='val" . $idbd . "' name='val" . $idbd . "' class='caja' value='" . $row["const_val"] . "' size='30' required>", "<input type='text' id='est" . $idbd . "' class='caja' value='" . $row["const_est"] . "' size='10' required>", "<img class='boton1a' style='width:18px;height:18px;margin-top:5px' id='" . $idbd . "' alt='actualizar' title='actualizar' src='../img/check.png' onclick='actualizar(this.id);'>");
                                } else {
                                    printf("<tr><td><i>%s</i></td><td align='left'><b>%s</b></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", $idbd, $row["const_cat"], $row["const_nom"], $row["const_val"], $row["const_est"], "<a href=editarConst.php?id=" . $idbd . "><img border='0' alt='editar constante' title='editar constante: " . $idbd . "' src='../img/edit1.png' width='20' height='20'></a>");
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
                        <button type="button" class="boton1a" id="agregar" onclick="agregarSubFila();" title="Agregar"><b>&nbsp;+&nbsp;</b></button>
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