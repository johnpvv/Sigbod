<!DOCTYPE html>
<!--
Sigbod John Vaccarella actualizado 05/10/2019
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$_SESSION['query'] = "";
?>
<html>
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>
        <title>SigBod - Usuarios</title> 
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
        <script>
            $(document).ready(function () {
                var consulta;
                $("#busqueda").focus();//hacemos focus al campo de busqueda                
                $("#buscar").click(function (e) {//comprobamos si se pulsa una tecla 
                    consulta = $("#busqueda").val().replace(/[`~!@#$%^&*()_|+\=?;:'",.<>\{\}\[\]\\\/]/gi, '');//obtenemos el texto introducido en el campo de busqueda y elimina caracteres no correctos
                    var chkall = $("#checkall").prop("checked");
                    $.ajax({//hace la busqueda
                        type: "POST",
                        url: "buscarUsuariosAjax.php",
                        data: {b: consulta, chkall: chkall},
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
    </head>
    <body class="fondo">
        <table class="table2a">
            <tr>
                <td class="texto"><img class="imgico" alt='Usuario' title='Usuario' src='../img/user.png'>&nbsp;&nbsp;&nbsp;LISTADO DE USUARIOS</td>
            </tr>
            <tr>
                <td class="texto1"><center>Ingrese Nombre o RUT a Buscar:&nbsp;<span class="texto5a"><input id="busqueda" name="txtnom" type="search" placeholder="escriba aqui..." autofocus size="30" class='caja' onkeypress="if (event.keyCode == 13)
                    buscar.click();"/>&nbsp;&nbsp;&nbsp;&nbsp;<button id="buscar" class="boton1">Buscar&nbsp;<img class="imgbtnpeq" alt="buscador" title="Buscar" src="../img/lupa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" name="checkall" id="checkall" /> Mostrar Todos&nbsp;&nbsp;&nbsp;
                </span> </center>
                </td>
            </tr>        
            <tr>
                <td><center><button class="botonnormal" onclick="window.location = '../principal.php'">Volver&nbsp;&nbsp;<img class='img' alt='volver' title='Volver a pantalla anterior' src='../img/undo.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;
                <button onclick="window.location = '../exportar/exportaExcelUser.php'" class="botonnormal">Exportar a Excel&nbsp;&nbsp;<img class='img' alt='exportar' title='Exportar a Excel los datos mostrados en pantalla' src='../img/excel3.png'></button></center></td>
            </tr>
        </table>
        <div id="resultado"></div>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>