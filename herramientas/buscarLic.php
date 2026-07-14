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
?>
<html>
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <title>SigBod - Informacion Licitaciones</title>
        <script type="text/javascript">
            function buscarCod() {
                var cod = $("#txtcod").val().replace(/\s/g, "");
                //var valor = document.getElementById(cod).value
                var indices = [];
                for (var i = 0; i < cod.length; i++) {
                    if (cod[i].toLowerCase() === "-")
                        indices.push(i);//contar las veces que se repite el guion
                }
                if (indices.length < 2) {
                    alert("Error:\nLa Licitación ingresada no corresponde...");
                    $("#txtcod").val("");
                    $("#txtcod").focus();
                    return false;
                } else {
                    $("#txtcod").val(cod.toUpperCase());
                    cart.action = "../edicion/decodeJsonLic.php?op=1";
                    cart.submit();
                }
            }
        </script>
    </head>
    <body class="fondo">
        <form id="cart" name="cart" method="GET" onsubmit= "return buscarCod()">
            <table class="table">
                <tr>
                    <td class="texto" colspan="2">Buscar Detalle de Licitaciones</td>
                </tr>
                <tr>
                    <td class="texto1">Ingrese ID a Buscar:</td>
                    <td><input type="text" name="id" id="txtcod" class="texto6" autofocus size="20" maxlength="18" onkeypress="if (event.keyCode == 13)
                                buscarCod();"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="boton1" title="refresca la pagina" onclick="location.href = 'buscarInfoOC.php'"/>Limpiar&nbsp;<img class='imgbtnpeq' alt='limpiar' title='Limpiar Pantalla' src='../img/clean.png'> </button></td>
                </tr>            
                <tr>
                    <td colspan="2"><center> <button class="botonnormal" type="submit">Buscar&nbsp;<img class="img" alt="buscador" title="Buscar Detalles" src="../img/lupa.png"></button> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                    <button class="botonnormal" type="button" onClick='history.go(-1);'>Volver&nbsp;<img class="img" alt="volver" title="Volver a pantalla anterior" src="../img/undo.png"></button></center></</td>
                </tr>
            </table>
        </form>
    </body>
</html>