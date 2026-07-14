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
        <title>SigBod - Indicadores Economicos Historicos</title>   
    </head>   
    <body class="fondo">
        <?php
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../index.php');
            exit();
        }
        $anio = $_GET["anio"];
        $fecha = date("$anio-01-01");
        $fecha2 = date("$anio-12-31");
        include("../include/conn.php");
        $link = Conectarse(); //Variable de conexion
        $comilladob = '"';
        $comillasim = "'";
        $sql = "SELECT * FROM indicador WHERE ind_fecha BETWEEN '$fecha' AND '$fecha2' ORDER BY ind_fecha DESC";
        $res = MySQL_query($sql, $link)or die(mysql_error());
        $contar = mysql_num_rows($res);
        if ($contar == 0) {
            echo '<script>alert("No existen registros para mostrar.");
                    history.back(-1)</script>';
        }
        echo '<table class="table5" style="width:800px;">';
        echo '<tr><td colspan="5" class="texto">Histórico Indicadores Económicos</td></tr>';
        echo '<tr><td colspan="5" class="texto4">N° de registros Encontrados: ' . $contar;
        echo '</table>';
        echo '<table class="table7" id="tablamov" style="width:800px;">';
        echo '<thead><tr><th width="60">Fecha Indicador</th><th width="100">Valor Dólar</th><th width="100">Valor Euro</th><th width="100">Valor U.F.</th><th width="100">Valor U.T.M.</th></thead>';
        while ($row = mysql_fetch_array($res)) {
            $estado = $row["estado_nombre"];
            printf("<tr><td><b>%s</b></td><td><b>$%s</b></td><td><b>$%s</b></td><td><b>$%s</b></td><td><b>$%s</b></td></tr>", date("d/m/Y", strtotime($row["ind_fecha"])), $row["ind_dolar"], $row["ind_euro"], $row["ind_uf"], $row["ind_utm"]);
        }
        echo '</table><table class="table7" style="width:800px;">';
        echo '<tr><td colspan="5" class="texto8a"></td></tr>';
        echo '<tr><td colspan="5" class="texto5"><center><button class="boton" type="button" onclick="history.back(-1);">Volver&nbsp;<img class="img" alt="principal" title="Volver" src="../img/undo.png"></button>';
        echo '</table>';
        mysql_close($link);
        ?>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>