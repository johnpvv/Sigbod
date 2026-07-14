<!DOCTYPE html>
<!-- fecha creacion: 17/05/2019
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
        <script type="text/javascript">
            function validar() {
                var total = document.getElementById("txttotalneto").value;
                if (total === "0") {
                    alert("Error, debe ingresar al menos un articulo para generar cotizacion...");
                    return false;
                } else {
                    if (confirm("¿Esta Seguro que los datos estan correctos?")) {
                    var form = document.getElementById("form1");
                    form.setAttribute("method", "post");
                    form.setAttribute("action", "cotPDF.php");
                    form.setAttribute("target", "_blank");
                    form.submit();    
                    } else {
                        return false;
                    }
                }
            }
        </script>
        <script type="text/javascript">
            function cambiarPrecio(id) {
                var precio = document.getElementById(id).value;
                if (precio === "") {
                } else {
                    var read = document.getElementById(id).readOnly;
                    if (read === true) {
                        alert("Ya no se puede modificar el precio...");
                    } else {
                        if (confirm("¿Esta Seguro que quiere cambiar el precio?")) {
                        } else {
                            document.getElementById(id).readOnly = true;
                            alert("Se ha desactivado la modificacion de precio para esta linea...");
                            return false;
                        }
                    }
                }
            }
        </script>
        <script type="text/javascript">
            function actualizarPrecio(id) {
                var precio = document.getElementById(id).value;
                if (precio === "0") {
                    alert("El Valor no puede ser cero.");
                    document.getElementById(id).focus;
                } else {
                    var totalid = id.replace("txtprec_", "txtprectotal_");
                    var cantid = id.replace("txtprec_", "txtreq_");
                    var totalanterior = document.getElementById(totalid).value;
                    var neto = document.getElementById("txttotalneto").value;
                    var totalnuevo = document.getElementById(id).value;
                    var cuenta = document.getElementById(cantid).value;
                    var suma = totalnuevo * cuenta;
                    var nuevoneto = neto - totalanterior;
                    document.getElementById(totalid).value = suma;
                    var nuevovalor = nuevoneto + suma;
                    $("#txttotalneto").val(nuevovalor);
                    $("#txtiva").val(Math.round(nuevovalor * 0.19));
                    $("#txttotalfinal").val(Math.round(nuevovalor * 1.19));
                }
            }
        </script>
        <script type="text/javascript">
            function buscar(){
                window.open('listarBuscaArticulo.php', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=720,height=480 left=300,top=50');
            }
        </script>
        <script type="text/javascript">
            function agregarFila() {
				$('#cabecera').show();
				$('#neto').show();
				$('#iva').show();
				$('#total').show();
                var flag=document.getElementById("flag").value;
                if (flag==="0"){
                    alert("Para agregar otra fila, primero complete la actual.");
                }else{
                    var table = document.getElementById("tablasaldo");
                    var cont = table.rows.length;//se resta 11 porque son las filas que tienen datos fijos en la tabla
                    var cuenta = parseInt($("#contador").val());
                    $("#contador").val(cuenta + 1);
                    $("#contart").val(cont - 10);
                    var id="txtcod_"+cuenta;
                    document.getElementById("tablasaldo").insertRow(cont - 5).innerHTML = '<td>' + (cuenta + 1) + '</td><td><input required type="text" class="caja2" size="7" maxlength="8" id="' + id + '" name="' + id + '">&nbsp;<img class="imgpeq" id="img_' + (cuenta) + '" alt="buscar" title="buscar" src="../img/lupan.png" onclick="buscar()"></td>\n\
                    <td><input type="text" id="txtcm_' + (cuenta) + '" name="txtcm_' + (cuenta) + '" value="" size="6" class="caja2" readonly></td>\n\
                    <td align="left"><input type="text" id="txtglosa_' + (cuenta) + '" name="txtglosa_' + (cuenta) + '" value="" size="60" class="caja2a" readonly></td>\n\
                    <td><input type="text" id="txtum_' + (cuenta) + '" name="txtum_' + (cuenta) + '" value="" size="7" class="caja2" readonly></td>\n\
                    <td class="texto9">$ <input type="text" class="caja2c" size="8" value="" id="txtprec_' + (cuenta) + '" name="txtprec_' + (cuenta) + '" required onclick="cambiarPrecio(this.id)" onchange="actualizarPrecio(this.id)" readonly></td>\n\
                    <td><input required type="text" class="caja_azul" size="6" value="" id="txtreq_' + (cuenta) + '" name="txtreq_' + (cuenta) + '" readonly></td>\n\
                    <td class="texto9">$ <input type="text" class="caja2c" size="10" value="" id="txtprectotal_' + (cuenta) + '" name="txtprectotal_' + (cuenta) + '" readonly></td>\n\
                    <td><img class="imgcentro" alt="borrar articulo" id="' + (cuenta) + '" title="Borrar Articulo" src="../img/trash.png" onclick="eliminarFila(this.parentNode.parentNode.rowIndex,this.id)"></td>';
                    document.getElementById(id).focus();
                    document.getElementById("flag").value = "0";
                    document.getElementById("agregar").className = "boton1";
                    document.getElementById("id").value = id;
                }
            }
        </script>
        <script type="text/javascript">
            function eliminarFila(i, x) {
                var id = "txtprectotal_" + x;
                var valor = document.getElementById(id).value;
                var table = document.getElementById("tablasaldo");
                var rowCount = table.rows.length;
                if (valor === "") {
                    table.deleteRow(i);
                    $("#contart").val(rowCount - 12);
                    document.getElementById("flag").value = "1";
                    document.getElementById("agregar").className = "boton1a";
                } else {
                    if (confirm("¿Esta Seguro que quiere eliminar el artículo?")) {
                        var valorneto = document.getElementById("txttotalneto").value;
                        var nuevovalor = valorneto - valor;
                        $("#txttotalneto").val(nuevovalor);
                        $("#txtiva").val(Math.round(nuevovalor * 0.19));
                        $("#txttotalfinal").val(Math.round(nuevovalor * 1.19));
                        document.getElementById("flag").value = "1";
                        document.getElementById("agregar").className = "boton1a";
                        if (rowCount <= 1) {
                            alert('No se pueden eliminar los Títulos');
                        } else {
                            table.deleteRow(i);
                            $("#contart").val(rowCount - 12);
                        }
                    } else {
                        return false;
                    }
                }
            }
        </script>
        <script type="text/javascript"> //consultar datos y autollenar campos 
            $(document).change(function () {
                var i = ($("#contador").val()) - 1; //determinar posicion en la fila de la tabla
                //$("body").on("blur", "#txtcod_" + i, function (event) {//activar funcion al sacar el foco del input del codigo
				$("#txtcod_" + i).on("blur", function blur(){
				//alert("hola");
                    //event.preventDefault();//para que funcione el selector de jquery con variables dinamicas
                    var valor = $(this).val(); //sacar el valor del input del codigo
                    $.ajax({
                        url: "../herramientas/buscaCod.php",
                        type: "POST",
                        dataType: "json",
                        data: {val: valor},
                        success: function (res) {//objeto que trae los parametros json elegidos
                            if (res.glosa === null) {
                                $("#txtcod_" + i).val("");
                                $("#txtcm_" + i).val("");
                                $("#txtglosa_" + i).val("");
                                $("#txtum_" + i).val("");
                                $("#txtprec_" + i).val("");
                                $("#txtprectotal_" + i).val("");
                                $("#txtreq_" + i).val("");
                                alert("Error...\nEl codigo ingresado: '" + valor + "' , NO Existe");
                            } else {
                                $("#txtcm_" + i).val(res.cm); //asignar los valores a los input elegidos dinamicos
                                $("#txtglosa_" + i).val(res.glosa);
                                $("#txtum_" + i).val(res.um);
                                $("#txtprec_" + i).val(res.prec);
                                $("#txtcod_" + i).attr("readonly", true);
                                $("#txtprec_" + i).attr("readonly", false);
                                $("#txtreq_" + i).attr("readonly", false);
                                $("#flag").val("1");
                                $("#agregar").addClass("boton1a");
                                $("#txtreq_" + i).focus();
                            }
                        }
                    });
                });

            });
        </script> 
        <script type="text/javascript">
            $(document).change(function () {
                var z = ($("#contador").val()) - 1;
                $("body").on("change", "#txtreq_" + z, function (event) {//activar funcion al sacar el foco del input del codigo
                    event.preventDefault();//para que funcione el selector de jquery con variables dinamicas
                    var cant = $(this).val();
                    var precio = $("#txtprec_" + z).val();
                    var precioant = $("#txtprectotal_" + z).val();
                    var suma = 0;
                    var sumatotal = 0;
                    var sumafinal = 0;
                    if (precioant === "") {
                        suma = Math.round(precio * cant);
                        $("#txtprectotal_" + z).val(suma);
                        sumatotal = parseInt($("#txttotalneto").val());
                        $("#txttotalneto").val(sumatotal + suma);
                        $("#txtiva").val(Math.round((sumatotal + suma) * 0.19));
                        $("#txttotalfinal").val(Math.round((sumatotal + suma) * 1.19));
                    } else {
                        sumatotal = parseInt($("#txttotalneto").val());
                        sumatotal = (sumatotal - precioant);
                        suma = Math.round(precio * cant);
                        sumafinal = sumatotal + suma;
                        $("#txtprectotal_" + z).val(suma);
                        $("#txttotalneto").val(sumafinal);
                        $("#txtiva").val(Math.round(sumafinal * 0.19));
                        $("#txttotalfinal").val(Math.round(sumafinal * 1.19));
                    }
                });
            });
        </script> 
        <title>SigBod - Crear Cotización</title>
    </head>
    <body class="fondo">
        <?php
        $fecha = date("d/m/Y");
        ?>
        <form id="form1" name="form1" onsubmit= "return validar()">
            <table class="table1" id="tablasaldo">
                <tr>
                    <td colspan="9" class="texto"><center>Generar Cotización</center></td>
                </tr>
                <tr>
                    <td colspan="3" class="texto8">FECHA ACTUALIZACIÓN:</td>                    
                    <td colspan="6" class="texto9"><?php echo $fecha; ?></td>
                </tr>                
                <tr>
                    <td colspan="3" class="texto8">COMENTARIOS:</td>                    
                    <td colspan="6" class="texto9"><textarea name="txtcom" id="glosa" class="caja_negrita" cols="110" rows="4"></textarea></td>
                </tr>
                <tr>
                    <td colspan="3" class="texto8">N° ARTÍCULOS:</td>                    
                    <td colspan="6" class="texto9"><input type="text" class="caja2b" id="contart" name="contart" size="2" value="0"></td>
                </tr>
                <tr>
                    <td colspan="9" class="texto8c"></td>
                </tr>
                <tr class="texto5" id="cabecera" style="display:none;">
                    <th>N°</th>
                    <th width="130">Código Interno</th>
                    <th width="120">Código CM</th>
                    <th width="500">Glosa</th>
                    <th width="80">Unidad de Medida</th>
                    <th width="120">Precio Unitario</th>
                    <th width="80">Cantidad Requerida</th>
                    <th width="140">Total Unitario Neto</th>
                    <th width="50">Acción</th>
                </tr>
                <tr>
                    <td colspan="9" class="texto8c"><button type="button" class="boton1a" id="agregar" onclick="agregarFila()" title="Pulse para agregar un artículo"><b>&nbsp;+&nbsp;</b></button></td>
                </tr>
                <tr id="neto" style="display:none;">
                    <td colspan="6" class="texto8">Neto:</td>                    
                    <td colspan="3" class="texto9"><b>$ </b><input type="text" class="caja2b" id="txttotalneto" name="txttotalneto" value="0" size="17" readonly>
                    <input type="hidden" name="contador" id="contador" value="0">
                    <input type="hidden" name="flag" id="flag" value="1">
                    <input type="hidden" name="id" id="id" value=""></td>
                    
                </tr>
                <tr id="iva" style="display:none;">
                    <td colspan="6" class="texto8">IVA:</td>                    
                    <td colspan="3" class="texto9"><b>$ </b><input type="text" class="caja2b" id="txtiva" name="txtiva" value="0" size="17" readonly>
                    </td>
                </tr>
                <tr id="total" style="display:none;">
                    <td colspan="6" class="texto8">TOTAL FINAL:</td>                    
                    <td colspan="3" class="texto9"><b>$ </b><input type="text" class="caja2c" id="txttotalfinal" name="txttotalfinal" value="0" size="17" readonly>
                    </td>
                </tr>
                <tr>
                    <td colspan="9" class="texto5">
                        <button class="botonnormal" type="button" onclick="window.location = '../principal.php'">Volver&nbsp;&nbsp;<img class='img' alt='volver' title='Volver a pantalla anterior' src='../img/undo.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" class="botonnormal">Generar en PDF&nbsp;&nbsp;<img class='img' alt='exportar' title='Exportar a PDF' src='../img/pdf1.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="botonnormal" type="button" onclick="window.location = 'crearCotizacion.php'">Limpiar&nbsp;&nbsp;<img class='img' alt='modificar' title='limpiar formulario' src='../img/clean.png'></button></td>
                </tr>
            </table>
        </form>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>