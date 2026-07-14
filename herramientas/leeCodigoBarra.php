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
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>
        <title>SigBod - código de Barras</title>
        <script type="text/javascript">
            $(document).ready(function () {
                $('#ticket').hide();
            });
        </script>
        <script type="text/javascript">
            function validarFecha(date) {
                //alert(date+"valida");				
                var x = new Date();
                fecha = date.split("/");
                x.setFullYear(fecha[2], fecha[1] - 1, fecha[0]);
                var today = new Date();

                if (x <= today)
                    $('#ticket').show();
            }
        </script>
        <script type="text/javascript">
            function obtenerLote() {
                var codBar = $("#codigo").val();
                var codigoEAN = "";
                var codigofinal = "";
                var fecha = "";
                var ano = "";
                var mes = "";
                var valorf = "";
                var lote = "";
                var Fvence = "";
                var Fprod = "";
                var lotefinal = "";
                var fechafinal = "";
                var elabfinal = "";
                if (jQuery.trim(codBar).substr(0, 2) == "01") {
                    codigoEAN = jQuery.trim(codBar).substr(2, 14);
                    //codigofinal =  extraePosiciondeControl(codigoEAN);
                    codigofinal = codigoEAN;
                }
                if (jQuery.trim(codBar).length > 18) {
                    if (jQuery.trim(codBar).substr(16, 2) == "17" || jQuery.trim(codBar).substr(16, 2) == "12") {
                        fecha = jQuery.trim(codBar).substr(18, 6);
                        ano = fecha.substr(0, 2); //ano
                        if (ano == "00") {
                            ano = "99";
                        }
                        mes = fecha.substr(2, 2); //mes
                        if (mes == "00") {
                            mes = "12";
                        }
                        dia = fecha.substr(4, 2); // dia
                        if (dia == "00") {
                            dia = "28";
                        }
                        anoactual = $("#ano").val();
                        fechafinal = dia + "/" + mes + "/" + anoactual + ano;
                    }
                    if (jQuery.trim(codBar).substr(16, 2) == "11") {
                        valorf = jQuery.trim(codBar).length - 18;
                        //alert(valorf);
                        lote = jQuery.trim(codBar).substr(18, valorf);
                        fechaelab = jQuery.trim(codBar).substr(18, 6);
                        //alert(Fprod);
                        //fechaelab = Fprod.substr(0, 2);
                        anoelab = fechaelab.substr(0, 2); //ano
                        if (anoelab == "00") {
                            anoelab = "99";
                        }
                        meselab = fechaelab.substr(2, 2); //mes
                        if (meselab == "00") {
                            meselab = "12";
                        }
                        diaelab = fechaelab.substr(4, 2); // dia
                        if (diaelab == "00") {
                            diaelab = "28";
                        }
                        anoactual = $("#ano").val();
                        elabfinal = diaelab + "/" + meselab + "/" + anoactual + anoelab;
                    }
                    if (jQuery.trim(codBar).substr(16, 2) == "10") {
                        valorf = jQuery.trim(codBar).length - 18;
                        lote = jQuery.trim(codBar).substr(18, valorf);
                        Fvence = lote.indexOf("17");
                        Fprod = lote.indexOf("11");

                        if (Fvence != -1 && Fprod != -1) {
                            if (Fvence > Fprod) {
                                lotefinal = lote.substr(0, Fprod);
                            }
                            if (Fvence < Fprod) {
                                lotefinal = lote.substr(0, Fvence);
                            }
                        }
                        fecha = lote.substr(Fvence, 6);
                        ano = fecha.substr(0, 2); //ano
                        if (ano == "00") {
                            ano = "99";
                        }
                        mes = fecha.substr(2, 2); //mes
                        if (mes == "00") {
                            mes = "12";
                        }
                        dia = fecha.substr(4, 2); // dia
                        if (dia == "00") {
                            dia = "28";
                        }
                        anoactual = $("#ano").val();
                        fechafinal = dia + "/" + mes + "/" + anoactual + ano;
                    }
                    if (jQuery.trim(codBar).substr(24, 2) == "10" || jQuery.trim(codBar).substr(24, 2) == "21") {
                        var valor = jQuery.trim(codBar).length - 26;

                        lote = jQuery.trim(codBar).substr(26, valor);

                        if (lote.indexOf("37") != -1) {
                            lote = lote.substr(0, lote.indexOf("37"));
                        }
                        lotefinal = lote;
                    }
                }
                $("#lote").val(lotefinal);
                $("#fecha").val(fechafinal);
                $("#cod").val(codigofinal);
                validarFecha(fechafinal);
                $("#elab").val(elabfinal);
            }
        </script>
        <script type="text/javascript">
            function generar() {
                $("#img").hide();
                var lote = $("#lote").val();
                var ffin = $("#fecha").val();
                var cod = $("#cod").val();
                if (lote === "") {
                    alert("Error, no se puede generar Codigo QR, hay Campos Vacios.");
                } else {
                    //var elab = $("#elab").val();
                    var string = "*Bodega Insumos Clinicos* Serie Producto: " + cod + ", Lote producto: " + lote + ", Fecha Vencimiento: " + ffin;
                    var src = "https://qrcode.tec-it.com/API/QRCode?data=" + string + "&size=Small&errorcorrection=M";
                    $("#img").attr("src", src);
                    $("#img").show();
                    //<img src="https://qrcode.tec-it.com/API/QRCode?data=Generador+de+C%c3%b3digos+QR+de+TEC-IT" />
                    //<img src='https://barcode.tec-it.com/barcode.ashx?data=01007630002370661720081421NWL431779G&code=EANUCC128&multiplebarcodes=false&translate-esc=true&unit=Min&dpi=96&imagetype=Jpg&rotation=0&color=%23000000&bgcolor=%23ffffff&qunit=Mm&quiet=0' alt='Barcode Generator TEC-IT'/>
                }
			}				
			function crearEtiqueta(){
				var lote = $("#lote").val();
				var ffin = $("#fecha").val();
                var cod = $("#cod").val();
				var string = "Serie_Producto(" + cod + ");Lote_producto(" + lote + ");Fecha_Vencimiento(" + ffin + ")";				
				if (lote === "") {
                    alert("Error, no se puede generar Etiquetas, hay Campos Vacios.");
                } else {
				window.open('../reportes/etiquetaLote.php?id='+string+'', '_blank', 'scrollbars=0,statusbar=0,resizable=0,width=800,height=500,left=300,top=100');
				}
				
			}
			
        </script>	
    </head>
    <body class="fondo">
        <table class="table5">
            <tr>
                <td colspan="2" class="texto"><img class="imgico" alt='codbarra' title='Codigo de barra' src='../img/barcode.png'>&nbsp;&nbsp;&nbsp;Procesador de Códigos</td>
            </tr>
            <tr>
                <td colspan="2" class="caja_pading">Código a Procesar:&nbsp;&nbsp;
                    <input type="text" id="codigo" class="caja" onchange='obtenerLote();' autofocus size="40">                    
                </td>       
            </tr>
            <tr>
                <td colspan="2" class="texto8b">Resultados:</td>
            </tr>
            <tr>
                <td class="caja_pading" align="right">Código Producto:</td>
                <td class="caja_pading" align="left"><input id="cod" type="text" class="caja_negrita" size="25" readonly ></td>
            </tr>
            <tr>
                <td class="caja_pading" align="right">Lote:</td>
                <td class="caja_pading" align="left"><input id="lote" type="text" class="caja" size="25" readonly ></td>
            </tr>
            <tr>
                <td class="caja_pading" align="right">Fecha Vencimiento:</td>
                <td class="caja_pading" align="left"><input id="fecha" type="text" class="caja" size="25" readonly >&nbsp;&nbsp;&nbsp;
                    <img class="imgnormal" src="../img/error.png" id="ticket" title="El codigo ingresado esta VENCIDO"> </td>
            </tr>
            <tr>
                <td class="caja_pading" align="right">Fecha Elaboracion:</td>
                <td class="caja_pading" align="left"><input id="elab" type="text" class="caja" size="25" readonly >
                    <input type="hidden" value="20" id="ano"></td>
            </tr>
            <tr>
                <td colspan="2" class="texto6">
				<button class="botonnormal" onclick="window.close()">Salir&nbsp;<img class="img" alt="principal" title="Cerrar" src="../img/salir.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;
				<button class="botonnormal" onclick="location.reload();">Limpiar&nbsp;<img class="img" alt="recargar" title="Limpiar" src="../img/clean.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;
				<button class="botonnormal" onclick="generar()">Generar&nbsp;<img class="img" alt="crear" title="Crear" src="../img/barcode.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;
				<button class="botonnormal" onclick="crearEtiqueta();">Imprimir Etiqueta&nbsp;<img border='0' alt='crear etiqueta' title='crear etiqueta del articulo' src='../img/printlabel.png' class="img"></button>
				</td>
            </tr>
            <tr>
                <td colspan="2" class="caja_pading">
                    <img src="" id ="img" style="width:160px; height: 160px;" hidden="yes"/>
                </td>
            </tr>
        </table>
    </body>
</html>