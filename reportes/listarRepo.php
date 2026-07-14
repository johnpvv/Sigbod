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
$dia = date("j");
setlocale(LC_ALL, "es_ES@euro", "es_ES", "esp");
$fecha = strftime("%d de %B de %Y", strtotime(date("d-m-Y")));
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
                $("#dia").change(function () {//comprobamos si se pulsa una tecla 
                    var consulta;
                    consulta = $("#dia").val();//obtenemos el texto introducido en el campo de busqueda
                    if (consulta === '0') {
                        $("#resultado").empty();
                    } else {
                        $.ajax({//hace la busqueda
                            type: "POST",
                            url: "listarRepoAjax.php",
                            data: {b: consulta},
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
        <script type="text/javascript">
            $(document).ready(function () {
                var consulta;
                consulta = $("#diatxt").val();//obtenemos el texto introducido en el campo de busqueda
                $.ajax({//hace la busqueda
                    type: "POST",
                    url: "listarRepoAjax.php",
                    data: {b: consulta},
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
            });
        </script>
        <title>SigBod - Listado de Productos a Reponer</title> 
    </head>
    <body class="fondo">     
        <table class="table2">
            <tr>
                <td class="texto"><img class="imgico" alt='reponer' title='reponer' src='../img/mano.png'>&nbsp;&nbsp;&nbsp;Listado de Productos a Reponer</td>
            </tr>
            <tr>
                <td class="texto6">Día del Mes:
                    <select name="dia" class="caja" id="dia">
                        <?php
                        $w = 0;
                        while ($w <= 31) {
                            if ($w == 0) {
                                echo "<option value='" . $w . "' selected>‌Elija Dia...</option>";
                            } else {
                                echo"<option value=" . $w . ">" . $w . "</option>";
                            }
                            $w++;
                        }
                        ?>    
                    </select>&nbsp;&nbsp;&nbsp;&nbsp;Fecha Actual: &nbsp;&nbsp;<?= $fecha ?>
                    <input type="hidden" value="<?= $dia ?>" id="diatxt">
                </td>                
            </tr>
            <tr>
                <td><center><button class="botonnormal" onclick="window.location = '../principal.php'">Volver&nbsp;&nbsp;<img class='img' alt='volver' title='Volver a pantalla anterior' src='../img/undo.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button onclick="window.location = '../exportar/exportaExcelAnalisis.php'" class="botonnormal">Exportar a Excel(pendiente)&nbsp;&nbsp;<img class='img' alt='exportar' title='Exportar a Excel los datos mostrados en pantalla' src='../img/excel3.png'></button></center></td>
    </tr>
</table>
<div id="resultado"></div>
<a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
</body>
</html>