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
                $("#tablamov").tablesorter();
            });
        </script>
        <title>SigBod - Solicitudes por OC</title>   
    </head>   
    <body class="fondo">
        <?php
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../index.php');
            exit();
        }
        if ($_GET['oc'] == "") {
            echo"<script>alert('Error, no hay datos para mostrar.');javascript:history.go(-1)</script>";
            exit();
        }
        include("../include/conn.php");
        $link = Conectarse(); //Variable de conexion
        $cod = $_GET['oc'];
        $op = $_GET['op'];
        $comilladob = '"';
        $comillasim = "'";
        $sql = "SELECT * FROM movimiento, detmovimiento, proveedores, tipomov, estado WHERE (detmov_oc='$cod') AND prov_rut=mov_rutprv AND mov_tipo=tipomov_id AND mov_estado=estado_id AND detmov_id=mov_id GROUP BY mov_id ORDER BY mov_id DESC";
        $res = MySQL_query($sql, $link)or die(mysql_error());
        $contar = mysql_num_rows($res);
        if ($contar == 0) {
            echo '<script>alert("La Orden de Compra seleccionada, no registra Solicitudes.");
                    window.close();</script>';
        }
        echo '<table class="table7" style="width:872px;">';
        echo '<tr><td colspan="10" class="texto">SOLICITUDES POR ORDEN DE COMPRA</td></tr>';
        echo '<tr><td colspan="10" class="texto4">N° de Solicitudes Encontradas: ' . $contar;
        echo '</table>';
        echo '<table class="table7" id="tablamov">';
        echo '<thead><tr><th width="60">N° Solicitud</th><th width="80">Tipo Movimiento</th><th width="80">Fecha Solicitud</th><th width="80">Orden de Compra</th><th width="200">Proveedor</th><th width="90">Valor Total Solicitud</th><th width="50">Estado Solicitud</th><th width="50" data-sorter="false" data-filter="false">Acciones:</th></tr></thead>';
        while ($row = mysql_fetch_array($res)) {
            $estado = $row["mov_estado"];
            if ($estado == "0") {
                $estado = " class='color_rojo'";
            } else {
                $estado = " class='texto5'";
            }
            printf("<tr><td><b>%s</b></td><td>%s</td><td>%s</td><td><b>%s</b></td><td align='left'><b>%s</b></td><td><b>%s</b></td><td" . $estado . ">%s</td><td>%s</td></tr>", "SOL-" . $row["mov_id"], $row["tipomov_nombre"], date("d/m/Y H:i:s", strtotime($row["mov_fecha"])), $row["detmov_oc"], "<a href=../edicion/modificarProveedor.php?id=" . $row["prov_rut"] . "&option=1 target='_blank' title='Ver detalle proveedor' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=560, width=800 left=300 top=100" . $comillasim . "); return false;" . $comilladob . ">" . number_format($row["prov_rut"], 0, '.', '.') . "-" . $row["prov_dv"] . "</a>&nbsp;" . " " . $row["prov_nombre"], '$' . number_format($row["mov_valorneto"] * 1.19, 0, '.', ','), $row["estado_nombre"], "<a href=../reportes/pedidoPDF.php?id=" . $row["mov_id"] . " target='_blank'><img border='0' alt='imprimir reporte' title='imprimir reporte pedido a: " . $row["prov_nombre"] . "' src='../img/pdf.png' width='30' height='28'></a>");
        }
        echo '</table><table class="table7" style="width:872px;">';
        echo '<tr><td colspan="10" class="texto8a"></td></tr>';
        echo '<tr><td colspan="10" class="texto5"><center><button class="boton" type="button" onclick="window.close()">Salir&nbsp;<img class="img" alt="principal" title="Salir" src="../img/salir.png"></button>';
        echo '</table>';
        mysql_close($link);
        ?>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>