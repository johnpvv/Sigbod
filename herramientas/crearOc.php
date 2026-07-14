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
$link = Conectarse();
$rs = mysql_query("SELECT * FROM const_config WHERE const_cat ='est' AND const_est='1'", $link); //constantes de configuracion, reconoce todos los categoria establecimiento guardados
$row1 = mysql_fetch_array($rs);
$org = $row1["const_val"];
$oc = $_GET["oc"];
$op = $_GET['op'];
if ($op == 1) {
    $btn = '<button class="botonnormal" type="button" onclick="window.close()">Salir&nbsp;<img class="img" alt="principal" title="Salir" src="../img/salir.png"></button>';
} else {
    $btn = '<button class="botonnormal" type="button" onclick="javascript:history.back(-1)">Volver&nbsp;<img class="img" alt="principal" title="Salir" src="../img/undo.png"></button>';
}
if ($_SESSION['oc'] == $oc) {
    echo"<script>alert('Error, La OC ya se ha grabado en sistema.');window.close();</script>";
    exit();
}
if ($oc == !"") {
    $dir = "dwnl.click()";
} else {
    $dir = "";
}
?>
<html>
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#txtoc").focus();
                $('#ticket').hide();
				$('#ticket1').hide();
                $('#est').hide();
                $('#dwnl').hide();
                $('#provfic').hide();
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
                    alert("Error, debe ingresar al menos un articulo para generar OC...");
                    return false;
                } else {
                    if (confirm("¿Esta Seguro que los datos estan correctos?")) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        </script>
        <script type="text/javascript">
            $(document).ready(function (e) {
                $("#form1").on('submit', (function (e) {
                    e.preventDefault();
                    if (validar()) {
                        $.ajax({
                            url: 'crearOcAjax.php',
                            data: $("#form1").serialize(),
                            type: 'POST',
                            beforeSend: function () {
                                $.blockUI({
                                    message: "<p class='centrar_cuadro_alertas'>Creando OC, por favor espere...<img src='../img/loading.gif' width='200' height='140' /></p>"
                                });
                            },
                            error: function () {
                                alert("error peticion ajax");
                            },
                            success: function (data) {
                                $("#resultado").empty();
                                $("#resultado").append(data);
                                $('#ticket1').show();
                                $.unblockUI();
                            }
                        });
                    }
                }));
            });
        </script>
        <script type="text/javascript">
            function cambiarPrecio(id) {
                var precio = document.getElementById(id).value;
                if (precio === "") {
                } else {
                    var read = document.getElementById(id).readOnly;
                    if (read === true) {
                        alert("No se puede modificar el precio... Revise que se encuentren bien asociados los codigos internos.");
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
                    alert("El Valor no puede ser Cero");
                    document.getElementById(id).value = "";
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
            function calcPrecioNuevo(id) {
                var cant = document.getElementById(id).value;
                var totalid = id.replace("txtreq_", "txtprectotal_");
                var precioid = id.replace("txtreq_", "txtprec_");
                var total = document.getElementById(totalid).value;
                var cant = document.getElementById(id).value;
                if (cant == 0 || isNaN(cant)) {
                    alert("Error, Revisar la Cantidad");
                    document.getElementById(precioid).value = "";
                    document.getElementById(id).value = "";
                } else {
                    var resultado = (total / cant);
                    document.getElementById(precioid).value = resultado;
                }
            }
        </script>
        <script type="text/javascript">
            function buscar(id) {
                var idx = id.replace("img", "txtcod");
                document.getElementById("id").value = idx;
                window.open('../reportes/listarBuscarArticulo.php', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=720,height=480 left=300,top=50');
            }
        </script>
        <script type="text/javascript">
            function buscarProv() {
                window.open('../reportes/listarBuscaProv.php', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=720,height=480 left=300,top=50');
            }
        </script>
        <script type="text/javascript">
            function agregarFila() {
                var flag = document.getElementById("flag").value;
                if (flag === "0") {
                    var pos = document.getElementById("id").value;
                    alert("Para agregar otra fila, primero complete la actual.");
                    document.getElementById(pos).focus();
                } else {
                    var table = document.getElementById("tablasaldo");
                    var cont = table.rows.length;//se resta 11 porque son las filas que tienen datos fijos en la tabla
                    var cuenta = parseInt($("#contador").val());
                    $("#contador").val(cuenta + 1);
                    $("#contart").val(cont - 16);//se resta 15 porque son las filas que tienen datos fijos en la tabla y asi ingrese la fila en la posicion correcta
                    var id = "txtcod_" + cuenta;
                    document.getElementById("tablasaldo").insertRow(cont - 5).innerHTML = '<td>' + (cuenta + 1) + '</td><td><input required type="textbox" class="caja2" size="7" maxlength="8" id="' + id + '" name="' + id + '" onBlur="buscarCod(this.id,' + cuenta + ')">&nbsp;<img class="imgpeq" id="img_' + (cuenta) + '" alt="buscar" title="buscar" src="../img/lupan.png" onclick="buscar(this.id)"></td>\n\
                    <td><input type="text" id="txtcm_' + (cuenta) + '" name="txtcm_' + (cuenta) + '" value="" size="6" class="caja2" readonly></td>\n\
                    <td align="left"><input type="text" id="txtglosas_' + (cuenta) + '" name="txtglosas_' + (cuenta) + '" value="" size="85" class="caja2f" title="Glosa del sistema" readonly><input type="text" class="caja2a1" id="txtglosa_' + (cuenta) + '" name="txtglosa_' + (cuenta) + '" value="" size="85" title="Glosa del Portal" readonly></td>\n\
                    <td><input type="text" id="txtum_' + (cuenta) + '" name="txtum_' + (cuenta) + '" value="" size="7" class="caja2" readonly></td>\n\
                    <td class="texto9">$ <input type="number" class="caja_val" step="any" value="" id="txtprec_' + (cuenta) + '" name="txtprec_' + (cuenta) + '" required onclick="cambiarPrecio(this.id)" onchange="actualizarPrecio(this.id)" readonly></td>\n\
                    <td><input required type="text" class="caja_azul" size="6" value="" id="txtreq_' + (cuenta) + '" name="txtreq_' + (cuenta) + '" onkeyup="calcPrecioNuevo(this.id)" readonly></td>\n\
                    <td class="texto9">$ <input type="text" class="caja2c" size="11" value="" id="txtprectotal_' + (cuenta) + '" name="txtprectotal_' + (cuenta) + '" readonly></td>\n\
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
                    $("#contart").val(rowCount - 17);
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
                            $("#contart").val(rowCount - 17);
                        }
                    } else {
                        return false;
                    }
                }
            }
        </script>
        <script type="text/javascript"> //consultar datos y autollenar campos del codigo
            function buscarCod(id, i) {
                var valor = $("#" + id).val(); //sacar el valor del input del codigo
                if (valor !== "") {
                    $.ajax({
                        url: "../herramientas/buscaCod.php",
                        type: "POST",
                        dataType: "json",
                        data: {val: valor},
                        success: function (res) {//objeto que trae los parametros json elegidos
                            if (res.glosa === null) {
                                var msj = confirm("Error...\nEl codigo ingresado: " + valor + " , NO se encuentra en Sistema. ¿Desea mantener los datos para la fila actual?");
                                if (msj) {
                                    $("#txtcod_" + i).val("");
                                    $("#txtcod_" + i).css("background", "red");
                                } else {
                                    $("#txtcod_" + i).val("");
                                    $("#txtcm_" + i).val("");
                                    $("#txtglosas_" + i).val("");
                                    $("#txtglosa_" + i).val("");
                                    $("#txtum_" + i).val("");
                                    $("#txtprec_" + i).val("");
                                    $("#txtprectotal_" + i).val("");
                                    $("#txtreq_" + i).val("");
                                }
                            } else {
                                $("#txtcm_" + i).val(res.cm); //asignar los valores a los input elegidos dinamicos
                                $("#txtglosas_" + i).val(res.glosa);
                                $("#txtum_" + i).val(res.um);
                                $("#txtcod_" + i).attr("readonly", true);
                                $("#txtprec_" + i).attr("readonly", false);
                                $("#txtreq_" + i).attr("readonly", false);
                                $("#flag").val("1");
                                $("#agregar").addClass("boton1a");
                                $("#txtprec_" + i).focus();
                                $("#txtcod_" + i).css("background", "");
                            }
                        }
                    });
                }
            }
        </script>
        <script type="text/javascript"> //consultar datos y autollenar campos del rutprov
            $(document).ready(function () {
                $("#rutpv").on("blur", function () {//activar funcion al sacar el foco del input del codigo
                    var valor = $(this).val(); //sacar el valor del input del codigo
                    if (valor !== "") {
                        $.ajax({
                            url: "../herramientas/buscaProv.php",
                            type: "POST",
                            dataType: "json",
                            data: {val: valor},
                            success: function (res1) {//objeto que trae los parametros json elegidos
                                if (res1.nombre === null) {
                                    $("#rutpv").val("");
                                    $("#dv").val("");
                                    $("#nompv").val("");
                                    $("#nomcont").val("");
                                    $("#provdir").val("");
                                    alert("Error...\nEl RUT ingresado: '" + valor + "' , no existe en el sistema, se debe crear antes de Grabar la OC");
                                    $("#provfic").fadeIn(1000);
                                    var rut = $("#rutpv2").val();
                                    var dv = $("#dvpv2").val();
                                    var nom = $("#nompv2").val();
                                    var giro = $("#giropv2").val();
                                    var cont = $("#contpv2").val();
                                    var mail = $("#mailpv2").val();
                                    var fono = $("#fonopv2").val();
                                    var dir = $("#dirpv2").val();
                                    $('#linkprov').prop('href', '../edicion/modificarProveedor.php?option=3&rut=' + rut + "&nombre=" + nom + "&dv=" + dv + "&giro=" + giro + "&cont=" + cont + "&mail=" + mail + "&fono=" + fono + "&dir=" + dir);
                                } else {
                                    $("#dv").val(res1.dv); //asignar los valores a los input elegidos dinamicos
                                    $("#nompv").val(res1.nombre);
                                    $("#nomcont").val(res1.contacto);
                                    $("#provdir").val(res1.dir);
                                    $("#rutpv").attr("readonly", true);
                                }
                            }
                        });
                    }
                });
            });
        </script>
        <script type="text/javascript">
            $(document).change(function () {
                var z = ($("#contador").val()) - 1;
                $("body").on("change", "#txtreq_" + z, function (event) {//activar funcion al sacar el foco del input del codigo
                    event.preventDefault();//para que funcione el selector de jquery con variables dinamicas
                    var cant = $(this).val();
                    if (cant == 0 || isNaN(cant)) {
                        $("#txtprec_" + z).val($("#txtprectotal_" + z).val());
                    } else {
                        var precio = $("#txtprec_" + z).val();
                        var precioant = $("#txtprectotal_" + z).val();
                        var suma = 0;
                        var sumatotal = 0;
                        var sumafinal = 0;
                        if (precioant === "") {
                            suma = Math.trunc(precio * cant);
                            $("#txtprectotal_" + z).val(suma);
                            sumatotal = parseInt($("#txttotalneto").val());
                            $("#txttotalneto").val(sumatotal + suma);
                            $("#txtiva").val(Math.round((sumatotal + suma) * 0.19));
                            $("#txttotalfinal").val(Math.round((sumatotal + suma) * 1.19));
                        } else {
                            sumatotal = parseInt($("#txttotalneto").val());
                            sumatotal = (sumatotal - precioant);
                            suma = Math.trunc(precio * cant);
                            sumafinal = sumatotal + suma;
                            $("#txtprectotal_" + z).val(suma);
                            $("#txttotalneto").val(sumafinal);
                            $("#txtiva").val(Math.round(sumafinal * 0.19));
                            $("#txttotalfinal").val(Math.round(sumafinal * 1.19));
                        }
                    }
                });
            });
        </script>
        <script type="text/javascript"> //consultar datos y autollenar campos del codigo
            $(document).ready(function () {
                $("#txtoc").on("change", function () {//activar funcion al sacar el foco del input del codigo
                    var valor = $(this).val().replace(/\s/g, ""); //sacar el valor del input del codigo
                    var indices = [];
                    for (var i = 0; i < valor.length; i++) {
                        if (valor[i].toLowerCase() === "-")
                            indices.push(i);//contar las veces que se repite el guion
                    }
                    if (indices.length < 2) {
                        alert("Error:\nLa Orden de compra ingresada no corresponde...");
                        $("#txtoc").val("");
                    } else {
                        $("#txtoc").val(valor.toUpperCase());
                        if (valor !== "") { //sacar el valor del input del codigo
                            $.ajax({
                                url: "../herramientas/buscaOC.php",
                                type: "POST",
                                dataType: "json",
                                data: {oc: valor},
                                success: function (res) {//objeto que trae los parametros json elegidos
                                    if (res.oc !== null) {
                                        alert("Error...\nLa orden de compra ingresada: " + valor + " , ya existe en el sistema, no se puede crear.");
                                        $("#txtoc").val("");
                                        $('#ticket').hide();
                                    } else {
                                        $('#ticket').show();
                                        $("#dwnl").fadeIn(1000);
                                    }
                                }
                            });
                        }
                    }
                });
            });
        </script>
        <script type="text/javascript"> //consultar datos y autollenar campos del codigo
            $(document).ready(function () {
                $("#dwnl").click(function () {
                    var valor = $("#txtoc").val(); //sacar el valor del input del codigo
                    if (valor !== "") {
                        $.ajax({
                            url: "../herramientas/buscaOCMP.php",
                            type: "POST",
                            dataType: "json",
                            data: {oc: valor},
                            beforeSend: function () {//imagen de carga                            
                                $.blockUI({
                                    message: "<p class='centrar_cuadro_alertas'>Descargando de Mercado Público, favor espere...<img src='../img/clock.gif' width='140' height='140' /></p>"
                                });
                            },
                            error: function () {
                                alert("Error: No se ha podido Conectar, Favor intente más tarde.");
                                $("#resultado").append("<center><br><span class='texto6'>Error con la descarga de datos, favor intente mas tarde.</span></center>");
                                $.unblockUI();
                            },
                            success: function (res) {//objeto que trae los parametros json elegidos
                                //var num = res["Cantidad"];
                                if (res == null) {
                                    alert("Error, la OC " + valor + " no se ha encontrado, o es inválida");
                                    $("#txtoc").val("");
                                    $('#dwnl').hide();
                                    $('#ticket').hide();
                                    $.unblockUI();
                                } else {
                                    var org = res["Listado"][0]["Comprador"]["CodigoOrganismo"];
                                    if (org != "<?= $org ?>") {
                                        var alerta = confirm("La Orden de compra " + valor + " No corresponde al Establecimiento actual, ¿Desea Continuar?");
                                        if (!alerta) {
                                            location.href="crearOC.php";
                                        }
                                    }
                                    var estado = res["Listado"][0]["CodigoEstado"];
                                    if (estado == "9") {
                                        var alerta1 = confirm("La Orden de compra " + valor + " Se encuentra Cancelada en el portal, ¿Desea Continuar?");
                                        if (!alerta1) {
                                            location.href="crearOC.php";
                                        }
                                    }
                                    var rut = res["Listado"][0]["Proveedor"]["RutSucursal"].replaceAll(".", "").slice(0, -2);
                                    var dv = res["Listado"][0]["Proveedor"]["RutSucursal"].slice(-1);
                                    var estnom = res["Listado"][0]["Comprador"]["NombreOrganismo"];
                                    var est = "Estado OC: " + res["Listado"][0]["Estado"] + ", (Tipo Moneda: " + res["Listado"][0]["TipoMoneda"] + "), Establecimiento: " + org + ": (" + estnom + ")";
                                    var fecha = res["Listado"][0]["Fechas"]["FechaCreacion"].replaceAll("T", "").slice(0, 10);
                                    var lic = res["Listado"][0]["CodigoLicitacion"];
									$("#txtlic").val(lic);
									$("#rutpv").val(rut);
                                    $("#rutpv2").val(rut);
									$("#estc").val(estado);
                                    $("#nompv2").val(res["Listado"][0]["Proveedor"]["Nombre"].replace(/ /g, "_"));
                                    $("#giropv2").val(res["Listado"][0]["Proveedor"]["Actividad"].replace(/ /g, "_"));
                                    $("#contpv2").val(res["Listado"][0]["Proveedor"]["NombreContacto"].replace(/ /g, "_"));
                                    $("#mailpv2").val(res["Listado"][0]["Proveedor"]["MailContacto"].trim());
                                    $("#fonopv2").val(res["Listado"][0]["Proveedor"]["FonoContacto"].trim());
                                    $("#dirpv2").val(res["Listado"][0]["Proveedor"]["Direccion"].replace(/ /g, "_"));
                                    $("#dvpv2").val(dv);
                                    $("#glosa").val(res["Listado"][0]["Nombre"]);
                                    $("#est").text(est);
                                    $("#est").show();
                                    $("#rutpv").blur();
                                    $("#fecha").val(fecha);
                                    $("#agregar").click();
                                    var item = res["Listado"][0]["Items"]["Cantidad"];
                                    var i;
                                    for (i = 0; i < item; i++) {
                                        var glosa = res["Listado"][0]["Items"]["Listado"][i]["EspecificacionComprador"];
                                        if (glosa == "") {
                                            glosa = res["Listado"][0]["Items"]["Listado"][i]["EspecificacionProveedor"];
                                        }
                                        var glosan = glosa.replace(/[`~!@#$%^&*()_|+\=?;:'",.<>\{\}\[\]\\\/ ]/gi, '').trim();
                                        var cod = glosan.slice(0, 8).trim();
                                        if (cod.includes("-")) {
                                            $("#txtcod_" + i).val(cod);
                                        } else {
                                            var pos = glosan.indexOf('-') - 3;
                                            cod = glosan.slice(pos);
                                            if (cod.includes("-")) {
                                                $("#txtcod_" + i).val(cod);
                                            } else {
                                                $("#txtcod_" + i).val("");
                                            }
                                        }
                                        $("#txtglosa_" + i).val(glosa);
                                        $("#txtprec_" + i).val(res["Listado"][0]["Items"]["Listado"][i]["PrecioNeto"]);
                                        $("#txtreq_" + i).val(res["Listado"][0]["Items"]["Listado"][i]["Cantidad"]);
                                        $("#txtprectotal_" + i).val(res["Listado"][0]["Items"]["Listado"][i]["Total"]);
                                        $("#flag").val("1");
                                        if (i < item - 1) {
                                            $("#agregar").click();
                                        } else {
                                            $("#flag").val("0");
                                            $('#dwnl').hide();
                                            $("#txttotalneto").val(res["Listado"][0]["TotalNeto"]);
                                            $("#txtiva").val(res["Listado"][0]["Impuestos"]);
                                            $("#txttotalfinal").val(res["Listado"][0]["Total"]);
                                            $("#txtoc").attr("readonly", true);
                                            $("#glosa").focus();
                                            $.unblockUI();
                                        }
                                    }
                                }
                            }
                        });
                    }
                });
            });
        </script>
        <title>SigBod - Crear Orden de Compra</title>
    </head>
    <body class="fondo" onload="<?= $dir ?>">
        <?php
        $fecha = date("Y-m-d");
        ?>
        <form id="form1" name="form1" method="post" autocomplete="off">
            <table class="table1" id="tablasaldo">
                <tr>
                    <td colspan="9" class="texto"><img class="imgico" alt='crearoc' title='Crear OC' src='../img/oc.png'>&nbsp;&nbsp;&nbsp;CREAR ORDEN DE COMPRA &nbsp;<img class="imgnormal" src="../img/check.png" id="ticket1" title="Guardado OK"></td>
                </tr>
                <tr>
                    <td colspan="3" class="texto8">N° MERCADO PUBLICO:</td>                    
                    <td colspan="6" class="texto9"><input type="text" class="caja" id="txtoc" name="txtoc" size="16" value="<?= $oc ?>" required>&nbsp;&nbsp;<img class="imgnormal" src="../img/check.png" id="ticket" title="La OC ingresada esta disponible">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img class="imgnormal" src="../img/download.png" id="dwnl" title="descargar OC desde MP">&nbsp;&nbsp;<span id="est" class="texto11"></span></td>
                </tr>
				<tr>
                    <td colspan="3" class="texto8">N° LICITACIÓN:</td>                    
                    <td colspan="6" class="texto9"><input type="text" class="caja" id="txtlic" name="txtlic" size="16" value="">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="parc" id="parc"/> Parcializada</td>
                </tr>
                <tr>
                    <td colspan="3" class="texto8">RUT PROVEEDOR:</td>                    
                    <td colspan="6" class="texto9"><input type="text" class="caja_negrita" id="rutpv" name="rutpv" size="9" maxlength="8" value="" required>&nbsp;<b>-</b>&nbsp;<input type="text" class="caja_negrita" id="dv" name="dv" size="1" maxlength="1" value="" readonly>&nbsp;&nbsp;&nbsp;<img class="imgnormal" id="imgbp" alt="buscar" title="buscar" src="../img/lupan.png" onclick="buscarProv()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="" id="linkprov" target='_blank' onclick="window.open(this.href, this.target, 'height=560, width=800 left=200 top=20');return false;"><img class="imgnormal" src="../img/prov.png" id="provfic" title="Ver y Crear Ficha Proveedor"></a></td>
                </tr>
                <tr>
                    <td colspan="3" class="texto8">NOMBRE PROVEEDOR:</td>                    
                    <td colspan="6" class="texto9"><input type="text" class="caja2b" id="nompv" name="nompv" size="100" value="" readonly></td>
                </tr>
                <tr>
                    <td colspan="3" class="texto8">CONTACTO:</td>                    
                    <td colspan="6" class="texto9"><input type="text" class="caja2b" id="nomcont" name="nomcont" size="100" value="" readonly></td>
                </tr>
                <tr>
                    <td colspan="3" class="texto8">DIRECCIÓN:</td>                    
                    <td colspan="6" class="texto9"><input type="text" class="caja2b" id="provdir" name="provdir" size="100" value="" readonly></td>
                </tr>
                <tr>
                    <td colspan="3" class="texto8">FECHA CREACIÓN:</td>                    
                    <td colspan="6" class="texto9"><input type="date" class='caja2b' name="fecha" id="fecha" step="1" min="" max="<?= $fecha ?>" value="<?= $fecha ?>"/></td>
                </tr>                
                <tr>
                    <td colspan="3" class="texto8">OBSERVACIONES:</td>                    
                    <td colspan="6" class="texto9"><textarea name="txtcom" id="glosa" class="caja_negrita" cols="110" rows="2"></textarea></td>
                </tr>
                <tr>
                    <td colspan="3" class="texto8">N° ARTÍCULOS:</td>                    
                    <td colspan="6" class="texto9"><input type="text" class="caja2b" id="contart" name="contart" size="2" value="0" readonly></td>
                </tr>
                <tr>
                    <td colspan="9" class="texto8c"></td>
                </tr>
                <tr>
                    <th>N°</th>
                    <th width="150">Código Interno</th>
                    <th width="120">Código CM</th>
                    <th width="500">Glosa</th>
                    <th width="80">Unidad de Medida</th>
                    <th width="120">Precio Unitario</th>
                    <th width="80">Cantidad Requerida</th>
                    <th width="150">Total Unitario Neto</th>
                    <th width="50">Acción</th>
                </tr>
                <tr>
                    <td colspan="9" class="texto8c"><button type="button" class="boton1a" id="agregar" onclick="agregarFila()"><b>&nbsp;+&nbsp;</b></button></td>
                </tr>
                <tr>
                    <td colspan="7" class="texto8">Neto:</td>                    
                    <td colspan="2" class="texto9"><b>$ </b><input type="text" class="caja2b" id="txttotalneto" name="txttotalneto" value="0" size="17" readonly>
                        <input type="hidden" name="contador" id="contador" value="0">
                        <input type="hidden" name="flag" id="flag" value="1">
                        <input type="hidden" name="id" id="id" value="">
                        <input type="hidden" name="" id="rutpv2" value="">
                        <input type="hidden" name="" id="nompv2" value="">
                        <input type="hidden" name="" id="dvpv2" value="">
                        <input type="hidden" name="" id="giropv2" value="">
                        <input type="hidden" name="" id="mailpv2" value="">
                        <input type="hidden" name="" id="contpv2" value="">
                        <input type="hidden" name="" id="fonopv2" value="">
                        <input type="hidden" name="" id="dirpv2" value="">
						<input type="hidden" name="estc" id="estc" value="">
                    </td>
                </tr>
                <tr>
                    <td colspan="7" class="texto8">IVA (19%):</td>                    
                    <td colspan="2" class="texto9"><b>$ </b><input type="text" class="caja2b" id="txtiva" name="txtiva" value="0" size="17" readonly>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" class="texto8">TOTAL FINAL:</td>                    
                    <td colspan="2" class="texto9"><b>$ </b><input type="text" class="caja2c" id="txttotalfinal" name="txttotalfinal" value="0" size="16" readonly>
                    </td>
                </tr>				
                <tr>
                    <td colspan="9" class="texto5">
                        <button type="submit" class="botonnormal">Grabar&nbsp;&nbsp;<img class='img' alt='grabar' title='Grabar en sistema' src='../img/save.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;
                        <button class="botonnormal" type="button" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;
                        <?= $btn ?>&nbsp;&nbsp;&nbsp;&nbsp;
                        <button class="botonnormal" type="button" onclick="window.location = 'crearOc.php'">Limpiar&nbsp;&nbsp;<img class='img' alt='modificar' title='limpiar formulario' src='../img/clean.png'></button></td>
                </tr>
            </table>
        </form>
        <div id="resultado"></div>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>