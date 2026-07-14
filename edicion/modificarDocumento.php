<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<html lang="es">
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
        $link = Conectarse();
        $comilladob = '"';
        $comillasim = "'";
        $fecha = date("Y-m-d");
        ?>        
        <title>SigBod - Modificar Documentos</title>
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
            function buscarDuplicado(x, i) {
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
        <script type="text/javascript">
            function buscarProv() {
                window.open('../reportes/listarBuscaProv.php', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=720,height=480 left=300,top=50');
            }
        </script>
        <script type="text/javascript">
            function buscarDev(id) {
                var iddoc = id.replace("subtipo_", "subndoc_"); //para crear id subdoc automatico
                var idprec = id.replace("subtipo_", "submonto_");
                var idfech = id.replace("subtipo_", "subfdoc_");
                var combo = document.getElementById(id);
                var selected = combo.options[combo.selectedIndex].text;
                var clave = document.getElementById("rutpv").value;
                if (selected == "Devolucion Documento") {
                    $.ajax({
                        url: "../herramientas/buscaDev.php",
                        type: "POST",
                        dataType: "json",
                        data: {clave: clave},
                        success: function (res) {//objeto que trae los parametros json elegidos
                            if (res.numdoc !== null) {
                                document.getElementById(iddoc).value = res.numdoc;
                                document.getElementById(idprec).value = 0;
                                document.getElementById(idfech).value = "<?= $fecha ?>";
                                $("#frm").change();
                            }
                        }
                    });
                }
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
                        '<tr><td class="texto7c">&nbsp;&nbsp;<input type="text" class="textoredondo" size="1" value="' + (cont - 1) + '" readonly>&nbsp;&nbsp;&nbsp;Tipo SubDoc.:&nbsp;' +
                        '<select name="subtipo_' + cuentatotal + ntab + '" class="caja_neg_ssombra" id="subtipo_' + cuentatotal + ntab + '" required onchange="buscarDev(this.id);">' +
                        '<option value="" selected>‌--Elija Opcion--</option>' +
                        '<option value="Nota de Credito">Nota de Credito</option>' +
                        '<option value="Guía de Despacho">‌Guía de Despacho</option>' +
                        '<option value="Guía Manual">‌Guía Manual</option>' +
                        '<option value="Recep. de Bodega">Recep. de Bodega</option>' +
                        '<option value="Devolucion">Devol. o Retiro</option>' +
                        '<option value="Devolucion">Devolucion Documento</option>' +
                        '<option value="Copia Factura">Copia Factura</option>' +
						'<option value="Nota de Debito">Nota de Debito</option>' +
                        '<option value="Otros">Otros</option>' +
                        '</select>&nbsp;' +
                        'N° SubDoc.:&nbsp;&nbsp;' +
                        '<input type="text" name="subndoc_' + cuentatotal + ntab + '" id="subndoc_' + cuentatotal + ntab + '" class="caja_num_subdoc" size="8" required onChange="buscarDuplicado(this.value,this.id)">&nbsp;&nbsp;&nbsp;' +
                        'Fecha Doc:&nbsp;&nbsp;' +
                        '<input type="date" name="subfdoc_' + cuentatotal + ntab + '" id="subfdoc_' + cuentatotal + ntab + '" class="caja_neg_ssombra" required>&nbsp;&nbsp;' +
                        '<img class="imgnormal" src="../img/datepicker.png" title="Seleccionar fecha" alt="Seleccionar fecha" />&nbsp;&nbsp;&nbsp;' +
                        'Monto Total $:&nbsp;&nbsp;' +
                        '<input type="text" name="submonto_' + cuentatotal + ntab + '" id="submonto_' + cuentatotal + ntab + '" class="caja_neg_ssombra" size="9" required>&nbsp;' +
                        '<button type="button" class="boton1c" id="copiaprec_' + cuentatotal + ntab + '" title="Copiar precio del Doc. Principal" onclick="copiaPrecio(' + btn + ', this.id)"><b>&nbsp;↓&nbsp;</b></button>&nbsp;&nbsp;' +
                        '<input type="file" name="file_' + cuentatotal + ntab + '" id="file_' + cuentatotal + ntab + '" class="boton1b" accept=".jpg,.pdf">' +
                        '&nbsp;<button type="button" class="boton1" id="' + idz + '" title="Eliminar" onclick="eliminarSubFila(this.parentNode.parentNode.rowIndex,this.id)"><b>-</b></button>' +
                        '<input type="hidden" name="' + idtab + '" id="' + idtab + '" value="' + (cont - 1) + '">' +
                        '<input type="hidden" name="tipodato_' + cuentatotal + ntab + '" id="tipodato_' + cuentatotal + ntab + '"  value="new"></td></tr>';
                document.getElementById("subcuentatotal").value = cuentatotal + 1;
                document.getElementById("subcuenta").value = subcuenta + 1;
                document.getElementById('subndoc_' + cuentatotal + ntab).focus();
                $("#save").prop("disabled", true);
                $("#save").css("background", "black");
                $("#save").css('cursor', 'no-drop');//cursor mause error                
            }
        </script>
        <script type="text/javascript">
            function eliminarSubFila(i, x) {
                var idx = x.replace("elimina_", "tabla_");
                var table = document.getElementById(idx);
                table.deleteRow(i);
                $("#frm").change();
            }
        </script>
        <script type="text/javascript">
            function borrar() {
                if (confirm("¿Esta Seguro que quiere borrar este Subdocumento del documento principal?"))
                    return true;
                else
                    return false;
            }
        </script>
        <script type="text/javascript">
            $(document).ready(function (e) {
                $("#save").click(function (e) {
                    e.preventDefault();
                    var data = new FormData(document.getElementById("frm")); // <-- 'this' is your form element
                    $.ajax({
                        url: 'modificarDocumentoAjax.php?op=1',
                        data: data,
                        cache: false,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        beforeSend: function () {
                            $.blockUI({
                                message: "<p class='centrar_cuadro_alertas'>Grabando, por favor espere...<img src='../img/loading.gif' width='200' height='140' /></p>"
                            });
                        },
                        error: function () {
                            alert("error peticion ajax");
                        },
                        success: function (data) {
                            $.unblockUI();
                            $("#resultado").empty();
                            $("#resultado").append(data);
                        }
                    });
                });
            }
            );
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#frm").change(function () {
                    var cuenta = 0;
                    var form = $(this);
                    form.find("input:required, select:required").each(function () {//revisar siu estan llenos los campos antes de grabar
                        var cont = $(this);
                        var largo = cont.val().length;
                        if (largo === 0) {
                            $(cont).css("background", "red");
                            cuenta = cuenta + 1;
                        } else {
                            $(cont).css("background", "");
                        }
                        if (cuenta === 0) {
                            $("#save").prop("disabled", false);
                            $("#save").css("background", "");
                            $("#save").css('cursor', 'pointer');
                        } else {
                            $("#save").prop("disabled", true);
                            $("#save").css("background", "black");
                            $("#save").css('cursor', 'no-drop');//cursor mause error
                        }
                    });
                });
            });
        </script>
        <script type="text/javascript">
            function copiaPrecio(x, y) {
                var origen = document.getElementById("monto_" + x).value;
                if (origen != "") {
                    var id = y.replace("copiaprec_", "submonto_");
                    $("#" + id).val(origen);
                    alert("Precio Copiado");
                    $("#frm").change();
                } else {
                    alert("Se ha producido un error, favor revise los datos e intente nuevamente");
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
    </head>
    <body class="fondo">
        <?php
        $id = $_GET["id"];
        $sql = "SELECT * FROM proveedores, documento WHERE doc_rutpv=prov_rut AND doc_id='$id'";
        $data = mysql_query($sql);
        $row = mysql_fetch_array($data);
        ?>

    <center><form id="frm" name="frm" action="modificarDocumentoAjax.php" method="post" enctype="multipart/form-data">
            <table class="table2d" id="tabla">
                <tr>
                    <td class="texto"><img class="imgico" alt='Documento' title='Documento' src='../img/edit1.png'>&nbsp;&nbsp;&nbsp;MODIFICAR DOCUMENTOS v.1</td>
                </tr>
                <tr>
                    <td class="texto5a"> N° Registro:<input type="text" name="nreg" id="nreg" class="caja_neg_ssombra" size="4" readonly value="<?= $row["doc_id"] ?>" />&nbsp;&nbsp;&nbsp;&nbsp;RUT Proveedor:
                        <input type="text" name="rutpv" id="rutpv" class="caja_color" value="<?= $row["prov_rut"] ?>"  size="9"  maxlength="8" required readonly/> - <input type="text" name="dv" id="dv" class="caja_color" value="<?= $row["prov_dv"] ?>" size="1"  maxlength="1" readonly />
                        &nbsp;&nbsp;&nbsp;<img class="imgnormal" id="imgbp" alt="buscar" title="buscar" src="../img/lupan.png" onclick="buscarProv()">
                        &nbsp;&nbsp;&nbsp;
                        <span class="texto5a">Nombre Proveedor:</span>
                        <input type="text" name="nompv" id="nompv" class="caja_negrita" size="60" readonly value="<?= $row["prov_nombre"] ?>" />
                </tr>
                <tr>
                    <td class="texto5c">
                        <table id="tabla_0" class="table2c">
                            <tr>
                                <td>
                                    <span class="caja_pading1">Tipo de Doc.:&nbsp;
                                        <select name="tipo_0" class="caja_texto" id="tipo_0" required>
                                            <option value="">‌Elija Opcion</option>
                                            <?php echo "<option value='" . $row["doc_tipodoc"] . "' selected>‌" . $row["doc_tipodoc"] . "</option>"; ?>
                                            <option value='Boleta'>‌Boleta</option>
                                            <option value='Factura'>‌Factura</option>
                                            <option value='Guía de Despacho'>‌Guía de Despacho</option>
                                            <option value="Nota de Credito">Nota de Credito</option>
                                            <option value="Nota de Debito">Nota de Debito</option>
                                        </select>&nbsp;&nbsp;                       
                                        N° Documento:&nbsp;
                                        <input type="text" name="nfact_0" id="nfact_0" class="caja_texto" size="8" required onChange="buscarDuplicado(this.value, this.id)" value="<?= $row["doc_ndoc"] ?>" >&nbsp;&nbsp;
                                        Fecha Doc.:&nbsp;
                                        <input type="date" name="ffact_0" id="ffact_0" class="caja_texto" required value="<?= $row["doc_fechadoc"] ?>" >&nbsp;
                                        <img class="imgnormal" src='../img/datepicker.png' title="Seleccionar fecha" alt="Seleccionar fecha"/>&nbsp;&nbsp;
                                        URL:&nbsp;<input type="text" name="url_0" id="url_0" class="caja_texto" size="3" value="<?= $row["doc_url"] ?>">
                                    </span>&nbsp;&nbsp;
                                    O.C.:&nbsp;
                                    <input type="text" name="noc_0" id="noc_0" class="caja_neg_ssombra" size="18" placeholder="Escriba OC" required value="<?= $row["doc_noc"] ?>" onChange="validaOC(this.id)">&nbsp;
                                    Monto $:&nbsp;
                                    <input type="text" name="monto_0" id="monto_0" class="caja_neg_ssombra" size="12" placeholder="Monto con IVA" required value="<?= $row["doc_montototal"] ?>">&nbsp&nbsp;&nbsp;
                                    <button type="button" class="boton1a" id="agrega_0" onclick="agregarSubFila(this.id)" title="Agregar Subdocumento"><b>&nbsp;+&nbsp;</b></button>
                                </td>
                            </tr>
                            <tr>                                
                                <td>
                                    <span class="caja_pading1">
                                        Origen Doc.:
                                        <select name="origen_0" class="caja_texto" id="origen_0">
                                            <option value='' selected>‌Elija Opción</option>
                                            <?php echo "<option value='" . $row["doc_origen"] . "' selected>‌" . $row["doc_origen"] . "</option>"; ?>
                                            <option value='Memo'>‌Memo</option>
                                            <option value='Providencia'>Providencia</option>
                                            <option value='Correo'>‌Correo</option>
                                            <option value="Manual">Manual</option>
                                        </select>&nbsp;
                                        N°:
                                        <input type="text" name="norig_0" id="norig_0" class="caja_texto" size="4" value="<?= $row["doc_norigen"] ?>">&nbsp;
                                        Fecha:
                                        <input type="date" name="forig_0" id="forig_0" class="caja_texto" value="<?= $row["doc_fechaorigen"]; ?>">&nbsp;
                                        <img class="imgnormal" src='../img/datepicker.png' title="Seleccionar fecha" alt="Seleccionar fecha"/>
                                    </span>									
                                    &nbsp;&nbsp;Observaciones:
                                    <textarea name="obs_0" id="obs_0" class="bajo" cols="80" rows="2"><?= $row["doc_obs"] ?></textarea>
                                </td>
                                <?php
                                $cont = 1;
                                if ($row["doc_subdoc_cuenta"] > 0) {
                                    $sql1 = "SELECT * FROM subdocumento WHERE subdoc_id='$id'";
                                    $data1 = mysql_query($sql1);
                                    while ($row1 = mysql_fetch_array($data1)) {
                                        if ($row1["subdoc_tipo"] == "Recep. de Bodega" || $row1["subdoc_tipo"] == "Nota de Credito" || $row1["subdoc_tipo"] == "Devolucion") {
                                            $tipo = 1;
                                        } else {
                                            $tipo = 0;
                                        }
                                        if ($row1["subdoc_adjunto"] == "") {
                                            $adj = "&nbsp;&nbsp;<img class='imgcopy' alt='archivo' title='No hay archivo adjunto' src='../img/error.png'>&nbsp;&nbsp;&nbsp;";
                                        } else {
                                            $adj = "&nbsp;&nbsp;<a href=" . $row1["subdoc_adjunto"] . " target='_blank' title='Ver Imagen' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=720, width=840 left=300 top=10" . $comillasim . "); return false;" . $comilladob . "><img class='imgcopy' alt='archivo' title='Ver archivo adjunto' src='../img/lupa.png'></a>&nbsp;&nbsp;&nbsp;";
                                        }
                                        echo '<tr><td class="texto7c" >&nbsp;&nbsp;<input type="text" class="textoredondo2" size="1" value="' . $cont . '" readonly>&nbsp;&nbsp;&nbsp;Tipo SubDoc.:&nbsp;';
                                        echo '<select name="subtipo_' . $cont . "_0" . '" class="caja_neg_ssombra" id="subtipo_' . $cont . "_0" . '" required onchange="mostrarCopia(this.id, this.value)"';
                                        if ($row1["subdoc_tipo"] == "Recep. de Bodega") {
                                            echo "style='background:lightblue;'";
                                        } echo '>' .
                                        '<option value="">‌--Elija Opcion--</option>' .
                                        '<option value="' . $row1["subdoc_tipo"] . '" selected>‌' . $row1["subdoc_tipo"] . '</option>' .
                                        '<option value="Nota de Credito">Nota de Credito</option>' .
                                        '<option value="Guía de Despacho">‌Guía de Despacho</option>' .
                                        '<option value="Guía Manual">‌Guía Manual</option>' .
                                        '<option value="Recep. de Bodega">Recep. de Bodega</option>' .
                                        '<option value="Devolucion">Devol. o Retiro</option>' .
                                        '<option value="Copia Factura">Copia Factura</option>' .
                                        '<option value="Otros">Otros</option>' .
                                        '</select>&nbsp;' .
                                        'N° SubDoc.:&nbsp;&nbsp;' .
                                        '<input type="text" name="subndoc_' . $cont . "_0" . '" id="subndoc_' . $cont . "_0" . '" class="caja_neg_ssombra" size="8" required onChange="buscarDuplicado(this.value,this.id)" value="' . $row1["subdoc_numdoc"] . '" readonly>&nbsp;&nbsp;&nbsp;' .
                                        'Fecha Doc:&nbsp;&nbsp;' .
                                        '<input type="date" name="subfdoc_' . $cont . "_0" . '" id="subfdoc_' . $cont . "_0" . '" class="caja_neg_ssombra" required value="' . $row1["subdoc_fecha"] . '">&nbsp;&nbsp;' .
                                        '<img class="imgnormal" src="../img/datepicker.png" title="Seleccionar fecha" alt="Seleccionar fecha" />&nbsp;&nbsp;&nbsp;' .
                                        'Monto Total $:&nbsp;&nbsp;' .
                                        '<input type="text" name="submonto_' . $cont . "_0" . '" id="submonto_' . $cont . "_0" . '" class="caja_neg_ssombra" size="9" required value="' . $row1["subdoc_monto"] . '">&nbsp;' .
                                        '<button type="button" class="boton1c" id="copiaprec_' . $cont . "_0" . '" title="Copiar precio del Doc. Principal" onclick="copiaPrecio(0, this.id)"><b>&nbsp;↓&nbsp;</b></button>&nbsp;&nbsp;' .
                                        '<input type="file" name="file_' . $cont . '_0" id="file_' . $cont . '_0" class="boton1b" accept=".jpg,.pdf">' .
                                        $adj .
                                        '&nbsp;<a href="borraSubdoc.php?id=' . $row1["subdoc_clave"] . '&rut=' . $row1["subdoc_rutpv"] . '&cont=' . $row["doc_subdoc_cuenta"] . '&clv=' . $row["doc_id"] . '&tipo=' . $tipo . '" class="boton1" title="Eliminar Registro" onclick="return borrar();"><b>-</b></a>' .
                                        '<input type="hidden" name="tipodato_' . $cont . "_0" . '" id="tipodato_' . $cont . "_0" . '"  value="act"></td></tr>';
                                        $cont ++;
                                    }
                                }
                                echo '<input type="hidden" name="tabfila_0" id="tabfila_0" value="' . ($cont - 1) . '">';
                                mysql_close($link);
                                ?>
                            </tr>
                        </table>
                        <input type="hidden" name="contadortotal" id="contadortotal" value="1">
                        <input type="hidden" name="subcuenta" id="subcuenta" value="1">
                        <input type="hidden" name="subcuentatotal" id="subcuentatotal" value="<?= $cont ?>">
                        <input type="hidden" name="rutini" id="rutini" value="<?= $row["prov_rut"] ?>">
                    </td>
                </tr>
                <tr>                    
                    <td class="texto5"><center><button type="button" class="botonnormal" id="save">Grabar y Salir&nbsp;&nbsp;<img class='img' alt='grabar' title='Grabar en sistema y salir' src='../img/save1.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" class="botonnormal">Grabar&nbsp;&nbsp;<img class='img' alt='grabar' title='Grabar en sistema' src='../img/save.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="botonnormal" type="button" onclick="window.close()">Salir&nbsp;<img class="img" alt="principal" title="Salir" src="../img/salir.png"></button></center></td>
                </tr>
            </table>
        </form></center> 
    <div id="resultado"></div>
    <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
</body>
</html>