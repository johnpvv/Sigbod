<!DOCTYPE html>
<!--
Sigbod John Vaccarella creacion 07/09/2019
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$fecha = date("Y-m-d");

?>
<html>
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
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
            $(document).ready(function () {
                $("#tipo, #anio").change(function () {//comprobamos si se pulsa una tecla 
                    var consulta, anio;
                    consulta = $("#tipo").val();//obtenemos el texto introducido en el campo de busqueda
                    anio = $("#anio").val();    
                    if(consulta==='0'){
                        $("#resultado").empty();
                    }else{
                    $.ajax({//hace la busqueda
                        type: "POST",
                        url: "analisisStockAjax.php",
                        data: {b: consulta, anio:anio},
                        beforeSend: function () {
                            //imagen de carga
                            $("#resultado").html("<p class='centrar_cuadro'>Cargando, por favor espere...<img src='../img/ajax-loading.gif' width='150' height='100' /></p>");
                        },
                        error: function () {
                            alert("error peticion ajax");
                        },
                        success: function (data) {
                            $("#resultado").empty();
                            $("#resultado").append(data);
                        }
                    });
                    }
                });
            });
        </script>
        <title>SigBod - Análisis de Stock</title> 
    </head>
    <body class="fondo">     
        <table class="table2">
            <tr>
                <td class="texto">Análisis de Quiebres de Stock</td>
            </tr>
            <tr>
                <td class="texto6">Tipo de Análisis:
                    <select name="tipo" class="caja" id="tipo">
                        <option value='0' selected>‌Elija Una Opción...</option>
                        <option value='1'>‌Ordenar por N° de Avisos</option>
                        <option value='2'>‌Ordenar por N° de Quiebres</option>
                    </select>&nbsp;&nbsp;&nbsp;&nbsp;
                    Fecha de Corte:
                    <input type="date" class='caja' name="anio" id="anio" step="1" min="<?= $fechatope ?>" max="<?= $fecha ?>" value="<?= $fecha?>" required />
                </td>                
            </tr>
            <tr>
                <td><center><button class="botonnormal" onclick="window.location = '../principal.php'">Volver&nbsp;&nbsp;<img class='img' alt='volver' title='Volver a pantalla anterior' src='../img/undo.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button onclick="window.location = '../exportar/exportaExcelAnalisis.php'" class="botonnormal">Exportar a Excel&nbsp;&nbsp;<img class='img' alt='exportar' title='Exportar a Excel los datos mostrados en pantalla' src='../img/excel3.png'></button></center></td>
    </tr>
</table>
<div id="resultado"></div>
<a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
</body>
</html>