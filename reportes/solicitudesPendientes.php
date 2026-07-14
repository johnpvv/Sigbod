<!DOCTYPE html>
<!--
Sigbod John Vaccarella fecha de creacion 10/09/2019 hp grande
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$_SESSION['query'] = "";
$id = $_GET["op"];
if ($id == 1) {
    $vlv = " value='6' selected";
    $vlv1 = " value='0'";
    $dir = "buscar.click()";
} else {
    $vlv = " value='6'";
    $vlv1 = " value='0' selected";
    $dir = "";
}
?>
<html>
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
            $(document).ready(function () {
                $("#buscar").click(function () {//comprobamos si se pulsa una tecla
                    var consulta, anio, chkall;
                    consulta = $("#tipo").val(); //obtenemos el texto introducido en el campo de busqueda
                    anio = $("#fecha").val();
                    chkall = $("#checkall").prop("checked");
                    if (consulta === '0') {
                        $("#resultado").empty();
                    } else {
                        $.ajax({//hace la busqueda
                            type: "POST",
                            url: "solicitudesPendientesAjax.php",
                            data: {b: consulta, anio: anio, chkall: chkall},
                            beforeSend: function () {
                                $.blockUI({
                                    message: "<p class='centrar_cuadro_alertas'>Buscando, por favor espere...<img src='../img/loading.gif' width='200' height='150' /></p>"
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
                    }
                });
            });
        </script>

        <title>SigBod - Monitoreo de Solicitudes</title>
    </head>
    <body class="fondo" onload="<?= $dir ?>">
        <?php
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../index.php');
            exit();
        }
        $fechaact = date("Y-m-d");
        $fechatope = date("Y-m-d", strtotime($fechaact . "- 10 days"));//para sumar diez dias
        ?>
        <table class="table2">
            <tr>
                <td class="texto">Solicitudes Pendientes Sin Stock</td>
            </tr>
            <tr>
                <td class="texto6">Días de Búsqueda:
                    <select name="tipo" class="caja" id="tipo">
                        <option <?= $vlv1 ?>>‌Elija Una Opción...</option>
                        <option value='1'>‌1 día</option>
                        <option value='2'>‌2 días</option>
                        <option value='3'>‌3 días</option>
                        <option value='4'>‌4 días</option>
                        <option value='‌5'>5 días</option>
                        <option <?= $vlv ?>>‌6 días</option>
                        <option value='7'>‌7 días</option>
                        <option value='8'>‌8 días</option>
                        <option value='9'>‌9 días</option>
                        <option value='10'>‌10 días</option>
                    </select>&nbsp;&nbsp;&nbsp;&nbsp;
                    Fecha de Corte:
                    <input type="date" class='caja' name="fecha" id="fecha" step="1" min="<?= $fechatope ?>" max="<?= $fechaact ?>" value="<?= $fechaact ?>" required />&nbsp;&nbsp;&nbsp;&nbsp;
                    <button id="buscar" class="boton1">Buscar&nbsp;<img class="imgbtnpeq" alt="buscador" title="Buscar" src="../img/lupa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
            <tr>
                <td class = "texto6"><button class = "botonnormal" onclick = "window.location = '../principal.php'">Volver al Menú Principal&nbsp;<img class = "img" alt = "principal" title = "volver al menu principal" src = "../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button onclick = "window.location = '../exportar/exportaSolicitudPend.php'" class = "botonnormal">Exportar a Excel&nbsp;&nbsp;<img class = 'img' alt = 'exportar' title = 'Exportar a Excel los datos mostrados en pantalla' src = '../img/excel3.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button id="capturar" class="botonnormal" onclick="window.open('../reportes/solicitudPendPDF.php', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=860,height=620 left=200,top=30')">Generar Informe en PDF&nbsp;<img class="img" alt="pdf" title="Exporta los datos actuales a formato PDF" src="../img/pdf1.png"></button></td>
            </tr>
        </table>       
		<div id = "resultado"></div>
        <a href = "javascript:void(0);" id = "scroll" title = "Ir Arriba" style = "display: none;">Arriba<span></span></a>        
    </body>
</html>