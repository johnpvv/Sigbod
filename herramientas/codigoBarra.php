<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<?php
include_once("../include/conn.php");
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$cod = $_GET["id"];
if ($cod == "") {
    echo '<script>alert("Error...\nEl codigo ingresado es invalido, o se encuentra Inactivo");
				history.back(-1);</script>';
}
?>
<html>
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />         
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>
        <title>SigBod - Actualizar código de Barras</title>
        <script type="text/javascript">
            function obtenerLote() {
                var codBar = $("#codbr").val();
                var codigoEAN = "";
                var codigofinal = "";
                if (jQuery.trim(codBar).substr(0, 2) == "01") {
                    codigoEAN = jQuery.trim(codBar).substr(2, 14);
                    //codigofinal =  extraePosiciondeControl(codigoEAN);
                    codigofinal = codigoEAN;
                } else {
                    codigofinal = codbar;
                }
                $("#codbr").val(codigofinal);
            }
        </script>
        <script type="text/javascript"> //consultar codigo de barras
            function Validar() {
                $("#codbr").attr("readonly", "readonly");
                obtenerLote();
            }
        </script>
        <script type="text/javascript"> //consultar codigo de barras
            $(document).ready(function () {
                $("#salir").click(function () {
                    window.opener.desbloquea();
                });
            });
        </script>

        <script type="text/javascript">
            window.onunload = unloadPage;
            function unloadPage() {//para desbloquear al presionar cerrar en la ventanita
                window.opener.desbloquea();
            }
        </script>
        <script type="text/javascript"> //consultar codigo de barras
            $(document).ready(function () {
                $("#boton").click(function () {//activar funcion al sacar el foco del input del codigo
                    var valor = $("#codbr").val(); //sacar el valor del input del codigo                    
                    var cod = $("#codprd").val();
                    if (valor !== "") {
                        $("#boton").css("background", "");
                        $("#boton").css('cursor', 'pointer');//cursor mause error
                        $.ajax({
                            url: "../herramientas/codigoBarraAjax.php",
                            type: "POST",
                            dataType: "html",
                            data: {val: valor, cod: cod},
                            beforeSend: function () {
                                $("#resultado").html("<p class='centrar_cuadro'>Cargando, por favor espere...<img src='../img/ajax-loading.gif' width='150' height='100' /></p>");
                            },
                            error: function () {
                                alert("error peticion ajax");
                            },
                            success: function (data) {
                                $("#resultado").empty();
                                $("#resultado").append(data);
                                window.opener.desbloquea();//desbloquear pantalla de articulo
                                window.close();//cerrar ventana
                            }
                        });
                    } else {
                        $("#boton").css("background", "black");
                        $("#boton").css('cursor', 'no-drop');//cursor mause error
                    }
                });
            });
        </script>
    </head>
    <body class="fondo">  
        <div id="resultado">
            <table class="table4">
                <tr>
                    <td class="texto1">Escanear Código:&nbsp;&nbsp;
                        <input type="text" name="codbr" class="caja" id="codbr" value="" size="20" autofocus onKeypress="if (event.keyCode === 13)
                                    Validar();" required/>
                        <input type="hidden" value="<?= $cod ?>" id="codprd"></td>
                </tr>
                <tr>
                    <td class="texto5a"><button type='button' name='boton' id="boton" value="submit" class="botonnormal">Grabar&nbsp;&nbsp;<img class='img' alt='cargar' title='grabar' src='../img/save.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="botonnormal" type="button" id="salir" onclick="window.close()">Salir&nbsp;&nbsp;<img class='img' alt='volver' title='salir' src='../img/salir.png'></button></td>
                </tr>
            </table>
        </div>
    </body>
</html>