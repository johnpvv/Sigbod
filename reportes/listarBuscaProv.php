<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<html>
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/tablesorter.min.js" type="text/javascript"></script>
        <title>SigBod - Buscador v1.1</title> 
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
                $("#busqueda").keyup(function (e) {//comprobamos si se pulsa una tecla 
                    consulta = $("#busqueda").val().replace(/[`~!@#$%^&*()_|+\=?;:'",.<>\{\}\[\]\\\/]/gi,'');//obtenemos el texto introducido en el campo de busqueda y elimina caracteres no correctos
                    $.ajax({//hace la busqueda
                        type: "POST",
                        url: "listarBuscaProvAjax.php",
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
            });
        </script>
        <script type="text/javascript">
            function cerrar(idx) {
                var idpadre=window.opener.document.getElementById('rutpv').id;//capturar el id de la ventana padre o principal
                window.opener.document.getElementById(idpadre).value = idx; 
                window.opener.document.getElementById(idpadre).focus();
                this.window.close();
            }
        </script>
    </head>
    <body>
    <center>        
        <span class="texto6">Texto a Buscar:</span><input type="text" name="busqueda" class="caja_simple" size="25" id="busqueda">&nbsp;&nbsp;&nbsp;
        <input type='button' value='Limpiar' class="boton1a" onclick="location.href = 'listarBuscaProv.php'">&nbsp;&nbsp;
        <input type='button' value='Cerrar' class="boton1" onclick='javascript:window.close();'> 
    </center>
    <div id="resultado"></div>
    <br><br><br>
    <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
</body>
</html>
