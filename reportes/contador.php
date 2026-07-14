<!DOCTYPE html>
<!-- fecha creacion 22/10/2019 hp
Sigbod John Vaccarella
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
include ("../include/conn.php");
$link = Conectarse();
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
        <title>SigBod - Contador de Datos</title>
    </head>
    <body class="fondo">

        <?php
        $comilladob = '"';
        $comillasim = "'";

        $sql1 = "SELECT * FROM productos";
        $result1 = mysql_query($sql1, $link);
        $contar1 = mysql_num_rows($result1);

        $sql1a = "SELECT prd_codigo FROM productos ORDER by prd_fecha DESC LIMIT 1";
        $result1a = mysql_query($sql1a, $link);
        $row1a = mysql_fetch_array($result1a);
        $cod = $row1a["prd_codigo"];
        $codlink = "<a href=../edicion/modificarArticulo.php?id=" . $cod . "&tipo=1 target='_blank' title='Ver detalle Articulo' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=620, width=800 left=300 top=20" . $comillasim . "); return false;" . $comilladob . ">" . $cod . "</a>";

        $sql2 = "SELECT * FROM productos WHERE prd_destacado='1'";
        $result2 = mysql_query($sql2, $link);
        $contar2 = mysql_num_rows($result2);

        $sql3 = "SELECT * FROM proveedores";
        $result3 = mysql_query($sql3, $link);
        $contar3 = mysql_num_rows($result3);

        $sql4 = "SELECT * FROM proveedores WHERE prov_estado='1'";
        $result4 = mysql_query($sql4, $link);
        $contar4 = mysql_num_rows($result4);

        $sql5 = "SELECT * FROM stock";
        $result5 = mysql_query($sql5, $link);
        $contar5 = mysql_num_rows($result5);

        $sql6 = "SELECT * FROM stock WHERE stock_cantidad > 0";
        $result6 = mysql_query($sql6, $link);
        $contar6 = mysql_num_rows($result6);

        $sql7 = "SELECT * FROM stock WHERE stock_cantidad = 0";
        $result7 = mysql_query($sql7, $link);
        $contar7 = mysql_num_rows($result7);

        $sql8 = "SELECT SUM(stock_cantidad) as total FROM stock";
        $result8 = mysql_query($sql8, $link);
        $row8 = mysql_fetch_array($result8);
        $contar8 = $row8["total"]; //mostar el total calculado de la suma del stock

        $sql9 = "SELECT * FROM saldos";
        $result9 = mysql_query($sql9, $link);
        $contar9 = mysql_num_rows($result9);

        $sql10 = "SELECT COUNT(DISTINCT saldo_codigo) as total FROM saldos";
        $result10 = mysql_query($sql10, $link);
        $row10 = mysql_fetch_array($result10);
        $contar10 = $row10["total"]; //mostar cuenta de articulos con oc sin duplicados

        $sql10a = "SELECT COUNT(DISTINCT saldo_oc) as total FROM saldos";
        $result10a = mysql_query($sql10a, $link);
        $row10a = mysql_fetch_array($result10a);
        $contar10a = $row10a["total"]; //mostar cuenta de oc sin duplicados

        $sql11 = "SELECT SUM(saldo_pendiente) AS total, SUM(saldo_solicitado) AS total11 FROM saldos";
        $result11 = mysql_query($sql11, $link);
        $row11 = mysql_fetch_array($result11);
        $contar11 = $row11["total"]; //mostar el total calculado de la suma del los pendientes
        $suma11 = $row11["total11"];
        $sumafinal = ($contar11 / $suma11) * 100;

        $sql12 = "SELECT * FROM documento";
        $result12 = mysql_query($sql12, $link);
        $contar12 = mysql_num_rows($result12);

        $sql13 = "SELECT * FROM documento WHERE doc_estado!=''";
        $result13 = mysql_query($sql13, $link);
        $contar13 = mysql_num_rows($result13);

        $sql13a = "SELECT * FROM subdocumento";
        $result13a = mysql_query($sql13a, $link);
        $contar13a = mysql_num_rows($result13a);

        $sql14 = "SELECT * FROM carga_log WHERE carga_nombre!='Borrado'";
        $result14 = mysql_query($sql14, $link);
        $contar14 = mysql_num_rows($result14);

        $sql15 = "SELECT SUM(carga_cuenta) as total FROM carga_log";
        $result15 = mysql_query($sql15, $link);
        $row15 = mysql_fetch_array($result15);
        $contar15 = $row15["total"]; //mostar el total calculado de la suma del stock

        $sql16 = "SELECT * FROM usuarios";
        $result16 = mysql_query($sql16, $link);
        $contar16 = mysql_num_rows($result16);

        $sql17 = "SELECT * FROM movimiento";
        $result17 = mysql_query($sql17, $link);
        $contar17 = mysql_num_rows($result17);

        $sql18 = "SELECT * FROM detmovimiento";
        $result18 = mysql_query($sql18, $link);
        $contar18 = mysql_num_rows($result18);

        mysql_close($link);
        ?>
        <table class="table3" align="center">
            <tr><td colspan="2" class="texto">ESTADÍSTICAS DEL SISTEMA</td></tr>
            <tr><td class="texto8i">ARTÍCULOS REGISTRADOS:</td><td class="texto9"><span class="texto6"></span><b><?= number_format($contar1, 0, '.', '.') ?></b>&nbsp;(Ultimo codigo modificado: <?= $codlink ?>)</td></tr>
            <tr><td class="texto8i">CUENTA DE ARTÍCULOS CON REVISION DE STOCK:</td><td class="texto9"><span class="texto6"></span><b><?= number_format($contar2, 0, '.', '.') ?></b></td></tr>
            <tr><td class="texto8i">CUENTA DE PROVEEDORES:</td><td class="texto9"><span class="texto6"></span><b><?= number_format($contar3, 0, '.', '.') ?></b></td></tr>
            <tr><td class="texto8i">PROVEEDORES VIGENTES:</td><td class="texto9"><span class="texto6"></span><b><?= number_format($contar4, 0, '.', '.') ?></b></td></tr>
            <tr><td class="texto8i">PRODUCTOS EN STOCK:</td><td class="texto9"><span class="texto6"></span><b><?= number_format($contar5, 0, '.', '.') ?></b></td></tr>
            <tr><td class="texto8i">ARTICULOS CON STOCK MAYOR A CERO:</td><td class="texto9"><span class="texto6"></span><b><?= number_format($contar6, 0, '.', '.') ?></b></td></tr>
            <tr><td class="texto8i">ARTICULOS CON STOCK IGUAL A CERO:</td><td class="texto9"><span class="texto6"></span><b><?= number_format($contar7, 0, '.', '.') ?></b></td></tr>
            <tr><td class="texto8i">SUMA DE UNIDADES EN STOCK:</td><td class="texto9"><span class="texto6"></span><b><?= number_format($contar8, 0, '.', '.') ?></b></td></tr>
            <tr><td class="texto8i">TOTAL DE ITEMS SALDO EN OC:</td><td class="texto9"><span class="texto6"></span><b><?= number_format($contar9, 0, '.', '.') ?></b></td></tr>
            <tr><td class="texto8i">TOTAL DE ARTICULOS CON SALDO EN OC:</td><td class="texto9"><span class="texto6"></span><b><?= number_format($contar10, 0, '.', '.') ?></b></td></tr>
            <tr><td class="texto8i">TOTAL DE ORDENES DE COMPRA CON SALDOS:</td><td class="texto9"><span class="texto6"></span><b><?= number_format($contar10a, 0, '.', '.') ?></b></td></tr>
            <tr><td class="texto8i">SUMA TOTAL DE UNIDADES PENDIENTES EN OC:</td><td class="texto9"><span class="texto6"></span><b><?= number_format($contar11, 0, '.', '.') ?></b> <?= "(" . number_format($sumafinal, 2, ',', '.') . "%) del total ordenado." ?></td></tr>
            <tr><td class="texto8i">DOCUMENTOS INGRESADOS:</td><td class="texto9"><span class="texto6"></span><b><?= number_format($contar12, 0, '.', '.') ?></b></td></tr>
            <tr><td class="texto8i">DOCUMENTOS REGULARIZADOS:</td><td class="texto9"><span class="texto6"></span><b><?= number_format($contar13, 0, '.', '.') ?></b></td></tr>
            <tr><td class="texto8i">SUB-DOCUMENTOS REGISTRADOS:</td><td class="texto9"><span class="texto6"></span><b><?= number_format($contar13a, 0, '.', '.') ?></b></td></tr>
            <tr><td class="texto8i">ARCHIVOS CARGADOS:</td><td class="texto9"><span class="texto6"></span><b><?= number_format($contar14, 0, '.', '.') ?></b></td></tr>
            <tr><td class="texto8i">REGISTROS MODIFICADOS CON CARGA DE ARCHIVOS:</td><td class="texto9"><span class="texto6"></span><b><?= number_format($contar15, 0, '.', '.') ?></b></td></tr>
            <tr><td class="texto8i">USUARIOS REGISTRADOS EN EL SISTEMA:</td><td class="texto9"><span class="texto6"></span><b><?= number_format($contar16, 0, '.', '.') ?></b></td></tr>
            <tr><td class="texto8i">PEDIDOS GENERADOS:</td><td class="texto9"><span class="texto6"></span><b><?= number_format($contar17, 0, '.', '.') ?></b></td></tr>
            <tr><td class="texto8i">CANTIDAD DE ARTICULOS SOLICITADOS:</td><td class="texto9"><span class="texto6"></span><b><?= number_format($contar18, 0, '.', '.') ?></b></td></tr>
            <tr><td class="texto5" colspan="2"><center><button class="botonnormal" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="botonnormal" onclick="location.reload();">Actualizar&nbsp;<img class="img" alt="recargar" title="recargar" src="../img/reload.png"></button></center></td></tr>
</table>
<div id="resultado"></div>
<a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
</body>
</html>