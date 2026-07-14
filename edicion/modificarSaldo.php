<!DOCTYPE html>
<!-- fecha creacion: 17/05/2019
Sigbod John Vaccarella
-->
<?php
include_once("../include/conn.php");
include("../include/NumeroAletras.php"); // convertir numeros a letras
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
        <script src="../js/tablesorter.min.js" type="text/javascript"></script>
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
                if (confirm("¿Esta Seguro que los datos estan correctos?")) {
                    return true;
                } else {
                    return false;
                }
            }
        </script>
        <script type="text/javascript">
            function borrar() {
                if (confirm("¿Esta Seguro que quiere borrar este Producto de la orden de compra actual?"))
                    return true;
                else
                    return false;
            }
        </script>
        <script type="text/javascript">
            function agregarFila() {
                var table = document.getElementById("tabla");
                var cont = table.rows.length;//se resta 11 porque son las filas que tienen datos fijos en la tabla
                var cuenta = parseInt($("#contador").val());
                $("#contador").val(cuenta + 1);
                $("#contador1").val(cont);
                var id = "txtcod_" + cuenta;
                document.getElementById("tabla").insertRow(cont).innerHTML = '<td>' + (cuenta + 1) + '</td><td><input required type="textbox" class="caja2" size="7" id="txtcod_' + (cuenta) + '" name="txtcod_' + (cuenta) + '"><img class="imgpeq" id="img_' + (cuenta) + '" alt="buscar" title="buscar" src="../img/lupan.png" onclick="buscar(this.id)"></td>\n\
                <td><input type="text" id="txtcm_' + (cuenta) + '" value="" size="6" class="caja2" readonly></td>\n\
                <td align="left"><input type="text" id="txtglosa_' + (cuenta) + '" value="" size="40" class="caja2a" readonly></td>\n\
                <td><input type="text" id="txtum_' + (cuenta) + '" value="" size="6" class="caja2" readonly></td>\n\
                <td><input required type="text" class="caja_color" size="6" value="" name="txtsol_' + (cuenta) + '" id="txtsol_' + (cuenta) + '" onchange="calcularPend(this.id)" /></td>\n\
                <td><input required type="text" class="caja_azul" size="6" value="" name="txtrec_' + (cuenta) + '" id="txtrec_' + (cuenta) + '" onchange="calcularRec(this.id)"/></td>\n\
                <td><input required type="text" class="caja_rojo" size="6" value="" name="txtpend_' + (cuenta) + '" id="txtpend_' + (cuenta) + '"/></td>\n\
                <td>$ <input required type="text" class="caja2c" size="9" value="" name="txtprec_' + (cuenta) + '" id="txtprec_' + (cuenta) + '" onchange="calcularPrecioF(this.id)"/></td>\n\
                <td align="left"><span class="texto5" id="txtpxq_' + (cuenta) + '"></span></td><td></td>\n\
                <td><img class="imgcentro" alt="borrar articulo" title="Borrar Articulo" src="../img/trash.png" onclick="eliminarFila(this.parentNode.parentNode.rowIndex)"></td>\n\
                <input type="hidden"  value="n" name="txttipo_' + (cuenta) + '" />';
                document.getElementById("id").value = id;
                document.getElementById(id).focus();
            }
        </script>
        <script type="text/javascript">
            function eliminarFila(i) {
                var table = document.getElementById("tabla");
                var rowCount = table.rows.length;
                if (rowCount <= 1) {
                    alert('No se pueden eliminar los Títulos');
                } else {
                    table.deleteRow(i);
                    $("#contador1").val(rowCount - 2);
                }
            }
        </script>
        <script type="text/javascript">
            function calcularPend(i) {
                var valor = document.getElementById(i).value;
                if (isNaN(valor)) {
                    alert("Error:\nSolo se Admiten Numeros en este Campo.");
                    document.getElementById(i).value = "";
                } else {
                    var valorfin = 0;
                    var valorreal = 0;
                    var rec = i.replace("txtsol_", "txtrec_");
                    var pend = i.replace("txtsol_", "txtpend_");
                    var comp = document.getElementById(i).defaultValue;
                    var valorrec = document.getElementById(rec).value;
                    var valorpend = document.getElementById(pend).value;
                    if (valorrec !== "") {
                        valorreal = valorrec;
                    } else {
                        valorreal = 0;
                    }
                    valorfin = valor - valorrec;
                    if (valorfin <= 0) {
                        alert("Error:\nEl valor Comprado no puede ser Cero... se han dejado los valores iniciales.");
                        document.getElementById(rec).value = valorrec;
                        document.getElementById(pend).value = valorpend;
                        document.getElementById(i).focus();
                        document.getElementById(i).value = comp;
                    } else {
                        document.getElementById(pend).value = valorfin;
                        document.getElementById(rec).value = valorreal;
                    }
                }
                calcularPrecioN(i);
            }
            function calcularRec(i) {
                var valorfin = 0;
                var pend = i.replace("txtrec_", "txtpend_");
                var sol = i.replace("txtrec_", "txtsol_");
                var valorrec = document.getElementById(i).value;
                if (isNaN(valorrec)) {
                    alert("Error:\nSolo se Admiten Numeros en este Campo.");
                    document.getElementById(i).value = 0;
                } else {
                    var valorsol = document.getElementById(sol).value;
                    valorfin = valorsol - valorrec;
                    if (valorfin <= 0) {
                        alert("Alerta:\nEl Valor pendiente ha quedado en Cero...");
                        document.getElementById(pend).value = valorfin;
                    } else {
                        document.getElementById(pend).value = valorfin;
                    }
                }
            }
            function calcularPrecioN(x) {
                var id = x.replace("txtsol_", "txtpxq_");
                var precio = x.replace("txtsol_", "txtprec_");
                var prec = document.getElementById(precio).value;
                if (isNaN(prec)) {
                    alert("El Precio ingresado no es un numero Válido");
                    document.getElementById(precio).value = 0;
                } else {
                    var cant = document.getElementById(x).value;
                    document.getElementById(id).innerHTML = "$ " + parseInt(prec * cant).toLocaleString();
                    calcularTotal();
                }
            }
            function calcularPrecioF(x) {
                var id = x.replace("txtprec_", "txtpxq_");
                var cantidad = x.replace("txtprec_", "txtsol_");
                var prec = document.getElementById(x).value;
                if (isNaN(prec)) {
                    alert("El Precio ingresado no es un numero Válido");
                    document.getElementById(x).value = "";
                } else {
                    var cant = document.getElementById(cantidad).value;
                    document.getElementById(id).innerHTML = "$ " + parseInt(prec * cant).toLocaleString();
                    calcularTotal();
                }
            }
            function calcularTotal() {
                var z = 0;
                var neto = 0;
                $('#tabla').each(function () {
                    $('span.texto5').each(function () {
                        neto = neto + parseInt($('#txtpxq_' + z).text().replace(/[^\d,]/g, ""));
                        z++;
                    });
                });
                $("#txttotalneto").val(neto.toLocaleString());
                $("#txtiva").val(Math.round((neto * 0.19)).toLocaleString());
                $("#txttotalfinal").val(Math.round((neto * 1.19)).toLocaleString());
            }
        </script>
        <script type="text/javascript"> //consultar datos y autollenar campos 
            $(document).ready(function () {
                $("#agregar").click(function () {
                    var i = ($("#contador").val() - 1); //determinar posicion en la fila de la tabla
                    $("#txtcod_" + i).on("blur", function () {//activar funcion al sacar el foco del input del codigo
                        var valor = $(this).val(); //sacar el valor del input del codigo
                        if (valor !== "") {
                            $.ajax({
                                url: "../herramientas/buscaCod.php",
                                type: "POST",
                                dataType: "json",
                                data: {val: valor},
                                success: function (res) {//objeto que trae los parametros json elegidos
                                    if (res.glosa === null) {
                                        alert("Error...\nEl codigo ingresado: '" + valor + "' , NO Existe");
                                        $("#txtcod_" + i).val("");
                                        $("#txtcm_" + i).val("");
                                        $("#txtglosa_" + i).val("");
                                        $("#txtum_" + i).val("");
                                        $("#txtcod_" + i).focus();
                                    } else {
                                        if (res.estado === "0") {
                                            alert("Error...\nEl codigo ingresado: " + valor + " , NO esta Vigente, favor revisar");
                                            $("#txtcod_" + i).val("");
                                            $("#txtglosa_" + i).val("");
                                            $("#txtcod_" + i).focus();
                                        } else {
                                            $("#txtcm_" + i).val(res.cm); //asignar los valores a los input elegidos dinamicos
                                            $("#txtglosa_" + i).val(res.glosa);
                                            $("#txtum_" + i).val(res.um);
                                            $("#txtprec_" + i).val(res.prec);
                                        }
                                    }
                                }
                            });
                        }
                    });
                });
            });
        </script>
        <script type="text/javascript">
            function buscar(id) {
                var idx = id.replace("img", "txtcod");
                document.getElementById("id").value = idx;
                window.open('../reportes/listarBuscarArticulo.php', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=720,height=480 left=300,top=50');
            }
        </script>
        <script type="text/javascript">
            function actualizaEstado(i, x) {
                var id = i.replace("chk", "div");
                var chk = $("#" + i).prop("checked");
                $.ajax({
                    type: "POST",
                    url: "modificarSaldoEstadoAjax.php",
                    data: {chk: chk, key: x},
                    beforeSend: function () {//imagen de carga                            
                        $("#" + id).empty();
                        $("#" + id).append("<img src='../img/loader2.gif' width='15' heigth='15'>");
                    },
                    error: function () {
                        alert("error peticion ajax");
                    },
                    success: function (data) {
                        $("#" + id).empty();
                        $("#" + id).hide();
                        $("#" + id).append(data);
                        $("#" + id).show(800);
                    }
                });
            }
        </script>  
        <script type="text/javascript">
            function editarObs() {
                $("#obs").val("");
                $("#obs").focus();
            }
        </script>
        <script type="text/javascript">
            function copiarPorta(x) {
                $("#divoc").hide();
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val($(x).val()).select();
                document.execCommand("copy");
                $temp.remove();
                $("#divoc").empty();
                $("#divoc").append("<img src='../img/check.png' width='15' heigth='15' title='copiado OK'>");
                $("#divoc").show("800");
            }
        </script> 
        <script type="text/javascript">
            $(document).ready(function (e) {
                $("#form").on('submit', (function (e) {
                    e.preventDefault();
                    if (validar()) {
                        $.ajax({
                            url: 'modificarSaldoAjax.php',
                            data: $("#form").serialize(),
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
                                $("#resultado").empty();
                                $("#resultado").append(data);
                                $.unblockUI();
                                check();
                            }
                        });
                    }
                }));
            });
        </script>
        <script type="text/javascript">
            function check() {
                $("#check").show(1000);
                $('.check').show("slow");
            }
            function mensaje() {
                $("#est").append("(TERMINADA)");
            }
            function mensaje1() {
                $("#est").append("(EN PROCESO)");
            }
            function limpiar(){
                var id = $("#lic").val().replace(/[`~!@#$%^&*()_|+\=?;:'",.<>\{\}\[\]\\\/]/gi, '').trim();
                $("#lic").val(id);
            }
        </script>
        <title>SigBod - Editar Saldos OC</title>
    </head>
    <body class="fondo">
        <?php
        $link = Conectarse();
        $rutpv = $_GET['id']; //obtenemos el rut del proveedor elegido
        $oc = $_GET['oc']; //obtenemos orden de compra si se ha enviado
        $op = $_GET['op'];
        $comilladob = '"';
        $comillasim = "'";
        $verdoc = "<a href=../herramientas/generarDocOC.php?id=" . $oc . "&rut=" . $rutpv . "&op=1 target='_blank' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=560, width=1320 left=10 top=10" . $comillasim . "); return false;" . $comilladob . "><img src='../img/fact.png' class='imgcopy' title='Ver Documentos Registrados'></a>";
        $versofya = "<a href=http://10.6.24.11/SOFYA/contenido/Abastecimiento/OrdenDeCompra/AsignarSKUaOC.aspx?key=" . $oc . " target='_blank' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=520, width=1024 left=150 top=10" . $comillasim . "); return false;" . $comilladob . "><img src='../img/link2.png' class='imgcopy' title='Ver en sistema Sofya'></a>";
        if ($oc == null) {
            echo '<script>alert("Error... Parametro de OC Invalido...");history.back(-1);</script>';
            exit();
        } else {
            $varsql = " AND `saldo_oc`='$oc'";
        }
        if ($op == 1) {
            $btn = '<button class="boton" type="button" onclick="window.close()">Salir&nbsp;<img class="img" alt="principal" title="Salir" src="../img/salir.png"></button>';
        } else {
            $btn = '<button class="boton" type="button" onclick="javascript:history.back(-1)">Volver Atras&nbsp;<img class="img" alt="principal" title="Salir" src="../img/undo.png"></button>';
        }
        $sql = "SELECT `prd_codigo`,`prd_codcm`, `prd_glosa`, `prd_unimed`,`unimed_nombre`, `saldo_oc`, `saldo_est`,`prd_prov_unimed`, `saldo_id`,`saldo_rutprov`,`prov_nombre`,`prov_rut`,`prov_dv`, `saldo_codigo`,`saldo_fechaoc`, `saldo_pendiente`,`saldo_recibido`,`saldo_solicitado`,`saldo_preciounit` FROM `productos`,`proveedores`,`saldos`,`unimed` WHERE `prd_codigo` = `saldo_codigo` AND `prd_unimed`= `unimed_id` AND `prov_rut`= `saldo_rutprov` AND `saldo_rutprov`='$rutpv' " . $varsql . " ORDER BY saldo_codigo";
        $result = mysql_query($sql, $link);
        $result1 = mysql_query("SELECT `prov_nombre`,`prov_rut`,`prov_dv`, saldo_oc, saldo_fechaoc, saldo_fechacarga FROM `proveedores`,`saldos` WHERE `prov_rut`= '$rutpv' AND saldo_oc='$oc' ", $link);
        $row1 = mysql_fetch_array($result1);
        $cuenta = mysql_num_rows($result);
        $result2 = mysql_query("SELECT * FROM oc_obs WHERE oc_obs_oc='$oc' ORDER BY oc_obs_id DESC LIMIT 1", $link);
        $row2 = mysql_fetch_array($result2);
        $obs = $row2["oc_obs_det"];        
        $lic = $row2["oc_obs_lic"];
        if($lic!=""){
            $linklic= "<a href=decodeJsonLic.php?id=" .$lic. "&op=1 target='_blank' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=550, width=1068 left=350 top=100" . $comillasim . "); return false;" . $comilladob . "><img src='../img/info1.png' class='imgcopy' title='Ver Licitacion en Sistema'></a>";
        }
        if ($cuenta == 0) {
            //window.location.replace("https://www.mercadopublico.cl/PurchaseOrder/Modules/PO/DetailsPurchaseOrder.aspx?codigooc=' . $oc . '");
            echo "<b class='texto1'>Procesando...</b>";
            echo '<script>if (confirm("Los Datos ingresados no son validos, o la orden de compra no esta Registrada en Sigbod. Desea revisar en Mercadopublico?")){
			window.location.replace("../edicion/decodeJsonOC.php?op=1&oc=' . $oc . '&data=1");
		}else{window.close();}</script>';
        }
        $rut = "<a href=../edicion/modificarProveedor.php?id=" . $row1["prov_rut"] . "&option=1 target='_blank' title='Ver detalle proveedor' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=550, width=800 left=300 top=100" . $comillasim . "); return false;" . $comilladob . ">" . number_format($row1["prov_rut"], 0, '.', '.') . "-" . $row1["prov_dv"] . "</a>";
        $rut1 = number_format($row1["prov_rut"], 0, '.', '.') . "-" . $row1["prov_dv"];
        $nombre = $row1["prov_nombre"];
        $oc1 = $row1["saldo_oc"];
        $oc2 = "<a href=https://www.mercadopublico.cl/PurchaseOrder/Modules/PO/DetailsPurchaseOrder.aspx?codigooc=" . $oc . " target='_blank' title='Ver OC en MP' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=640, width=840 left=100 top=30" . $comillasim . "); return false;" . $comilladob . ">" . $oc . "</a>";
        $fechaoc = date("d/m/Y", strtotime($row1["saldo_fechaoc"]));
        $fechaoc1 = $row1["saldo_fechaoc"]; //pasar a la bbdd
        $fechacarga = date("Y-m-d", strtotime($row1["saldo_fechacarga"]));
        $mp = "<a href=decodeJsonOC.php?oc=" . $oc . "&op=1 target='_blank' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=640, width=1068 left=100 top=20" . $comillasim . "); return false;" . $comilladob . "><img src='../img/mp1.png' class='imgcopy' title='Ver Informacion Avanzada de la Orden de compra'></a>";
        $sol = "<a href=../reportes/cartolaOC.php?oc=" . $oc . "&op=1 target='_blank' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=640, width=920 left=200 top=20" . $comillasim . "); return false;" . $comilladob . "><img src='../img/ped1.png' class='imgcopy' title='Ver Solicitudes Asociadas a esta OC'></a>";
        $result3 = mysql_query("SELECT SUM(doc_montototal) AS sum FROM documento WHERE doc_noc='$oc' AND doc_estado <> 'Anulado'", $link);
        $row3 = mysql_fetch_array($result3);
        $sumfact = $row3["sum"];
		
        ?>
        <form id="form" name="form" method="post" autocomplete="off">
            <table class="table1" id="orden">
                <tr>
                    <td colspan="2" class="texto"><img class="imgico" alt='OC' title='OC' src='../img/oc2.png'>&nbsp;&nbsp;&nbsp;EDITAR SALDOS DE ORDEN DE COMPRA&nbsp;<span id="est"></span></td>
                </tr>
                <tr>
                    <td class="texto8">RUT PROVEEDOR:</td>                    
                    <td class="texto9"><b><?php echo $rut; ?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img class="imgnormal" id="check" src="../img/check.png" alt="ok" hidden></td>
                </tr>
                <tr>
                    <td class="texto8">NOMBRE PROVEEDOR:</td>                    
                    <td class="texto9"><b><?php echo $nombre; ?></b></td>
                </tr>
                <tr>
                    <td class="texto8">ID LICITACIÓN:</td>                    
                    <td class="texto9"><input type="text" id="lic" name="lic" value="<?php echo $lic ?>" class="texto6b" size="15" onchange="limpiar()">&nbsp;&nbsp;&nbsp;<?php echo $linklic ?>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="parc" id="parc" <?php
                        if ($row2["oc_obs_parc"] == 1) {
                            echo " checked";
                        }?>/> Parcializada</td>
                </tr>
                <tr style="height:35px;">
                    <td class="texto8">ORDEN DE COMPRA:</td>                    
                    <td class="texto9"><input type="hidden" value="<?php echo $oc1; ?>" id="oc"><b><?php echo $oc2; ?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b class="texto6">Acciones:</b>&nbsp;&nbsp;<img src="../img/copy1.png" class="imgcopy" title="copiar al portapapeles"  onclick="copiarPorta('#oc');">&nbsp;<span id='divoc' style="height: 15px;">&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;<?= $verdoc ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $versofya ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $mp ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $sol ?></td>
                </tr>                
                <tr>
                    <td class="texto8">FECHA ORDEN DE COMPRA:</td>                    
                    <td class="texto9a"><input type="date" value="<?php echo $fechaoc1; ?>" class="texto6a" name="foc" id="foc"></td>
                </tr>
                <tr>
                    <td class="texto8">N° ARTÍCULOS:</td>                    
                    <td class="texto9a"><input type="text" class="caja2b" id="contador1" size="2" value="<?php echo $cuenta; ?>" ></td>
                </tr>
                <tr>
                    <td class="texto8">OBSERVACIONES:</td>                    
                    <td class="texto9a"><input type="text" class="texto6a" id="obs" name="obs" size="104" value="<?php echo $obs; ?>" >&nbsp;&nbsp;<img src="../img/lapiz.png" class="imgcopy" title="Escribir Observaciones" onclick="editarObs();"></td>
                </tr>
            </table>
            <table class="table1" id="tabla">
                <thead><tr class="texto5">
                        <th width="15">N°</th>
                        <th width="120">Código Interno</th>
                        <th width="70">Código CM</th>
                        <th width="320">Glosa</th>
                        <th width="50">U. M.</th>
                        <th width="70">Cantidad Comprada</th>
                        <th width="70">Cantidad Recibida</th>
                        <th width="70">Cantidad Pendiente</th>
                        <th width="120">Precio Unitario</th>
                        <th width="110">Precio Total Neto</th>
                        <th width="25">Nulo</th>
                        <th width="40" data-sorter="false" data-filter="false">Acción</th>
                    </tr></thead>
                <?php
                $cont = 0;
                while ($row = mysql_fetch_array($result)) {
                    if ($row["saldo_est"] == '1') {
                        $vr = " checked";
                    } else {
                        $vr = "";
                    }
                    $udp = $row["prd_prov_unimed"];
                    $res4 = MySQL_query("SELECT * FROM unimed WHERE unimed_id='$udp'", $link)or die(mysql_error());
                    $row4 = mysql_fetch_array($res4);
                    $uniprov = $row4["unimed_nombre"];
                    if ($uniprov == "--Elija Unidad--") {
                        $uniprov = "Sin Datos";
                    }
                    printf("<tr><td>%s</td><td align='left'><b>%s</b></td><td>%s</td><td align='left'>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td><b>%s</b></td><td align='left'><b>%s</b></td><td>%s</td><td>%s</td></tr>", $cont + 1, '<input type="textbox" class="caja2" style="cursor:help;" title="La presentacion del Proveedor es: ' . $uniprov . ' " readonly size="7" name="txtcod_' . $cont . '" id="txtcod_' . $cont . '" value="' . $row["prd_codigo"] . '"/>', $row["prd_codcm"], $row["prd_glosa"], $row["unimed_nombre"], '<input type="textbox" class="caja_color" size="6" value="' . $row["saldo_solicitado"] . '" name="txtsol_' . $cont . '" id="txtsol_' . $cont . '" onchange="calcularPend(this.id)">', '<input type="textbox" class="caja_azul" size="6" value="' . $row["saldo_recibido"] . '"name="txtrec_' . $cont . '" id="txtrec_' . $cont . '" onchange="calcularRec(this.id)" >', '<input type="textbox" class="caja_rojo" size="6" value="' . $row["saldo_pendiente"] . '" name="txtpend_' . $cont . '" id="txtpend_' . $cont . '" readonly>', '$ <input type="textbox" class="caja2c" size="9" value="' . number_format($row["saldo_preciounit"], 2, '.', '') . '" name="txtprec_' . $cont . '" id="txtprec_' . $cont . '" onChange="calcularPrecioF(this.id)" required>', '<span class="texto5" id="txtpxq_' . $cont . '">$ ' . number_format($row["saldo_preciounit"] * $row["saldo_solicitado"], 0, '.', '.') . '</span>', "<input type='checkbox' id='chk_" . $cont . "' " . $vr . " onchange='actualizaEstado(this.id," . $row["saldo_id"] . ")'>&nbsp;<span id='div_" . $cont . "' style='height:15px;'></span>", "<a href=../edicion/borrarArticuloOc.php?id=" . $row["prd_codigo"] . "&oc=" . $row["saldo_oc"] . "&cnt=" . $cuenta . " onclick='return borrar()'><img border='0' alt='borrar articulo' title='Borrar Articulo " . $row["prd_codigo"] . " de la OC: " . $row["saldo_oc"] . "' src='../img/trash.png' width='23' height='23'></a>");
                    $cont++;
                    $sum = $sum + ($row["saldo_solicitado"] * $row["saldo_preciounit"]);
                    $pend = $pend + $row["saldo_pendiente"];
                }
                if ($sumfact > round(($sum * 1.19), 0)) {
                    $style = " style='background-color:#ed0505;'";
                    $im = '&nbsp;&nbsp;<img class="imgcopy" alt="Alerta" src="../img/exclam.png" title="Los montos Facturados superan el total de la OC, Favor Revisar.">&nbsp;&nbsp;';
                } else {
                    $style = " style='background-color:#ccffff;'";
                    $im = '&nbsp;&nbsp;<img class="imgcopy" alt="Alerta" src="../img/check.png" title="Los montos Facturados estan dentro del Monto de la OC.">&nbsp;&nbsp;';
                }
                if ($sumfact == round(($sum * 1.19), 0) && $pend == 0) {
                    echo "<script>mensaje();</script>";
                } else {
                    echo "<script>mensaje1();</script>";
                }
				$porcentaje = " (". number_format((($sumfact / ($sum * 1.19))*100), 1, ',', '.')."%)";
                mysql_free_result($result);
                mysql_close($link);
                $_SESSION["pedido"] = 1;
                ?> 
            </table>
            <table class="table1">
                <tr>
                    <td colspan="12" class="texto8c"><button type="button" class="boton1" id="agregar" onclick="agregarFila()">&nbsp;+&nbsp;</button>
                    </td>
                </tr>
                <tr>
                    <td colspan="12">
                        <input type="hidden" name="contador" id="contador" value="<?php echo $cont ?>" />
                        <input type="hidden" name="rut" value="<?php echo $rut1 ?>" />
                        <input type="hidden" name="oc" value="<?php echo $oc1 ?>" />
                        <input type="hidden" name="nompv" value="<?php echo $nombre ?>" />
                        <input type="hidden" name="fechaoc" value="<?php echo $fechaoc1 ?>" />
                        <input type="hidden" name="id" id="id" value="">
                        <input type="hidden" name="rutpv" value="<?php echo $rutpv ?>" />
                        <input type="hidden" name="op" value="<?php echo $op ?>" />
                    </td>
                <tr>                    
                    <td colspan="10" class="texto8" style="width: 960px;">Total Neto Final:</td>                    
                    <td colspan="2" class="texto9"><b>$ </b><input type="text" class="caja2b" id="txttotalneto" name="txttotalneto" value="<?= number_format($sum, 0, '', '.') ?>" size="15" readonly>
                </tr>
                <tr>
                    <td colspan="10" class="texto8">IVA:</td>                    
                    <td colspan="2" class="texto9"><b>$ </b><input type="text" class="caja2b" id="txtiva" name="txtiva" value="<?= number_format(($sum * 0.19), 0, '', '.') ?>" size="15" readonly>
                    </td>
                </tr>
                <tr>
                    <td colspan="10" class="texto8">TOTAL FACTURADO VIGENTE:<?= $im ?></td>                    
                    <td colspan="2" class="texto9" <?= $style ?>><b>$ </b><input type="text" class="caja2c" id="txttotalfact" name="txttotalfact" value="<?= number_format(($sumfact), 0, '', '.') ?><?=$porcentaje?>" size="15" readonly <?= $style ?>>
                    </td>
                </tr> 
                <tr>
                    <td colspan="10" class="texto8">TOTAL FINAL :&nbsp;&nbsp;&nbsp;&nbsp;<?php echo convertir(number_format(($sum * 1.19), 0, '', '')) ?> PESOS.</td>                    
                    <td colspan="2" class="texto9"><b>$ </b><input type="text" class="caja2c" id="txttotalfinal" name="txttotalfinal" value="<?= number_format(($sum * 1.19), 0, '', '.') ?>" size="15" readonly>
                    </td>
                </tr>  
            </table>
            <table class="table1">
                <tr>
                    <td colspan="10" class="texto5">
                        <button class="boton" type="button" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" name="modificar" class="boton">Modificar&nbsp;<img class="img" alt="modificar" title="Grabar" src="../img/save.png"></button> &nbsp;&nbsp;&nbsp;&nbsp;<button id="pdf" type="button" class="boton" onclick="window.open('../reportes/ocPDF.php?id=<?php echo $rutpv ?>&oc=<?php echo $oc ?>', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=960,height=640 left=150,top=20')">Generar Informe en PDF&nbsp;&nbsp;<img class="img" alt="pdf" title="Mostrar OC formato PDF" src="../img/pdf1.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $btn; ?></td>
                </tr>
            </table>
        </form>        
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#tabla").tablesorter();
            });
        </script>
        <div id="resultado"></div>
    </body>
</html>