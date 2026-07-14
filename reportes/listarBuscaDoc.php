<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
?>
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
            function selectall(form) {
                var formulario = eval(form);
                var x = 0;
                for (var i = 0, len = formulario.elements.length; i < len; i++) {
                    if (formulario.elements[i].type === "checkbox") {
                        formulario.elements[i].checked = formulario.elements[0].checked;
                        ++x;
                    }
                }
                x = x - 1;
                if (document.getElementById('chktodo').checked) {//obtener si el check nombre chktodo esta marcado o no
                    if (x === 0) {
                        alert("Error, No Hay Elementos para Seleccionar");
                    } else {
                        if (x > 250) {
                            alert("Error... Los elementos seleccionados superan los 250, favor revisar.");
                            document.getElementById('enviar').disabled = true;
                            $("#enviar").css("background", "black");
                            $("#enviar").css('cursor', 'no-drop');//cursor mause error
                        } else {
                            alert("Se han Seleccionado un Total de: " + x + " documentos");
                            document.getElementById('enviar').disabled = false;
                            $("#enviar").css("background", "");
                            $("#enviar").css('cursor', 'pointer');
                        }
                    }
                } else {
                    document.getElementById('enviar').disabled = false;
                    $("#enviar").css("background", "");
                    $("#enviar").css('cursor', 'pointer');
                }
            }
        </script>
        <script>
            $(document).ready(function () {
                var consulta;
                $("#busqueda").focus();//hacemos focus al campo de busqueda               
                $("#buscar").click(function (e) {//comprobamos si se pulsa una tecla 
                consulta = $("#busqueda").val().replace(/[`~!@#$%^&*()_|+\=?;:'",.<>\{\}\[\]\\\/]/gi, '');//obtenemos el texto introducido en el campo de busqueda y elimina caracteres no correctos
                var chksub = $("#chksub").val();   
                    $.ajax({//hace la busqueda
                        type: "POST",
                        url: "listarBuscaDocAjax.php",
                        data: {b: consulta, chksub: chksub},
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
            function cerrar() {
                $("#load").show();
                var idpadre = window.opener.document.getElementById('id').value;
                var formulario = eval(form);
                var x = 0;
                var idx = idpadre.replace("txtdoc_", "");
                var ctn = idx;
                for (var i = 0, len = formulario.elements.length; i < len - 1; i++) {
                    if (formulario.elements[i].type === "checkbox") {
                        if (document.getElementById('check_' + x).checked) {
                            window.opener.document.getElementById('txtdoc_' + idx).value = document.getElementById('check_' + x).name;
                            window.opener.document.getElementById('agregar').click();
                            ++idx;
                        }
                        x++;
                    }
                }
                if (idx == ctn) {
                    alert("Error, no ha seleccionado elementos, no se puede enviar...");
                    $("#load").hide();
                } else {
                    window.opener.document.getElementById('eliminar_' + idx).click();
                    this.window.close();
                }
            }
        </script>
    </head>
    <body>
    <center>        
        <span class="texto6">Texto a Buscar:</span><input type="text" name="busqueda" class="caja_simple" size="20" id="busqueda" onkeypress="if (event.keyCode == 13)buscar.click();">
        <input type='button' value='X' class="boton1b" onclick="location.href = 'listarBuscaDoc.php'" title="Limpiar">&nbsp;&nbsp;
        <input type='button' value=' Buscar ' class="boton1a" id="buscar">&nbsp;&nbsp;
        <input type="button" value=" Enviar " class="boton1d" onclick="cerrar();" id="enviar">&nbsp;&nbsp;
        <input type='button' value=' Cerrar ' class="boton1" onclick='javascript:window.close();'>&nbsp;&nbsp;
        <select name="chksub" class="caja_texto" id="chksub" onchange="busqueda.function buscar() {e};">
            <option value=''>‌--Todos--</option>
			<option value='Anulado'>‌Anulado</option>
			<option value='Archivado'>Archivado</option>
			<option value='Devuelto'>‌Devuelto</option>
			<option value="1">Pendiente</option>
            <option value='Recepcionado'>‌Recepcionado</option>
        </select>&nbsp;&nbsp;
        <img id="load" class="imgnormal" alt="cargando" title="cargar" src="../img/load.gif" hidden="yes">
    </center>
    <div id="resultado"></div>
    <br><br><br>
    <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
</body>
</html>
