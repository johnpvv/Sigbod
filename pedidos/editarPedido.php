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
        <title>SigBod - Editar Pedidos</title>       
        <script type="text/javascript">
            function modificarPedido() {
                if (document.form.obs.value.length === 0) {
                    alert("Error...\nDebe Escribir una Observacion para Anular...");
                    document.form.obs.focus();
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
                $("#form").on('submit', (function (e) {
                    e.preventDefault();
                    if (modificarPedido()) {
                        $.ajax({
                            url: 'editarPedidoAjax.php',
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
                            }
                        });
                    }
                }));
            });
        </script>
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
            function cambiarSol(i) {
                var valor = document.getElementById(i).value;
                var comp = document.getElementById(i).defaultValue;
                if (isNaN(valor)) {
                    alert("Error:\nSolo se Admiten Numeros en este Campo.");
                    document.getElementById(i).value = comp;
                } else {
                    var totaln = 0;
                    var idprec = i.replace("txtsol_", "txtprec_");
                    var idtotal = i.replace("txtsol_", "txtpxq_");
                    var precio = document.getElementById(idprec).value;
                    totaln = precio * valor;
                    if (totaln <= 0) {
                        alert("Error:\nEl valor solicitado no puede ser Cero... se han dejado los valores iniciales.");
                        document.getElementById(i).value = comp;
                        totaln = precio * comp;
                        document.getElementById(idtotal).value = totaln;
                    } else {
                        document.getElementById(idtotal).value = totaln;
                    }
                }
                calcularTotal();
                actualizaSol(i);
            }
            function actualizaSol(i) {
                var id = i.replace("txtsol_", "div_");
                var idsol = i.replace("txtsol_", "idmov_");
                var neto = $("#txttotalneto").val().replaceAll(".", "");
                var key = $("#" + idsol).val();
                var cant = $("#" + i).val();
                var ids = $("#id").val();
                $.ajax({
                    type: "POST",
                    url: "editarPedidoArt.php",
                    data: {neto: neto, key: key, cant: cant, ids: ids},
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
            function calcularTotal() {
                var neto = 0;
                $(".caja2b").each(function () {
                    if (isNaN(parseInt($(this).val()))) {
                        neto += 0;
                    } else {
                        neto += parseInt($(this).val());
                    }
                });
                $("#txttotalneto").val(neto.toLocaleString());
                $("#txtiva").val(Math.round((neto * 0.19)).toLocaleString());
                $("#txttotalfinal").val(Math.round((neto * 1.19)).toLocaleString());
            }
        </script>
        <script type="text/javascript">
            function borrar() {
                if (confirm("¿Esta Seguro que quiere borrar este Producto de la solicitud actual?"))
                    return true;
                else
                    return false;
            }
            function recargar() {
                location.reload();
            }
        </script>
        <script type="text/javascript">
            function actualizaObs() {
                var obs = $("#obs").val();
                var id = $("#id").val();
                $.ajax({
                    type: "POST",
                    url: "editarPedidoObs.php",
                    data: {obs: obs, id:id},
                    beforeSend: function () {//imagen de carga                            
                        $("#idobs").empty();
                        $("#idobs").append("<img src='../img/loader2.gif' width='15' heigth='15'>");
                    },
                    error: function () {
                        alert("error peticion ajax");
                    },
                    success: function (data) {
                        $("#idobs").empty();
                        $("#idobs").hide();
                        $("#idobs").append(data);
                        $("#idobs").show(800);
                    }
                });
            }
            </script>
    </head>   
    <body class="fondo">
        <?php
        include_once("../include/conn.php");
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../index.php');
            exit();
        }
        if ($_SESSION['perfil'] > 1) {
            echo '<script>alert("Su Perfil no permite la visualizacion de esta pantalla, Favor contacte al Administrador.");history.back(-1);</script>';
            echo '<center><button class="boton" onclick="javascript:window.close()">Salir&nbsp;<img class="img" alt="Salir" title="salir de la pantalla de edicion" src="../img/salir.png"></button></center>';
            exit();
        }
        $user = $_SESSION["usuario"];
        $link = Conectarse();
        $id = $_GET['id'];
        $sql1 = "SELECT * FROM movimiento, proveedores, usuarios, estado WHERE (mov_id ='$id') AND (usuario_rut=mov_usuario) AND mov_rutprv=prov_rut AND mov_estado=estado_id"; //agrupar en parentesis los campos para que se ejecute primero la accion
        $data1 = mysql_query($sql1);
        $row1 = mysql_fetch_array($data1);
        $pv = $row1["prov_nombre"];
        $estado = $row1["mov_estado"];
        $fecha = date("Y-m-d H:i", strtotime($row1["mov_fecha"]));
        $data2 = mysql_query("SELECT * FROM usuarios u INNER JOIN unid_oper UOP ON UOP.uni_id = u.usuario_institucion AND usuario_rut= '$user'", $link)or die(mysql_error());
        $row2 = mysql_fetch_array($data2);
        $uni = $row2["uni_nombre"];
        if ($estado == 0) {
            echo '<script>alert("Error...\nLa solicitud seleccionada ya ha sido Anulada, o no existe...");
				history.back(-1);</script>';
        } else {
            $sql = "SELECT * FROM movimiento, detmovimiento, productos, unimed WHERE (mov_id ='$id') AND mov_id=detmov_id AND detmov_codigoprd=prd_codigo AND prd_unimed=unimed_id"; //agrupar en parentesis los campos para que se ejecute primero la accion
            $data = mysql_query($sql);
            $cuenta = mysql_num_rows($data);
            $udp = $row["prd_prov_unimed"];
            $res4 = MySQL_query("SELECT * FROM unimed WHERE unimed_id='$udp'", $link)or die(mysql_error());
            $row4 = mysql_fetch_array($res4);
            $uniprov = $row4["unimed_nombre"];
            if ($uniprov == "--Elija Unidad--") {
                $uniprov = "Sin Datos";
            }
        }
        ?>
        <form id="form" name="form" method="post" autocomplete="off">
            <table class="table1" id="orden">
                <tr>
                    <td colspan="2" class="texto"><img class="imgico" alt='pedidos' title='pedidos' src='../img/ped1.png'>&nbsp;&nbsp;&nbsp;EDITAR SOLICITUD A PROVEEDOR</td>
                </tr>
                <tr>
                    <td class="texto8">N° SOLICITUD:</td>                    
                    <td class="texto9"><b style="color:red;"><?= $id . "/" . date("Y", strtotime($row1["mov_fecha"])) ?></b>&nbsp;<span id="idobs" style="height:15px;"></span></td>
                </tr>
                <tr>
                    <td class="texto8">FECHA SOLICITUD:</td>                    
                    <td class="texto9"><input type="datetime-local" value="<?= $fecha; ?>" class="texto6a" name="fecha" id="fecha"></td>
                </tr>
                <tr>
                    <td class="texto8">PROVEEDOR:</td>                    
                    <td class="texto9"><b><?= $pv ?></b></td>
                </tr>
                <tr>
                    <td class="texto8">RUT PROVEEDOR:</td>                    
                    <td class="texto9"><?= number_format($row1["prov_rut"], 0, ',', '.') . '-' . $row1["prov_dv"] ?></td>
                </tr>
                <tr>
                    <td class="texto8">USUARIO SOLICITANTE:</td>                    
                    <td class="texto9"><?= $row1["usuario_rut"]; ?>&nbsp;&nbsp;<b><?= $row1["usuario_nombre"] ?>&nbsp;&nbsp;<?= $row1["usuario_apellidos"] ?></b></td>
                </tr>
                <tr>
                    <td class="texto8">UNIDAD SOLICITANTE:</td>                    
                    <td class="texto9"><?= $uni ?></td>
                </tr>
                <tr>
                    <td class="texto8">N° ARTÍCULOS:</td>                    
                    <td class="texto9"><input type="text" class="caja2c" id="contador1" size="2" value="<?= $cuenta ?>" ></td>
                </tr>
                <tr>
                    <td class="texto8">OBSERVACIONES:</td>                    
                    <td class="texto9"><textarea name="obs" id="obs" class="caja_color" cols="105" rows="4" onchange="actualizaObs();"><?= $row1["mov_observacion"] ?></textarea></td>
                </tr>
            </table>
            <table class="table1" id="tabla">
                <thead>
                    <tr class="texto5">
                        <th width="15">N°</th>
                        <th width="90">Código Interno</th>
                        <th width="70">Código CM</th>
                        <th width="320">Glosa</th>
                        <th width="50">U. M.</th>
                        <th width="100">Orden de Compra</th>
                        <th width="70">Cantidad Solicitada</th>
                        <th width="120">Precio Unitario</th>
                        <th width="110">Precio Total Neto</th>
                        <th width="40" data-sorter="false" data-filter="false">Acciones</th>
                    </tr>
                </thead>
                <?php
                $cont = 0;
                while ($row = mysql_fetch_array($data)) {
                    $neto = ((float) $row["detmov_precio"] * (float) $row["detmov_cantidad"]);
                    $cod = $row["prd_codigo"];
                    $idmov = $row["detmov_num"];
                    $mod = $row["detmov_edit"];
                    if ($mod == "1") {
                        $mod = "<img src='../img/check1.png' width='15' heigth='15' title='Ya editado.'>";
                    } else {
                        $mod = "";
                    }
                    printf("<tr><td>%s</td><td align='left'><b>%s</b></td><td>%s</td><td align='left'>%s</td><td>%s</td><td>%s</td><td>%s</td><td><b>%s</b></td><td><b>%s</b></td><td>%s</td></tr>", $cont + 1, '<input type="textbox" class="caja2" title="La presentacion del Proveedor es: ' . $uniprov . ' " readonly size="7" name="txtcod_' . $cont . '" id="txtcod_' . $cont . '" value="' . $cod . '">&nbsp;<span id="div_' . $cont . '" style="height:15px;">' . $mod . '</span>', $row["prd_codcm"], $row["prd_glosa"], $row["unimed_nombre"], '<input type="textbox" class="caja2" size="17" value="' . $row["detmov_oc"] . '" name="txtoc_' . $cont . '" id="txtoc_' . $cont . '" readonly>', '<input type="textbox" class="caja_azul" size="6" value="' . $row["detmov_cantidad"] . '"name="txtsol_' . $cont . '" id="txtsol_' . $cont . '" onchange="cambiarSol(this.id)" required>', '$ <input type="textbox" class="caja2c" size="10" value="' . number_format($row["detmov_precio"], 2, '.', '') . '" name="txtprec_' . $cont . '" id="txtprec_' . $cont . '" readonly>', '$ <input type="textbox" class="caja2b" size="10" value="' . number_format(($row["detmov_precio"] * $row["detmov_cantidad"]), 0, '.', '') . '" name="txtpxq_' . $cont . '" id="txtpxq_' . $cont . '" readonly>', "<a href=../pedidos/editarBorrarArtPed.php?idart=" . $idmov . "&sol=" . $id . "&val=" . $neto . "&cod=" . $cod . " onclick='return borrar()'><img border='0' alt='borrar articulo' title='Borrar Articulo " . $cod . " de la solicitud: " . $id . "' src='../img/trash.png' width='23' height='23'></a><input type='hidden' id='idmov_" . $cont . "' value='" . $idmov . "'>");
                    $sum = $sum + $neto;
                    $cont++;
                }
                echo '<tr><td colspan="10" class="color_gris"></td></tr>';
                echo '<tr><td colspan="8" class="texto8">Total NETO:</td><td colspan="2" class="texto9">$ <input type="text" class="caja2c" id="txttotalneto" name="txttotalneto" value="' . number_format($sum, 0, ',', '.') . '" size="15" readonly></td></tr>';
                echo '<tr><td colspan="8" class="texto8">IVA 19%:</td><td colspan="2" class="texto9">$ <input type="text" class="caja2d" id="txtiva" name="txtiva" value="' . number_format(($sum * 0.19), 0, ',', '.') . '" size="15" readonly></td></tr>';
                echo '<tr><td colspan="8" class="texto8"><b>Total Solicitado (IVA Incluido):</b></td><td colspan="2" class="texto9"><b>$ <input type="text" class="caja2c" id="txttotalfinal" name="txttotalfinal" value="' . number_format(($sum * 1.19), 0, ',', '.') . '" size="15" readonly></b></td></tr>';
                mysql_free_result($data);
                mysql_close($link);
                ?>
                </tr>
            </table>
            <table class="table1" id="tablaboton">
                <tr>
                    <td colspan="2" class="texto5a">
                        <button class="boton" type="button" onclick="javascript:history.go(-1)">Volver Atras&nbsp;&nbsp;<img class='img' alt='volver' title='Volver a pantalla anterior' src='../img/undo.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="boton" type="button" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="boton" type="button" id="capturar" onclick="window.open('../reportes/pedidoPDF.php<?php echo'?id=' . $id ?>', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=860,height=620 left=200,top=30')">Generar PDF&nbsp;<img class="img" alt="generar PDF" title="generar PDF" src="../img/pdf1.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" name="anular" class="boton">Anular Solicitud&nbsp;<img class="img" alt="grabar" title="grabar" src="../img/error.png"></button>
                    </td>
                </tr>
                <input type="hidden" name="id" id="id" value="<?= $id ?>">
            </table>
        </form>        
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
        <div id="resultado"></div>
        <script><?= $rec ?></script>
    </body>
</html>