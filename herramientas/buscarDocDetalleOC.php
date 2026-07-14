<!DOCTYPE html>
<!-- fecha creacion 04/07/2020 hp
Sigbod John Vaccarella
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
    $chk = "checked";
    $dir = "buscar.click()";
} else {
    $chk = "";
    $dir = "";
}
$fechaact = date("Y-m-d");
?>
<!DOCTYPE html>
<html lang="es">
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
        <script>
            $(document).ready(function () {
                var consulta;
                $("#busqueda").focus();//hacemos focus al campo de busqueda               
                $("#buscar").click(function (e) {//comprobamos si se pulsa una tecla                    
                    consulta = $("#busqueda").val().replace(/[`~!@#$%^&*()_|+\=?;:'",.<>\{\}\[\]\\]/gi, '').trim();//obtenemos el texto introducido en el campo de busqueda y elimina caracteres no correctos
                    var chkall = $("#checkall").prop("checked");
                    var anio = $("#fecha").val();
                    var anio2 = $("#fecha2").val();
                    var fmod = $("#fmod").val();
                    largo = $("#busqueda").val().length;
                    if (largo === 0 || largo >= 1) {
                        $.ajax({//hace la busqueda
                            type: "POST",
                            url: "buscarDocDetalleOCAjax.php",
                            data: {saldo: consulta, chkall: chkall, anio: anio, anio2: anio2, fmod: fmod},
                            beforeSend: function () {//imagen de carga                            
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
        <script type="text/javascript">
            function borrar() {
                if (confirm("¿Esta Seguro que quiere borrar la orden de compra seleccionada?"))
                    return true;
                else
                    return false;
            }
        </script>

        <title>SigBod - Buscar Documentos</title>
    </head>
    <body class="fondo" onload="<?= $dir ?>">        
        <table class="table2a">
            <tr>
                <td class="texto">CONSULTA DE DOCUMENTOS CONTRA ORDEN DE COMPRA</td>
            </tr>
            <tr>
                <td class="texto5">
            <center>Ingrese Texto a Buscar:
                <span class="texto5"><input id="busqueda" name="txtcod" type="search" placeholder="escriba aqui..." autofocus size="25" maxlength="20" class='caja' onkeypress="if (event.keyCode == 13)
                    buscar.click();"/>&nbsp;&nbsp;<button id="buscar" class="boton1">Buscar&nbsp;<img class="imgbtnpeq" alt="buscador" title="Buscar" src="../img/lupa.png"></button>
                    &nbsp;&nbsp;Filtros:
                </span>
                <span class='caja_pading2'>Creado:
                    <input type="date" class='caja_texto' name="fecha" id="fecha" step="1" min="" max="<?= $fechaact ?>" value=""/>&nbsp; Al&nbsp;
                    <input type="date" class='caja_texto' name="fecha2" id="fecha2" step="1" min="" max="<?= $fechaact ?>" value=""/>&nbsp;
                    Modificado:
                    <input type="date" class='caja_texto' name="fmod" id="fmod" step="1" min="" max="<?= $fechaact ?>" value=""/>&nbsp;
                    <input type="checkbox" name="checkall" id="checkall" <?= $chk ?> /> Mostrar Todos
                </span>
            </center>
        </td>            
    </tr>        
    <tr>
        <td class="texto5"><center><button class="botonnormal" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="botonnormal" onclick="window.location = 'cargaDocumento.php'">Nuevo Documento&nbsp;<img class="img" alt="nuevo" title="Crear Documento" src="../img/doc3.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;
        <button onclick="window.location = '../exportar/exportaExcelDocDet.php'" class="botonnormal">Exportar a Excel&nbsp;&nbsp;<img class='img' alt='exportar' title='Exportar a Excel los datos mostrados en pantalla' src='../img/excel3.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button id="capturar" class="botonnormal" onclick="window.open('../reportes/docDetOcPDF.php', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=960,height=640 left=150,top=20')">Generar Informe en PDF&nbsp;&nbsp;<img class="img" alt="pdf" title="Exporta los datos actuales a formato PDF" src="../img/pdf1.png"></button></center></td>
</tr>
</table>
<div id="resultado"></div>
<a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
</body>
</html>