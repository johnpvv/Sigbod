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
?>
<html>
    <head>
        <title>SigBod - Cargar Proveedores</title>
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link href="../css/tabla.css" rel="stylesheet" type="text/css"/>
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function (e) {
                $("#cargaProv").on('submit', (function (e) {
                    e.preventDefault();
                    var data = new FormData(this); // <-- 'this' is your form element
                    $.ajax({
                        url: 'cargaExcelProvAjax.php',
                        data: data,
                        cache: false,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        beforeSend: function () {
                            $.blockUI({
                                message: "<p class='centrar_cuadro_alertas'>Cargando Archivo, por favor espere...<img src='../img/loading.gif' width='200' height='150' /></p>"
                            });
                        },
                        error: function () {
                            alert("error peticion ajax");
                        },
                        success: function (data) {
                            $("#resultado").empty();
                            $("#resultado").append(data);
                            $.unblockUI();
                        }
                    });
                }));
            });
        </script>
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
    </head>
    <body class="fondo">
        <form id="cargaProv" method='post' enctype="multipart/form-data">      
            <table class="table2">
                <tr>
                    <td class="texto">Carga Masiva de Proveedores</td>
                </tr> 
                <tr>
                    <td class="texto5a"><b>Seleccionar el archivo a Cargar:</b>&nbsp;&nbsp;                
                        <input type='file' name='sel_file' class="boton" accept=".csv" style="width: 620px;height: 30px;"/>
                    </td>
                </tr>
                <tr>
                    <td><span class="texto1">Tipo de Carga:&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="act" value="act" required />Actualización&nbsp;&nbsp;&nbsp;<input type="radio" name="act" value="crg" required />Carga Masiva</span></td>
                </tr>
                <tr>
                    <td class="texto5a"><button type='submit' name='submit' value="submit" class="botonnormal">Importar Archivo&nbsp;&nbsp;<img class='img' alt='cargar' title='cargar archivo' src='../img/up.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="botonnormal" type="button" onclick="window.location = '../principal.php'">Volver&nbsp;&nbsp;<img class='img' alt='volver' title='Volver a pantalla anterior' src='../img/undo.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' name='descargar' class="botonnormal" onclick="window.location = '../plantillas/generaPlantillaProv.php'">Descargar Plantilla&nbsp;&nbsp;<img class='img' alt='descargar' title='Descargar plantilla' src='../img/cloud.png'></button></td>
                </tr>
            </table>
        </form>   
        <br>
        <div id="resultado"></div>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>