<!DOCTYPE html>
<!-- fecha creacion 19/10/2022 hp
Sigbod John Vaccarella
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
include("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
$fechaact = date("Y-m-d");
$rs = mysql_query("SELECT est_lic_nombre,est_lic_cod FROM est_lic_mp", $link);
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
        <title>SigBod - Listar Licitaciones MP</title>
        <script>
            $(document).ready(function () {
                var consulta;
                $("#busqueda").focus();//hacemos focus al campo de busqueda               
                $("#buscar").click(function (e) {//comprobamos si se pulsa una tecla                    
                    consulta = $("#busqueda").val().replace(/[`~!@#$%^&*()_|+\=?;:'",.<>\{\}\[\]\\\/]/gi, '').trim();//obtenemos el texto introducido en el campo de busqueda y elimina caracteres no correctos
                    largo = $("#busqueda").val().length;
                    var anio = $("#fecha").val();
                    var est = $("#est").val();
                    if (largo === 0 || largo > 2) {
                        $.ajax({//hace la busqueda
                            type: "POST",
                            url: "listarLicMPAjax.php",
                            data: {saldo: consulta, anio: anio, est: est},
                            beforeSend: function () {//imagen de carga                            
                                $.blockUI({
                                    message: "<p class='centrar_cuadro_alertas'>Descargando de Mercado Publico, favor espere...<img src='../img/clock.gif' width='140' height='140'/></p>"
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
        <script>
            function exporta() {
                var rev = $("#flag").val();
                if (rev == null) {
                    alert("Error, no hay resultados para exportar.");
                } else {
                    document.export.action = "../exportar/exportaResImp.php";
                    document.export.submit();
                }
            }
        </script>
        <script type="text/javascript">
            function cambiaImg(id) {
                var nuevaImagen = "../img/ped.png";
                $("#" + id).attr("src", nuevaImagen);  // cambia para saber que se revisó
            }
        </script>
    </head>
    <body class="fondo">        
        <table class="table2a">
            <tr>
                <td class="texto">Listar Licitaciones desde Mercado Público</td>
            </tr>
            <tr>
                <td class="texto5b">Ingrese Licitación a Buscar:
                    <span class="texto5"><input id="busqueda" name="txtcod" type="search" placeholder="escriba aqui..." autofocus size="30" maxlength="20" class='caja' onkeypress="if (event.keyCode == 13)
                                buscar.click();"/>&nbsp;&nbsp;
                    </span>
                    <span class='caja_pading2'>Fecha Licitación:
                        <input type="date" class='caja_texto' name="fecha" id="fecha" step="1" min="" max="<?= $fechaact ?>" value=""/>&nbsp;&nbsp;&nbsp;
                        Estado Licitación: <select name="est" id="est" class="caja_texto" required> 
                            <option value="" selected>Seleccionar una opción</option>
                            <?php
                            while ($row = mysql_fetch_array($rs)) {
                                echo"<option value=", $row["est_lic_nombre"], ">", $row["est_lic_nombre"], "</option>";
                            }
                            ?>
                        </select>
                    </span>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button id="buscar" class="boton1">Buscar&nbsp;<img class="imgbtnpeq" alt="buscador" title="Buscar" src="../img/lupa.png"></button>&nbsp;&nbsp;

                </td>            
            </tr>        
            <tr>
                <td class="texto5">
                    <button class="botonnormal" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button onclick="exporta();" class="botonnormal">Exportar a Excel&nbsp;&nbsp;<img class='img' alt='exportar' title='Exportar a Excel los datos mostrados en pantalla' src='../img/excel3.png'></button>
                </td>
            </tr>
        </table>
        <div id="resultado"></div>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>