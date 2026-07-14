<!DOCTYPE html>
<!-- fecha creacion 22/10/2019 hp
fecha mod. 12/07/2022
Sigbod John Vaccarella
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$_SESSION['query'] = "";
$id = $_GET["op"];
$fechaact = date("Y-m-d");
$rec = $_GET["rec"];
if ($id == 1) {
    $chk = "checked";
    $chk1 = "selected";
    $dir = "buscar.click()";
} else {
    $chk = "";
    $chk1 = "";
    $dir = "";
}
if ($rec == 1) {
    $fdoc = date("Y-m-d", strtotime($fechaact . "- 7 days"));
    $fdoc1 = date("Y-m-d", strtotime($fechaact . "- 6 days"));
} else {
    $fdoc = "";
    $fdoc1 = "";
}
if ($rec == 2) {
    $fdoc = date("Y-01-01");
    $fdoc1 = date("Y-m-d", strtotime($fechaact));
    $est = $_GET["est"];
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />          
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>
        <script src="../js/tablesorter.min.js" type="text/javascript"></script>
        <script src="../js/jquery.tablesorter.pager.js"></script> 
		<style>
		.load tr {
			background-image: url('../img/loader2.gif');
			background-position: left left;
			background-repeat: no-repeat;
		}
		</style>
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
            $(document).ready(function () {
                var consulta;
                $("#busqueda").focus();//hacemos focus al campo de busqueda               
                $("#buscar").click(function (e) {//comprobamos si se pulsa una tecla                    
                    consulta = $("#busqueda").val().replace(/[`~!@#$%^&*()_|+\=?;:'",.<>\{\}\[\]\\]/gi, '').trim();//obtenemos el texto introducido en el campo de busqueda y elimina caracteres no correctos
                    var chkall = $("#checkall").prop("checked");
                    var chksub = $("#chksub").val();
                    var chkfac = $('#chkfac').val();
                    var anio = $("#fecha").val();
                    var anio2 = $("#fecha2").val();
                    var fmod = $("#fmod").val();
                    var fmod1 = $("#fmod1").val();
                    var fdoc = $("#fdoc").val();
                    var fdoc1 = $("#fdoc1").val();
                    var chkst = $("#checkst").prop("checked");
                    var rec = $("#rec").val();
                    largo = $("#busqueda").val().length;
                    if (largo === 0 || largo >= 1) {
                        $.ajax({//hace la busqueda
                            type: "POST",
                            url: "buscarDocumentoAjax.php",
                            data: {saldo: consulta, chkall: chkall, chkst: chkst, chksub: chksub, chkfac: chkfac, anio: anio, anio2: anio2, fmod: fmod, fmod1: fmod1, fdoc: fdoc, fdoc1: fdoc1, rec: rec},
                            beforeSend: function () {//imagen de carga                            
                                $.blockUI({
                                    message: "<p class='centrar_cuadro_alertas'>Buscando, por favor espere...<img src='../img/ajax-loading.gif' width='220' height='150' /></p>"
                                });
                            },
                            error: function () {
                                alert("error peticion ajax");
                            },
                            success: function (data) {
                                $("#resultado").empty();
                                $("#resultado").append(data);
                                $("#busqueda").select();
                                $.unblockUI();
                            }
                        });
                    }
                });
            });
        </script>        
        <script type="text/javascript">
            function limpiaFecha() {
                $("#fecha").val("");
                $("#fecha2").val("");
                buscar.click();
            }
            function limpiaFecha1() {
                $("#fmod").val("");
                $("#fmod1").val("");
                buscar.click();
            }
            function limpiaFecha2() {
                $("#fdoc").val("");
                $("#fdoc1").val("");
                buscar.click();
            }
        </script> 
        <script type="text/javascript">
            function cambiaImg(id) {
                var idx = id.replace("link_", "img_");
                $("#" + id).click(function () {
                    var nuevaImagen = "../img/fact1.png";
                    $("#" + idx).attr("src", nuevaImagen);  // cambia para saber que se revisó
                });
            }
            function cambiaImgTraza(id) {
                var idx = id.replace("linkt_", "imgt_");
                $("#" + id).click(function () {
                    var nuevaImagen = "../img/trackr.png";
                    $("#" + idx).attr("src", nuevaImagen);  // cambia para saber que se revisó
                });
            }
			function cambiaImgMail(id) {
				var idx = id.replace("linkm_", "imgm_");
                $("#" + id).click(function () {
                    var nuevaImagen = "../img/mailenv.png";
                    $("#" + idx).attr("src", nuevaImagen); // cambia para saber que se revisó
                });
            }
        </script>
		<script type="text/javascript">
            function buscaCod(x) {
                $("#busqueda").val("");
                $("#busqueda").val(x);
                buscar.click();
            }
        </script> 
        <title>SigBod - Buscar Facturas y Guias</title>
    </head>
    <body class="fondo" onload="<?= $dir ?>">        
        <table class="table2a">
            <tr>
                <td class="texto"><img class="imgico" alt='Documentos' title='Documentos' src='../img/doc4.png'>&nbsp;&nbsp;&nbsp;CONSULTA DE DOCUMENTOS</td>
            </tr>
            <tr>
                <td class="texto5"  style="padding-bottom: 10px;">Ingrese Texto a Buscar:
                    <span class="texto5"><input id="busqueda" name="txtcod" type="search" placeholder="escriba aqui..." autofocus size="35" maxlength="30" class='caja' onkeypress="if (event.keyCode == 13)
                    buscar.click();"/>&nbsp;&nbsp;&nbsp;&nbsp;<button id="buscar" class="boton1">Buscar&nbsp;<img class="imgbtnpeq" alt="buscador" title="Buscar" src="../img/lupa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;                   
                        <span class='caja_pading3'>
                            Tipo:&nbsp;&nbsp;
                            <select name="chkfac" class="caja_texto" id="chkfac">
                                <option value='' selected>‌--Todos--</option>
                                <option value='Boleta'>‌Boleta</option>
                                <option value='Factura'>‌Factura</option>
                                <option value='Guía de Despacho'>‌Guía de Despacho</option>
                                <option value="Nota de Credito">Nota de Credito</option>
                                <option value="Nota de Debito">Nota de Debito</option>
                            </select>&nbsp;&nbsp;
                            Estado:
                            <select name="chksub" class="caja_texto" id="chksub">
                                <option value=''>‌--Todos--</option>
                                <option value='Anulado'>‌Anulado</option>
                                <option value='Archivado'>Archivado</option>
                                <option value='Devuelto'>‌Devuelto</option>
                                <option value='0'>‌No Pendiente</option>
                                <option value="1" <?= $chk1 ?>>Pendiente</option>
                                <option value='Recepcionado'>‌Recepcionado</option> 
                                <option value='2'>‌No Reclamadas</option> 
                                <option value='Reclamadas'>‌Reclamadas</option> 
                                <?php
                                if ($rec == 2) {
                                    echo"<option value='" . $est . "' selected>‌" . $est . "</option>";
                                }
                                ?>
                            </select>&nbsp;&nbsp;&nbsp;
                            <input type="checkbox" name="checkst" id="checkst"/> Con Obs.
                        </span>&nbsp;&nbsp;
                        <input type="checkbox" name="checkall" id="checkall" <?= $chk ?> /> Mostrar Todos
                        <br>Filtros:
                    </span>
                    <span class='caja_pading2'>
                        Creado:
                        <input type="date" class='texto5' name="fecha" id="fecha" step="1" min="" max="<?= $fechaact ?>" value="2019-01-01"/>&nbsp;Al&nbsp;
                        <input type="date" class='texto5' name="fecha2" id="fecha2" step="1" min="" max="<?= $fechaact ?>" value="<?= $fechaact ?>"/>
                        <input type="button" id='limp' value="x" title="Limpiar fechas" class="boton1b" onclick="limpiaFecha();">&nbsp;&nbsp;
                        Modificado:
                        <input type="date" class='texto5' name="fmod" id="fmod" step="1" min="" max="<?= $fechaact ?>" value=""/>&nbsp;Al&nbsp;
                        <input type="date" class='texto5' name="fmod1" id="fmod1" step="1" min="" max="<?= $fechaact ?>" value=""/>
                        <input type="button" id='limp1' value="x" title="Limpiar fechas" class="boton1b" onclick="limpiaFecha1();">&nbsp;&nbsp;
                        Fecha Documento:
                        <input type="date" class='texto5' name="fdoc" id="fdoc" step="1" min="" max="<?= $fechaact ?>" value="<?= $fdoc ?>"/>&nbsp;Al&nbsp;
                        <input type="date" class='texto5' name="fdoc1" id="fdoc1" step="1" min="" max="<?= $fechaact ?>" value="<?= $fdoc1 ?>"/>
                        <input type="button" id='limp2' value="x" title="Limpiar fechas" class="boton1b" onclick="limpiaFecha2();">
                    </span>
                </td>                
            </tr>
            <tr>
                <td class="texto5">
                    <button class="botonnormal" onclick="window.location = '../principal.php'">Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;
                    <button class="botonnormal" onclick="window.location = 'cargaDocumento.php'">Nuevo Documento&nbsp;<img class="img" alt="nuevo" title="Crear Documento" src="../img/doc3.png"></button>&nbsp;&nbsp;&nbsp;
                    <button onclick="window.location = '../exportar/exportaExcelDoc1.php'" class="botonnormal">Exportar a Excel&nbsp;&nbsp;<img class='img' alt='exportar' title='Exportar a Excel los datos mostrados en pantalla' src='../img/excel3.png'></button>&nbsp;&nbsp;&nbsp;
                    <button id="capturar" class="botonnormal" onclick="window.open('../reportes/DocumentoPDF.php', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=960,height=640 left=150,top=20')">Generar Informe en PDF&nbsp;&nbsp;<img class="img" alt="pdf" title="Exporta los datos actuales a formato PDF" src="../img/pdf1.png"></button>&nbsp;&nbsp;&nbsp;
                    <button id="mail" class="botonnormal" onclick="window.open('mailto:?subject=Facturas%20pendientes%20Hospital%20san%20Borja%20Arriaran%20sin%20recepción%20conforme...URGENTE!!&body=Estimado/a:%0aJunto%20con%20saludar,%20le%20solicito%20favor%20adjuntar%20a%20la%20brevedad%20copia%20de%20los%20siguientes%20documentos%20firmados%20por%20el%20receptor%20de%20los%20productos,%20a%20fin%20de%20generar%20recepción%20conforme%20en%20nuestro%20sistema:%0A%0Axxxxxx%0ASe%20requiere%20Urgente.%20Muchas%20Gracias%20y%20quedo%20atento%20a%20sus%20comentarios.')">Generar mail&nbsp;&nbsp;<img class="img" alt="mail" title="Generar Plantilla email" src="../img/mail.png"></button>&nbsp;&nbsp;&nbsp;
                    <button type="button" onclick="window.location = 'buscarDocumento.php'" class="botonnormal">Limpiar&nbsp;&nbsp;<img class='img' alt='limpiar' title='Limpiar Pantalla' src='../img/clean.png'></button>
                </td>
            </tr>
        </table>
        <input type="hidden" id="rec" value="<?= $rec ?>">
        <div id="resultado"></div>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>