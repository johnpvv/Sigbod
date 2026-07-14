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
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>
        <?php
        include_once("../include/conn.php");
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../index.php');
            exit();
        }
        $link = Conectarse(); //Variable de coneccion
        $date = date("Y-m-d");
        $data = MySQL_query("SELECT MAX(doc_carga_id) AS max FROM documento", $link) or die("<script>alert('Ha Ocurrido un error con obtener la cabecera del Grupo a Insertar...');</script>");
        $row = mysql_fetch_array($data);
        $idins = $row["max"] + 1; //obtener el id maximo para crear un grupo de registros
        mysql_close($link);
        ?>        
        <title>SigBod - Ingreso de Documentos</title>
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
                                    alert("Error...\nEl RUT ingresado: '" + valor + "' , NO Existe");
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
        <script type="text/javascript"> //consultar datos y autollenar campos del rutprov
            function buscarSubDuplicado(x, i) {
                var clave = document.getElementById("rutpv").value;
                var valor = clave + x;
                $(document).ready(function () {
                    var cont = 0;
                    $(".caja_num_subdoc").each(function () {
                        if ($(this).val() == x) {
                            cont++;
                        }
                    });
                    if (cont > 1) {
                        alert("El Numero Ingresado: " + x + " Ya esta En la Lista de Subdocumentos, favor revisar.");
                        document.getElementById(i).value = "";
                        document.getElementById(i).focus();
                    }
                });
                if (x !== "") {
                    $.ajax({
                        url: "../herramientas/buscaDoc.php",
                        type: "POST",
                        dataType: "json",
                        data: {val: valor},
                        success: function (res) {//objeto que trae los parametros json elegidos
                            if (res.numdoc !== null) {
                                alert("Error...\nEl documento Ingresado: '" + x + "' , Ya existe en el sistema con el Folio N°: " + res.id + ", Proveedor: " + res.prov);
                                document.getElementById(i).value = "";
                            }
                        }
                    });
                }
            }
        </script>

        <script type="text/javascript"> //consultar datos y autollenar campos del rutprov
            function buscarDocDuplicado(x, i) {
                var clave = document.getElementById("rutpv").value;
                var valor = clave + x;
                $(document).ready(function () {
                    var cont = 0;
                    var fila = 0;
                    var fila1 = 0;
                    $(".caja_texto_numdoc").each(function () {
                        if ($(this).val() == x) {
                            cont++;
                            if (cont === 1) {
                                fila1 = fila + cont;
                            }
                        }
                        fila++;
                    });

                    if (cont > 1) {
                        alert("El Numero Ingresado: " + x + " Ya esta En la Lista de Documentos, favor revisar. (ya cargado en la fila: " + fila1 + ")");
                        document.getElementById(i).value = "";
                        document.getElementById(i).focus();
                    }
                });
                if (x !== "") {
                    $.ajax({
                        url: "../herramientas/buscaDoc.php",
                        type: "POST",
                        dataType: "json",
                        data: {val: valor},
                        success: function (res) {//objeto que trae los parametros json elegidos
                            if (res.numdoc !== null) {
                                alert("Error...\nEl documento Ingresado: '" + x + "' , Ya existe en el sistema con el Folio N°: " + res.id + ", Proveedor: " + res.prov);
                                document.getElementById(i).value = "";
                            }
                        }
                    });
                }
            }
        </script>
        <script type="text/javascript">
            function buscarProv() {
                window.open('../reportes/listarBuscaProv.php', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=720,height=480 left=300,top=50');
            }
        </script>
        <script type="text/javascript">
            function agregarSubFila(i) {
                var idx = i.replace("agrega_", "tabla_"); //para crear id de la tabla a la cual hay que crear subdocumento
                var idz = i.replace("agrega_", "elimina_");//para crear id boton borrar con el numero de la tabla correcta
                var idtab = i.replace("agrega_", "tabfila_");//para crear id contador de subdocumentos de la tabla correcta
                var ntab = i.replace("agrega_", "_");
                var btn = i.replace("agrega_", "");
                var subcuenta = parseInt($("#subcuenta").val());
                var cuentatotal = parseInt($("#subcuentatotal").val());
                var table = document.getElementById(idx);
                var cont = table.rows.length;
                //alert(cont);
                table.insertRow(cont).innerHTML =
                        '<tr><td class="texto7b"><input type="text" class="textoredondo" size="1" value="' + (cont - 1) + '" readonly>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tipo de SubDocumento:&nbsp;&nbsp;' +
                        '<select name="subtipo_' + cuentatotal + ntab + '" class="caja_neg_ssombra" id="subtipo_' + cuentatotal + ntab + '" required onchange="mostrarCopia(this.id, this.value)">' +
                        '<option value="" selected>‌--Elija Opcion--</option>' +
                        '<option value="Nota de Credito">Nota de Credito</option>' +
                        '<option value="Guía de Despacho">‌Guía de Despacho</option>' +
                        '<option value="Guía Manual">‌Guía Manual</option>' +
                        '<option value="Recep. de Bodega">Recepcion de Bodega</option>' +
                        '<option value="Devolucion">Devol. o Retiro</option>' +
                        '</select>&nbsp;&nbsp;&nbsp;' +
                        'N° Documento:&nbsp;&nbsp;' +
                        '<input type="text" name="subndoc_' + cuentatotal + ntab + '" id="subndoc_' + cuentatotal + ntab + '" class="caja_num_subdoc" size="10" required onChange="buscarSubDuplicado(this.value,this.id)">&nbsp;&nbsp;&nbsp;' +
                        'Fecha Doc:&nbsp;&nbsp;' +
                        '<input type="date" name="subfdoc_' + cuentatotal + ntab + '" id="subfdoc_' + cuentatotal + ntab + '" class="caja_neg_ssombra" max="<?=$date?>" required>&nbsp;&nbsp;' +
                        '<img class="imgnormal" src="../img/datepicker.png" title="Seleccionar fecha" alt="Seleccionar fecha" />&nbsp;&nbsp;&nbsp;' +
                        'Monto Total:&nbsp;&nbsp;' +
                        '<input type="text" name="submonto_' + cuentatotal + ntab + '" id="submonto_' + cuentatotal + ntab + '" class="caja_neg_ssombra" size="10" required>&nbsp;' +
                        '<button type="button" class="boton1c" id="copiaprec_' + cuentatotal + ntab + '" title="Copiar precio del Doc. Principal" onclick="copiaPrecio(' + btn + ', this.id)" style="display:none"><b>&nbsp;↓&nbsp;</b></button>&nbsp;&nbsp;&nbsp;&nbsp;' +
                        '&nbsp;<button type="button" class="boton1" id="' + idz + '" title="Eliminar" onclick="eliminarSubFila(this.parentNode.parentNode.rowIndex,this.id)"><b>&nbsp;-&nbsp;</b></button>' +
                        '<input type="hidden" name="' + idtab + '" id="' + idtab + '" value="' + (cont - 1) + '"></td></tr>';
                document.getElementById("subcuentatotal").value = cuentatotal + 1;
                document.getElementById("subcuenta").value = subcuenta + 1;
            }
        </script>
        <script type="text/javascript">
            function agregarFila() {
                var table = document.getElementById("tabla");
                var cont = table.rows.length;
                var cuenta = parseInt($("#contador").val());
                var cuentatotal = parseInt($("#contadortotal").val());
                $("#contadortotal").val(cuentatotal + 1);
                $("#contador").val(cuenta + 1);
                var id = "tabla_" + cuentatotal;
                document.getElementById("tabla").insertRow(cont - 2).innerHTML =
                        '<tr><td class="texto5c">' +
                        '<button type="button" class="boton1" id="Eliminar" title="Eliminar Fila" onclick="eliminarFila(this.parentNode.parentNode.rowIndex)">-&nbsp;<b>' + (cuentatotal + 1) + '</b>&nbsp;-</button>' +
                        '<center><table id="' + id + '" class="table2d">' +
                        '<tr><td><span class="caja_pading1">Tipo de Doc.:&nbsp;&nbsp;' +
                        '<select name="tipo_' + cuentatotal + '" class="caja_texto" id="tipo_' + cuentatotal + '" required>' +
                        '<option value="" selected>--Elija Opcion‌--</option>' +
                        '<option value="Boleta">‌Boleta</option>' +
                        '<option value="Factura">‌Factura</option>' +
                        '<option value="Guía de Despacho">‌Guía de Despacho</option>' +
                        '<option value="Nota de Credito">Nota de Credito</option>' +
                        '</select>&nbsp;&nbsp;' +
                        'N° Documento:&nbsp;' +
                        '<input type="text" name="nfact_' + cuentatotal + '" id="nfact_' + cuentatotal + '" class="caja_texto_numdoc" size="8" required onChange="buscarDocDuplicado(this.value,this.id)">&nbsp;&nbsp;&nbsp;' +
                        'Fecha Doc.:&nbsp;' +
                        '<input type="date" name="ffact_' + cuentatotal + '" id="ffact_' + cuentatotal + '" class="caja_texto" max="<?=$date?>" required>&nbsp;&nbsp;' +
                        '<img class="imgnormal" src="../img/datepicker.png" title="Seleccionar fecha" alt="Seleccionar fecha"/>&nbsp;&nbsp;<button type="button" class="boton1c" id="copiafech_' + cuentatotal + '" title="Copiar del doc. anterior FECHA" onclick="copiaFecha(' + (cuentatotal - 1) + ')"><b>&nbsp;↓&nbsp;</b></button>' +
                        '&nbsp;&nbsp;URL:&nbsp;<input type="text" name="url_' + cuentatotal + '" id="url_' + cuentatotal + '" class="caja_texto" size="3">'+
                        '</span>&nbsp;' +
                        'O.C.:&nbsp;' +
                        '<input type="text" name="noc_' + cuentatotal + '" id="noc_' + cuentatotal + '" class="caja_neg_ssombra" size="16" placeholder="Escriba OC" required onChange="validaOC(this.id)">' +
                        '&nbsp;<button type="button" class="boton1c" id="copiaoc_' + cuentatotal + '" title="Copiar del doc. anterior orden de compra" onclick="copiaOc(' + (cuentatotal - 1) + ')"><b>&nbsp;↓&nbsp;</b></button>&nbsp;&nbsp;' +
                        'Monto $:&nbsp;' +
                        '<input type="text" name="monto_' + cuentatotal + '" id="monto_' + cuentatotal + '" class="caja_neg_ssombra" size="12" placeholder="Monto con IVA" required>' +
			'&nbsp;<button type="button" class="boton1c" id="copiaMontoF_' + cuentatotal + '" title="Copiar del doc. anterior Monto" onclick="copiaMontoF(' + (cuentatotal - 1) +', this.id)"><b>&nbsp;↓&nbsp;</b></button>&nbsp;&nbsp;&nbsp;' +
                        '&nbsp;<button type="button" class="boton1a" id="agrega_' + cuentatotal + '" title="Agregar Subdocumento" onclick="agregarSubFila(this.id)"><b>&nbsp;+&nbsp;</b></button>' +
                        '</td></tr><tr><td>' +
                        '<span class="caja_pading1">' +
                        'Origen Doc.:&nbsp;' +
                        '<select name="origen_' + cuentatotal + '" class="caja_texto" id="origen_' + cuentatotal + '" onChange="cambiarTipo(this.id)">' +
                        '<option value="" selected>--‌Elija Opcion--</option>' +
                        '<option value="Memo">‌Memo</option>' +
                        '<option value="Providencia">Providencia</option>' +
                        '<option value="Correo">‌Correo</option>' +
                        '<option value="Manual">Manual</option>' +
                        '</select>&nbsp;&nbsp;' +
                        'N°:&nbsp;' +
                        '<input type="text" name="norig_' + cuentatotal + '" id="norig_' + cuentatotal + '" class="caja_texto" size="4">&nbsp;&nbsp;' +
                        'Fecha:&nbsp;' +
                        '<input type="date" name="forig_' + cuentatotal + '" id="forig_' + cuentatotal + '" class="caja_texto" max="<?=$date?>">&nbsp;&nbsp;' +
                        '<img class="imgnormal" src="../img/datepicker.png" title="Seleccionar fecha" alt="Seleccionar fecha"/>&nbsp;' +
                        '</span>&nbsp;<button type="button" class="boton1c" id="copia_' + cuentatotal + '" title="Copiar del doc. anterior origen, observacion" onclick="copiaDato(' + (cuentatotal - 1) + ')"><b>&nbsp;↓&nbsp;</b></button>&nbsp;&nbsp;' +
                        'Observaciones:&nbsp;' +
                        '<textarea name="obs_' + cuentatotal + '" id="obs_' + cuentatotal + '" class="bajo" cols="80" rows="2"></textarea>' +
                        '</td></tr></table></center>' +
                        '</td></tr>';
                $("html, body").animate({scrollTop: $(document).height()}, "slow");//bajar el scroll al final de la pagina
            }
        </script>
        <script type="text/javascript">
            function eliminarFila(i) {
                var valor = document.getElementById("contador").value;
                var table = document.getElementById("tabla");
                if (valor == 1) {
                    alert("ERROR, NO PUEDE BORRAR LA ULTIMA FILA");
                } else {
                    table.deleteRow(i);
                    $("#contador").val(valor - 1);
                }
            }
        </script>
        <script type="text/javascript">
            function eliminarSubFila(i, x) {
                var idx = x.replace("elimina_", "tabla_");
                var table = document.getElementById(idx);
                table.deleteRow(i);
            }
        </script>
        <script type="text/javascript">
            function copiaDato(x) {
                var y = x + 1;
                var origen = document.getElementById("origen_" + x).value;
                var norig = document.getElementById("norig_" + x).value;
                var forig = document.getElementById("forig_" + x).value;
                var obs = document.getElementById("obs_" + x).value;
                $("#origen_" + y).val(origen);
                $("#norig_" + y).val(norig);
                $("#forig_" + y).val(forig);
                $("#obs_" + y).val(obs);
                alert("Datos de Origen y Obs. Copiados");
            }
        </script>
        <script type="text/javascript">
            function copiaOc(x) {
                var y = x + 1;
                var origen = document.getElementById("noc_" + x).value;
                $("#noc_" + y).val(origen);
                alert("Datos de OC Copiados");
            }
        </script>
        <script type="text/javascript">
            function copiaFecha(x) {
                var y = x + 1;
                var origen = document.getElementById("ffact_" + x).value;
                $("#ffact_" + y).val(origen);
                alert("Datos de Fecha copiados");
            }
        </script>
        <script type="text/javascript">
            function mostrarCopia(x, y) {//x= id del campo, y= valor del campo
                var idprec = x.replace("subtipo_", "copiaprec_");
                var submnt = x.replace("subtipo_", "submonto_");
                if (y == "Recep. de Bodega") {
                    document.getElementById(idprec).style.display = 'inline';
                    document.getElementById(submnt).style.color = '#000000';
                } else if (y == "Nota de Credito") {
                    document.getElementById(idprec).style.display = 'inline';
                    document.getElementById(submnt).style.color = '#FF0000';
                } else {
                    document.getElementById(idprec).style.display = 'none';
                }
            }
        </script>
        <script type="text/javascript">
            function copiaPrecio(x, y) {
                var origen = document.getElementById("monto_" + x).value;
                if (origen != "") {
                    var id = y.replace("copiaprec_", "submonto_");
                    $("#" + id).val(origen);
                    alert("Precio Copiado");
                } else {
                    alert("Se ha producido un error, favor revise los datos e intente nuevamente");
                }
            }
        </script>
        <script type="text/javascript">
            function cambiarTipo(id) {
                var origen = document.getElementById(id).value;
                var norigen = id.replace("origen_", "norig_");
                if (origen == "Correo" || origen == "Manual") {
                    document.getElementById(norigen).style.display = 'none';
                } else {
                    document.getElementById(norigen).style.display = 'inline';
                }
            }
        </script>
        <script type="text/javascript">
            function validaOC(y) {
                var valor = document.getElementById(y).value.replace(/\s/g, "");
                var indices = [];
                for (var i = 0; i < valor.length; i++) {
                    if (valor[i].toLowerCase() === "-")
                        indices.push(i);//contar las veces que se repite el guion
                }
                if (indices.length < 2) {
                    alert("Error:\nLa Orden de compra ingresada no corresponde...");
                    $("#" + y).val("");
                } else {
                    $("#" + y).val(valor.toUpperCase());
                }
            }
        </script>
		<script type="text/javascript">
            function copiaMontoF(x, y) {
                var origen = document.getElementById("monto_" + x).value;
                if (origen != "") {
                    var id = y.replace("copiaMontoF_", "monto_");
                    $("#" + id).val(origen);
                    alert("Precio Copiado");
                } else {
                    alert("Se ha producido un error, favor revise los datos e intente nuevamente");
                }
            }
        </script>
    </head>
    <body class="fondo">
        <form id="frm" name="frm" action="cargaDocumentoAjax.php" method="post" onKeypress="if (event.keyCode == 13) event.returnValue = false;">
            <table class="table2a" id="tabla">
                <tr>
                    <td class="texto">CARGA DE DOCUMENTOS</td>
                </tr>
                <tr>
                    <td class="texto5a">Digite RUT Proveedor:
                        <input type="text" name="rutpv" id="rutpv" class="caja_color" value="" autofocus size="9"  maxlength="8" required> - <input type="text" name="dv" id="dv" class="caja_color" value="" size="1"  maxlength="1" readonly />
                        &nbsp;&nbsp;&nbsp;<img class="imgnormal" id="imgbp" alt="buscar" title="buscar" src="../img/lupan.png" onclick="buscarProv()">
                        &nbsp;&nbsp;&nbsp;
                        <span class="texto5a">Nombre Proveedor:</span>
                        <input type="text" name="nompv" id="nompv" class="caja_negrita_desab" size="60" readonly >
                </tr>
                <tr>
                    <td class="texto5c">
                        <button type="button" class="boton1" id="Eliminar" title="Eliminar Fila" onclick="eliminarFila(this.parentNode.parentNode.rowIndex)">-&nbsp;<b>1</b>&nbsp;-</button>
                <center><table id="tabla_0" class="table2d">
                        <tr>
                            <td>
                                <span class="caja_pading1">Tipo de Doc.:&nbsp;
                                    <select name="tipo_0" class="caja_texto" id="tipo_0" required>
                                        <option value='' selected>‌--Elija Opcion--</option>
                                        <option value='Boleta'>‌Boleta</option>
                                        <option value='Factura'>‌Factura</option>
                                        <option value='Guía de Despacho'>‌Guía de Despacho</option>
                                        <option value="Nota de Credito">Nota de Credito</option>
                                    </select>&nbsp;&nbsp;                       
                                    N° Documento:&nbsp;
                                    <input type="text" name="nfact_0" id="nfact_0" class="caja_texto_numdoc" size="8" required onChange="buscarDocDuplicado(this.value, this.id)">&nbsp;&nbsp;
                                    Fecha Doc.:&nbsp;
                                    <input type="date" name="ffact_0" id="ffact_0" class="caja_texto" max="<?=$date?>" required>&nbsp;
                                    <img class="imgnormal" src='../img/datepicker.png' title="Seleccionar fecha" alt="Seleccionar fecha"/>&nbsp;&nbsp;
                                    URL:&nbsp;<input type="text" name="url_0" id="url_0" class="caja_texto" size="3">
                                </span>&nbsp;&nbsp;
                                O.C.:&nbsp;
                                <input type="text" name="noc_0" id="noc_0" class="caja_neg_ssombra" size="16" placeholder="Escriba OC" required onChange="validaOC(this.id)">&nbsp;
                                Monto $:&nbsp;
                                <input type="text" name="monto_0" id="monto_0" class="caja_neg_ssombra" size="12" placeholder="Monto con IVA" required>&nbsp;&nbsp;&nbsp;
                                <button type="button" class="boton1a" id="agrega_0" onclick="agregarSubFila(this.id)" title="Agregar Subdocumento"><b>&nbsp;+&nbsp;</b></button>
                            </td>
                        </tr>
                        <tr>                                
                            <td>
                                <span class="caja_pading1">
                                    Origen Doc.:
                                    <select name="origen_0" class="caja_texto" id="origen_0" onChange="cambiarTipo(this.id)">
                                        <option value='' selected>‌--Elija Opcion--</option>
                                        <option value='Memo'>‌Memo</option>
                                        <option value='Providencia'>Providencia</option>
                                        <option value='Correo'>‌Correo</option>
                                        <option value="Manual">Manual</option>
                                    </select>&nbsp;
                                    N°:
                                    <input type="text" name="norig_0" id="norig_0" class="caja_texto" size="4">&nbsp;
                                    Fecha:
                                    <input type="date" name="forig_0" id="forig_0" class="caja_texto" max="<?=$date?>">&nbsp;
                                    <img class="imgnormal" src='../img/datepicker.png' title="Seleccionar fecha" alt="Seleccionar fecha"/>
                                </span>&nbsp;&nbsp;
                                Observaciones:
                                <textarea name="obs_0" id="obs_0" class="bajo" cols="80" rows="2"></textarea>
                            </td>
                        </tr>
                    </table></center>
                </td>
                </tr>
                <tr>
                    <td class="texto8c"><button type="button" class="boton1a" id="agregarItem" onclick="agregarFila()" title="Agregar Nuevo Documento"><b>&nbsp;+&nbsp;</b></button>
                        <input type="hidden" name="contador" id="contador" value="1">
                        <input type="hidden" name="contadortotal" id="contadortotal" value="1">
                        <input type="hidden" name="subcuenta" id="subcuenta" value="1">
                        <input type="hidden" name="subcuentatotal" id="subcuentatotal" value="1">
                        <input type="hidden" name="max" id="max" value="<?= $idins ?>">
                    </td>
                </tr>
                <tr>                    
                    <td class="texto1" ><center><button type="submit" class="botonnormal">Grabar&nbsp;&nbsp;<img class='img' alt='grabar' title='Grabar en sistema' src='../img/save.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="botonnormal" type="button" onclick="window.location = 'buscarDocumento.php'">Ir a Documentos&nbsp;<img class="img" alt="documentos" title="volver a Documentos" src="../img/gest.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="botonnormal" type="button" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" onclick="window.location = 'cargaDocumento.php'" class="botonnormal">Limpiar&nbsp;&nbsp;<img class='img' alt='limpiar' title='Limpiar Pantalla' src='../img/clean.png'></button></center></td>
                </tr>
            </table>
        </form>     
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>