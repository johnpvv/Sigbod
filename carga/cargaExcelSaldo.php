<!DOCTYPE html>
<!--
Sigbod John Vaccarella modificado: 18/09/2019
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
include("../include/conn.php");
$link = Conectarse(); //Variable de conexion
$sql="select carga_fecha, carga_nombre, carga_cuenta from sigbod.carga_log where carga_tipo like '%Saldo%' order by carga_id desc limit 1";
$data = mysql_query($sql,$link);
$row = mysql_fetch_array($data);
$carga= date("d/m/Y, H:i", strtotime($row["carga_fecha"]))." -- Tipo de Acción: ". $row["carga_nombre"];
$cuenta= $row["carga_cuenta"];
?>
<html>
    <head>
        <title>SigBod - Cargar Saldos Orden de Compra</title>
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link href="../css/tabla.css" rel="stylesheet" type="text/css"/>
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>

        <script type="text/javascript">
            $(document).ready(function (e) {
                $("#cargaSaldo").on('submit', (function (e) {
                    e.preventDefault();
                    var data = new FormData(this); // <-- 'this' is your form element
                    $.ajax({
                        url: 'cargaExcelSaldoAjax.php',
                        data: data,
                        cache: false,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        beforeSend: function () {
                            $.blockUI({
                                message: "<p class='centrar_cuadro_alertas'>Cargando Archivo, por favor espere...<img src='../img/loading.gif' width='200' height='140' /></p>"
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
            $(document).ready(function (e) {
                $("#borrar").on('click', (function (e) {
                    e.preventDefault();
                    var resp = $("#resp").val();
                    var statusConfirm = confirm("¿Realmente desea eliminar esto?");
                    if (statusConfirm === true) {
                        if (document.vaciar.resp.value === "si") {
                            $.ajax({
                                url: '../edicion/vaciarSaldo.php',
                                data: {resp: resp},
                                type: 'POST',
                                beforeSend: function () {
                                    $.blockUI({
                                        message: "<p class='centrar_cuadro_alertas'>Cargando Archivo, por favor espere...<img src='../img/loading.gif' width='200' height='140' /></p>"
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
                        } else {
                            alert("Debe ingresar 'SI', para borrar los datos");
                            document.vaciar.resp.focus();
                            return 0;
                        }
                    }
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
        <script type="text/javascript">
            $(document).ready(function () {//para cambiar nombre del boton borrar y ocultar/mostrar la opcion de borrado de tabla
                $('#borra').hide();
                $('#botonMostrar').on('click', function () {
                    var flag = $('#botonMostrar').val();
                    if (flag == 1) {
                        $('#borra').show();
                        $('#botonMostrar').val(0);
                        $('#texto').text("Ocultar Borrado");
                    } else {
                        $('#borra').hide();
                        $('#botonMostrar').val(1);
                        $('#texto').text("Mostrar Borrado");
                    }
                });
            });
        </script> 
    </head>
    <body class="fondo">
        <form name='vaciar' action='../edicion/cargarBDsaldo.php' method='post'>
            <table class="table2">
                <tr>
                    <td class="texto">Cargar Saldos de Orden de Compra</td>
                </tr>
                <tr id="borra">
                    <td class="texto5a"><b>PARA BORRAR LOS DATOS DE LA TABLA, ESCRIBA: "SI" EN EL RECUADRO AZUL</b>&nbsp;&nbsp;&nbsp;<input type="text" class="caja1" size="2" maxlength="2" name="resp" id='resp'/>&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='borrar' id='borrar' value='Borrar Datos' class="botonnormal"></td>
                </tr>
            </table></form>
        <form id='cargaSaldo' method='post' enctype="multipart/form-data">
            <table class="table2">
                <tr>
                    <td class="texto5a">Seleccionar el archivo a Cargar:&nbsp;&nbsp;&nbsp;&nbsp;<input type='file' name='sel_file' id='sel_file' class="boton" accept=".csv" style="width: 620px;height: 30px;"/></td>
                </tr>
                <tr>
                    <td><span class="texto1">Tipo de Carga:&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="act" value="act" required />Actualización&nbsp;&nbsp;&nbsp;<input type="radio" name="act" value="crg" required />Carga Masiva</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="texto5" title="Registros Modificados: <?=$cuenta?>">(Fecha última carga: <?=$carga?>)</span></td>
                </tr>
                <tr>
                    <td class="texto5a">
                        <button type='submit' name='submit' value="submit" class="botonnormal">Importar Archivo&nbsp;&nbsp;<img class='img' alt='cargar' title='cargar archivo' src='../img/up.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="botonnormal" type="button" onclick="window.location = '../principal.php'">Volver&nbsp;&nbsp;<img class='img' alt='volver' title='Volver a pantalla anterior' src='../img/undo.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' name='descargar' class="botonnormal" onclick="window.location = '../plantillas/generaPlantillaSaldo.php'">Descargar Plantilla&nbsp;&nbsp;<img class='img' alt='descargar' title='Descargar plantilla' src='../img/cloud.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' name='mostrar borrado' id="botonMostrar" value="1" class="botonnormal"><span id="texto">Mostrar Borrado</span>&nbsp;&nbsp;<img class='img' alt='borrar' title='Mostrar Opcion de borrado' src='../img/trash.png'></button></td>
                </tr>
            </table>
        </form>
        <br>
        <div id="resultado"></div>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>