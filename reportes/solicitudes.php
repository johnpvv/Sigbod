<!DOCTYPE html>
<!--
Sigbod John Vaccarella fecha de creacion 10/03/2022 hp grande
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$_SESSION['query_solicitudes'] = "";
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
                    var anio, anio1, chk, consulta;
                    anio = $("#fecha").val();
                    anio1 = $("#fecha1").val();
                    chk = $("#chk").prop("checked");
                    consulta = $("#busqueda").val().replace(/[`~!@#$%^&*()_|+\=?;:'",.<>\{\}\[\]\\]/gi, '').trim();//obtenemos el texto introducido en el campo de busqueda y elimina caracteres no correctos

                    $.ajax({//hace la busqueda
                        type: "POST",
                        url: "solicitudesAjax.php",
                        data: {anio: anio, anio1: anio1, chk: chk, saldo: consulta},
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
                });
            });
        </script>
        <title>SigBod - Solicitudes Realizadas</title>
    </head>
    <body class="fondo">
        <?php
        $fechaact = date("Y-m-d");
        $fechamin = date("Y-m-01");
        ?>
        <table class="table2">
            <tr>
                <td class="texto">Detalle de Solicitudes Realizadas</td>
            </tr>
            <tr>
                <td class="texto6">
                    <span class="texto5">
                        Ingrese Texto a Buscar:&nbsp;<input id="busqueda" name="busqueda" type="search" placeholder="escriba aqui..." autofocus size="25" maxlength="30" class='caja' onkeypress="if (event.keyCode == 13)
                                    buscar.click();"/>&nbsp;&nbsp;
                        Fecha Solicitudes:&nbsp;
                        <span class="caja_pading3">Desde: 
                            <input type="date" class='caja_texto' name="fecha" id="fecha" step="1" min="" max="<?= $fechaact ?>" value="<?= $fechamin ?>" required />
                            &nbsp;&nbsp;Hasta:&nbsp;
                            <input type="date" class='caja_texto' name="fecha" id="fecha1" step="1" min="" max="<?= $fechaact ?>" value="<?= $fechaact ?>" required />
                        </span>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <button id="buscar" class="boton1">Buscar&nbsp;<img class="imgbtnpeq" alt="buscador" title="Buscar" src="../img/lupa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" name="chk" id="chk"/> Solo Productos Destacados
                    </span>
                </td>
            </tr>
            <tr>
                <td class = "texto6"><button class = "botonnormal" onclick = "window.location = '../principal.php'">Volver al Menú Principal&nbsp;<img class = "img" alt = "principal" title = "volver al menu principal" src = "../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button onclick = "window.location = '../exportar/exportaSolicitud.php'" class = "botonnormal">Exportar a Excel&nbsp;&nbsp;<img class = 'img' alt = 'exportar' title = 'Exportar a Excel los datos mostrados en pantalla' src = '../img/excel3.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button id="capturar" class="botonnormal" onclick="window.open('../reportes/solicitudPDF.php', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=860,height=620 left=200,top=30')">Generar Informe en PDF&nbsp;<img class="img" alt="pdf" title="Exporta los datos actuales a formato PDF" src="../img/pdf1.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button type="button" onclick="window.location = 'solicitudes.php'" class="botonnormal">Limpiar&nbsp;&nbsp;<img class='img' alt='limpiar' title='Limpiar Pantalla' src='../img/clean.png'></button>
                </td>
            </tr>
        </table>       
        <div id = "resultado"></div>
        <a href = "javascript:void(0);" id = "scroll" title = "Ir Arriba" style = "display: none;">Arriba<span></span></a>        
    </body>
</html>