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
$date=date("Y-m-d",strtotime(date("Y-m-d")."+ 1 days"));
?>
<html>
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <title>SigBod - Cartola Articulos</title>
        <script type="text/javascript">
            function buscarCod() {
                var cod = $("#txtcod").val();
                if (cod === "" || cod.length <= 7) {
                    alert("Debe Introducir Un Codigo Valido Ej. 123-4567");
                    document.cart.id.focus();
                    return false;
                } else {
                    cart.action = "../reportes/cartolaArticulo.php";
                    cart.submit();
                }
            }
        </script>
    </head>
    <body class="fondo">
        <form id="cart" name="cart" method="GET" onsubmit= "return buscarCod()">
            <table class="table">
                <tr>
                    <td class="texto" colspan="2">CARTOLA DE ART&Iacute;CULOS</td>
                </tr>
                <tr>
                    <td class="texto1">Ingrese C&oacute;digo Interno:</td>
                    <td><input type="text" name="id" id="txtcod" class="texto6" autofocus size="20" maxlength="8" onkeypress="if (event.keyCode == 13) buscarCod();"/>&nbsp;&nbsp;&nbsp;<a onclick="window.open('listarBuscarArticulo.php', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=720,height=480 left=300,top=50')"><img alt='buscador' class="imgnormal" title='Buscador' src='../img/lupa.png'></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Limpiar" class="boton1" title="refresca la pagina" onclick="location.href = 'buscarCartolaArticulo.php'"/></td>
                </tr>
                <tr>
                    <td class="texto8d">Fecha Inicio:&nbsp;&nbsp;<input type="date" class='texto1a' name="fini" step="1" min="2018-01-01" max="<?=$date?>" required value="2019-01-01"/>&nbsp;<img align='center' src='../img/datepicker.png' width='35' height='35' title="Seleccionar fecha inicio"/></td>
                    <td class="texto8d">Fecha Fin:&nbsp;&nbsp;<input type="date" class='texto1a' name="ffin" step="1" min="2018-01-01" max="<?=$date?>" value="<?=$date?>" required />&nbsp;<img align='center' src='../img/datepicker.png' width='35' height='35' title="Seleccionar fecha fin"/></td>   
                </tr>                
                <tr>
                    <td colspan="2"><center> <button class="botonnormal" type="submit">Buscar&nbsp;<img class="img" alt="buscador" title="Buscar Movimientos" src="../img/lupa.png"></button> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                    <button class="botonnormal" type="button" onClick='history.go(-1);'>Volver&nbsp;<img class="img" alt="volver" title="Volver a pantalla anterior" src="../img/undo.png"></button></center></</td>
                   <!-- para capturar el id del campo codigo y dejar el codigo elegido en buscador externo-->
                    <input type="hidden" value="0" name="op">
                </tr>
            </table>
        </form>
         <input type="hidden" name="id" id="id" value="txtcod">
    </body>
</html>