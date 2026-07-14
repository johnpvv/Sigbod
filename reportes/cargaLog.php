<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<!--
Sigbod John Vaccarella creacion 04/09/2019
-->
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
            $(document).ready(function () {
                $("#tipo, #fecha, #tipoarch").change(function () {//comprobamos si se pulsa una tecla
                    var consulta, anio, consulta1;
                    consulta = $("#tipo").val(); //obtenemos el texto introducido en el campo de busqueda
                    anio = $("#fecha").val();
                    consulta1 = $("#tipoarch").val();
                    if (consulta === '') {
                        $("#resultado").empty();
                    } else {
                        $.ajax({//hace la busqueda
                            type: "POST",
                            url: "cargaLogAjax.php",
                            data: {b: consulta, anio: anio, c:consulta1},
                            beforeSend: function () {
                                $.blockUI({
                                    message: "<p class='centrar_cuadro_alertas'>Actualizando, por favor espere...<img src='../img/loading.gif' width='200' height='150' /></p>"
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
                });
            });
        </script>
        <title>SigBod - Registro de Carga</title> 
    </head>
    <body class="fondo">
        <?php
        include("../include/conn.php");
        $link = Conectarse(); //Variable de coneccion
        $fechaact = date("Y-m-d");
        $fechatope = date("Y-m-d", strtotime("2019-01-01"));
        $sql = "SELECT * FROM `carga_log` GROUP BY carga_nombre";
        $data = mysql_query($sql);
        $sql1 = "SELECT * FROM `carga_log` GROUP BY carga_tipo";
        $data1 = mysql_query($sql1);
        ?>       
        <table class="table2a">
            <tr>
                <td class="texto">Registro de carga de Archivos al Sistema</td>
            </tr>
            <tr>
                <td class="texto5a">
                    Tipo de Carga:
                    <select name="tipo" class="caja" id="tipo">
                        <option value='' selected>‌Elija una Opción</option>
                        <?php
                        $x = 0;
                        while ($row = mysql_fetch_array($data)) {
                            echo"<option value='" . $row["carga_nombre"] . "'>" . $row["carga_nombre"] . "</option>";
                            $x++;
                        }
                        ?>                            
                    </select>&nbsp;&nbsp;&nbsp;&nbsp;
                    Tipo Archivo:
                    <select name="tipoarch" class="caja" id="tipoarch">
                        <option value='' selected>‌Elija una Opción</option>
                        <?php
                        $y = 0;
                        while ($row1 = mysql_fetch_array($data1)) {
                            echo"<option value='" . $row1["carga_tipo"] . "'>" . $row1["carga_tipo"] . "</option>";
                            $y++;
                        }
                        ?>                            
                    </select>&nbsp;&nbsp;&nbsp;&nbsp;
                    Fecha de Corte:
                    <input type="date" class='caja' name="fecha" id="fecha" step="1" min="<?= $fechatope ?>" max="<?= $fechaact ?>" value="<?= $fechaact ?>" required />
                </td>
            </tr>
            <tr>
                <td class = "texto6"><button class = "botonnormal" onclick = "window.location = '../principal.php'">Volver al Menú Principal&nbsp;<img class = "img" alt = "principal" title = "volver al menu principal" src = "../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button onclick = "window.location = '../exportar/exportaCargaLog.php'" class = "botonnormal">Exportar a Excel (pendiente)&nbsp;&nbsp;<img class = 'img' alt = 'exportar' title = 'Exportar a Excel los datos mostrados en pantalla' src = '../img/excel3.png'></button></td>
            </tr>
        </table>
        <?php
        mysql_free_result($data);
        mysql_close($link);
        ?>
    <div id = "resultado"></div>
    <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
</body>
</html>