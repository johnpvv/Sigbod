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
$_SESSION['query'] = "";
$fechaact = date("Y-m-d");
?>
<html>
    <head>
        <script type="text/javascript" src="js/validarut.js"></script>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>
        <script src="../js/tablesorter.min.js" type="text/javascript"></script>
        <script src="../js/jquery.tablesorter.pager.js"></script> 
        <title>SigBod - Proveedores</title> 
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
                    consulta = $("#busqueda").val().replace(/[`~!@#$%^&*()_|+\=?;:'",.<>\{\}\[\]\\\/]/gi, '').trim();//obtenemos el texto introducido en el campo de busqueda y elimina caracteres no correctos
                    var check = $("#check").prop("checked");
                    var chkall = $("#checkall").prop("checked");
                    var anio = $("#fecha").val();
                    $.ajax({//hace la busqueda
                        type: "POST",
                        url: "buscarProveedorAjax.php",
                        data: {saldo: consulta, chk: check, chkall: chkall, anio: anio},
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
    <body class="fondo" onload="window.setTimeout(function () {
                buscar.click()
            }, 300);"><!-- pausa de 300 ms para ejecutar funcion buscar-->    
        <table class="table2a">
            <tr>
                <td class="texto"><img class="imgico" alt='Proveedor' title='Proveedor' src='../img/suply.png'>&nbsp;&nbsp;&nbsp;Consultar Proveedores</td>
            </tr>
            <tr>
                <td class="texto6">
            <center>
                Ingrese Nombre o RUT a Buscar:
                <span class="texto6">
                    <input id="busqueda" name="txtcod" type="search" placeholder="escriba aqui..." autofocus size="35" maxlength="20" class="caja" onkeypress="if (event.keyCode == 13)
                                buscar.click();"/>&nbsp;&nbsp;&nbsp;&nbsp;                    
                    <button id="buscar" class="boton1">Buscar&nbsp;<img class="imgbtnpeq" alt="buscador" title="Buscar" src="../img/lupa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="check" id="check" /> Sólo Vigentes&nbsp;&nbsp;&nbsp;<input type="checkbox" name="checkall" id="checkall" /> Mostrar Todos
                </span>
                <span class='caja_pading2'>Fecha Mod.:
                    <input type="date" class='caja_texto' name="fecha" id="fecha" step="1" min="" max="<?= $fechaact ?>" value=""/>
                </span>&nbsp;&nbsp;
            </center>
        </td>
    </tr>        
    <tr>
        <td>
    <center>
        <button class="botonnormal" onclick="window.location = '../principal.php'">Volver&nbsp;&nbsp;<img class='img' alt='volver' title='Volver a pantalla anterior' src='../img/undo.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button onclick="window.location = '../exportar/exportaExcelProv.php'" class="botonnormal">Exportar a Excel&nbsp;&nbsp;<img class='img' alt='exportar' title='Exportar a Excel los datos mostrados en pantalla' src='../img/excel3.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="botonnormal" onclick="window.location = '../edicion/modificarProveedor.php'">Crear Proveedor&nbsp;<img class="img" alt="nuevo" title="Crear Proveedor" src="../img/add.png"></button>
    </center>
</td>
</tr>
</table>
<div id="resultado"></div>
<a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
</body>
</html>