<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
include("../include/conn.php");
$link = Conectarse();
$comilladob = '"';
$comillasim = "'";
$dia = $_POST["b"];
$dianum = date("j");
if($dia==$dianum){
    $fecha="HOY";
}else{
    $fecha=date($dia."/m/Y");
}
$sql = "SELECT * FROM productos, unimed WHERE prd_unimed=unimed_id AND prd_diarepo ='" . $dia . "' ORDER by prd_codigo ASC "; //seleccionar el ultimo registro de atras para adelante de la carga de saldos
$res = MySQL_query($sql, $link) or die(mysql_error());
$contar = mysql_num_rows($res);
if ($contar == 0) {
    echo "<table class='table2'>";
    echo "<tr><td colspan='5' class='texto2'><img align='center' src='../img/info.png' width='25' height='25' title='se muestran todos los productos que cumplen con los filtros.'/>&nbsp;&nbsp;Se han encontrado: " . $contar . " Registros.</td></tr>";
} else {
    echo "<table class='table2'>";
    echo "<tr><td colspan='5' class='texto2'><img align='center' src='../img/info.png' width='25' height='25' title='se muestran todos los productos que cumplen con los filtros.'/>&nbsp;&nbsp;Se han encontrado: " . $contar . " Registros.</td></tr>";
    echo '<tr><th width="80">Codigo Articulo</th><th width="350">Glosa</th><th width="90">Presentación</th><th width="100">Fecha de Reposicion del Producto</th><th width="100">Acciones:</th></tr>';
    echo "<tr>";
    while ($row = mysql_fetch_array($res)) {
        $diarepo = $row["prd_diarepo"];
        $modcod = "<a href=../edicion/ModificarArticulo.php?id=" . $row["prd_codigo"] . "&tipo=1 target='_blank' title='Editar Articulo' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=620, width=800 left=250 top=30" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='editar articulo' title='Editar articulo en ventana externa' src='../img/edit1.png' width='20' height='20'></a>";
        if ($diarepo == $dia) {
            printf("<tr><td><b>%s</b></td><td align='left'>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td></tr>", $row["prd_codigo"], $row["prd_glosa"], $row["unimed_nombre"], $fecha,"<a href=../pedidos/PedidoArticulo.php?codprd=" . $row["prd_codigo"] . "&op=1 target='_blank'><img border='0' alt='solicitar solo el articulo' title='Buscar Saldos' src='../img/pedido.png' width='20' height='22'></a>&nbsp;&nbsp;|&nbsp;&nbsp;$modcod");
        }
    }
}
mysql_close($link);
echo "</tr></table>";
?>
