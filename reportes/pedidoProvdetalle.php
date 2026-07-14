<!DOCTYPE html>
<!-- fecha creacion 24-03-2019 mini hp
Sigbod John Vaccarella
-->
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
        <title>SigBod - Pedidos Por Proveedor</title>
    </head>
    <body class="fondo">
        <?php
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../index.php');
            exit();
        }
        if (!isset($_GET['rutpv'])) {
            echo '<script>alert("Error:\nLos datos seleccionados son incorrectos")</script>';
            echo "<script>javascript:history.back(-1)</script>";
            exit();
        } else {
            $rut = $_GET["rutpv"];
            $fecha_ini=$_GET["fini"];
            $fecha_fin=$_GET["ffin"];
        }
        if ($fecha_ini == "" or $fecha_fin == "") {
            $rango = "";
        } else {
            $rango = "AND mov_fecha BETWEEN '$fecha_ini' AND '$fecha_fin'";
        }
        include("../include/conn.php");
        $link = Conectarse(); //Variable de conexion
        $comilladob = '"';
        $comillasim = "'";
        $sql = "SELECT * FROM movimiento, proveedores, estado WHERE (mov_rutprv='$rut') AND prov_rut=mov_rutprv AND mov_estado=estado_id ".$rango. "ORDER BY mov_id DESC";
        $data = mysql_query($sql);
        $data1 = mysql_query($sql);
        $row1 = mysql_fetch_array($data1);
        $_SESSION['query'] = $sql;
        $contar = mysql_num_rows($data);
        if ($contar === 0) {
            echo '<script>alert("Error...\nNo hay datos para Mostrar...");history.back(-1);</script>';
            $_SESSION['query'] = "";
            exit();
        } else {
            echo '<table class="table2" align="center">';
            echo '<tr><td class="texto" colspan="10">Pedidos Generados por Proveedor</td></tr>';
            echo '<tr><td class="texto4" colspan="10"><img align="center" src="../img/info.png" width="20" height="20" title="Grilla de resultados"/>&nbsp;&nbsp;Mostrando todos los Registros. (se han encontrado: <b>' . $contar . '</b> Resultados)</td></tr>';
            echo '<tr><td colspan="3" class="texto8">RUT PROVEEDOR:</td><td colspan="7" class="texto9"><span class="texto6"></span><b>';
            echo "<a href=../edicion/modificarProveedor.php?id=" . $row1["prov_rut"] . "&option=1 target='_blank' title='Ver detalle proveedor' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=550, width=800 left=300 top=100" . $comillasim . "); return false;" . $comilladob . ">" . number_format($row1["prov_rut"], 0, '.', '.') . "-" . $row1["prov_dv"] . "</a>";
            echo '</b></td></tr>';
            echo '<tr><td colspan="3" class="texto8">RAZON SOCIAL:</td><td colspan="7" class="texto9"><span class="texto6"> </span>' . $row1["prov_nombre"] . '</td></tr>';
            echo '<tr><td colspan="3" class="texto8">NOMBRE CONTACTO:</td><td colspan="7" class="texto9"><span class="texto6"> </span>' . $row1["prov_nomcontacto"] . '</td></tr>';
            echo '<tr><td colspan="3" class="texto8">TELEFONO CONTACTO:</td><td colspan="7" class="texto9"><span class="texto6"> </span>' . $row1["prov_telefono"] . '</td></tr>';
            echo '<tr><td colspan="10" class="texto8c"></td></tr>';
            echo '<tr><th width="80">Folio</th><th width="90">Fecha Movimiento</th><th width="70">Articulos Solicitados</th><th>Valor Neto</th><th>Observaciones</th><th>Estado Solicitud</th><th width="50">ver PDF:</th></tr>';
            while ($row = mysql_fetch_array($data)) {
                $estado = $row["mov_estado"];
            if ($estado == "0") {
                $estado = " class='color_rojo'";
                $sumanul=$sumanul+$row["mov_valorneto"];
            } else {
                $estado = " class='texto5'";
            }
                printf("<tr><td><b>SOL-%s</b></td><td>%s</td><td><b>%s</b></td><td>%s</td><td>%s</td><td . $estado . >%s</td><td>%s</td></tr>", $row["mov_id"], date("d/m/Y H:i:s", strtotime($row["mov_fecha"])), $row["mov_cuenta"], '$' . number_format($row["mov_valorneto"], 0, ',', '.'), $row["mov_observacion"], $row["estado_nombre"], "<a href=../reportes/pedidoPDF.php?id=" . $row["mov_id"] . " target='_blank'><img border='0' alt='imprimir reporte' title='imprimir reporte pedido a: " . $row["prov_nombre"] . "' src='../img/pdf.png' width='30' height='28'></a>");
                $sum=$sum +$row["mov_valorneto"];
            }
            echo '<tr><td colspan="7" class="texto8f">Total Solicitado Neto:&nbsp;&nbsp;&nbsp;<b>$'.number_format($sum, 2, ',', '.').'</b></td></tr>';
            echo '<tr><td colspan="7" class="texto8f">Total Solicitado Anulado:&nbsp;&nbsp;&nbsp;<b>$'.number_format($sumanul, 2, ',', '.').'</b></td></tr>';
            echo '<tr><td colspan="7" class="texto8">Total Solicitado Vigente:&nbsp;&nbsp;&nbsp;<b>$'.number_format(($sum-$sumanul), 2, ',', '.').'</b></td></tr>';
            }
        mysql_close($link);
        ?>
    <tr><td colspan="7" class="texto6"><button class="botonnormal" onclick="javascript:history.back(-1)"/>Volver&nbsp;&nbsp;<img class='img' alt='volver' title='Volver a pantalla anterior' src='../img/undo.png'></button>&nbsp;&nbsp;&nbsp;<button class="botonnormal" onclick="window.location = '../principal.php'"/>Volver al Menú Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button onclick="window.location = '../exportar/exportaExcelMovProv.php'" class="botonnormal">Exportar a Excel&nbsp;&nbsp;<img class='img' alt='exportar' title='Exportar a Excel los datos mostrados en pantalla' src='../img/excel3.png'></button></td></tr>
    </table>   
    <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
</body>    
</html>
