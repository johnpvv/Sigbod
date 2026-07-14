<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
include("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
$buscar = $_POST['b'];
if (!empty($buscar) and strlen($buscar) >= 3) {
    buscar($buscar);
}
function buscar($b) {
    $sql = "SELECT * FROM productos, unimed, estado  WHERE (prd_codigo LIKE '%" . $b . "%' or prd_glosa LIKE '%" . $b . "%'or prd_ref LIKE '%" . $b . "%' or prd_codcm LIKE '%" . $b . "%' or prd_cenabast LIKE '%" . $b . "%' or prd_barcode LIKE '%" . $b . "%') AND prd_unimed = unimed_id AND prd_estado=estado_id AND prd_estado='1'";
    $data = mysql_query($sql);
    $contar = mysql_num_rows($data);
    if ($contar === 0) {
        echo '<center><table class="table4"><tr><td colspan="7" class="texto4c">No se han encontrado resultados para: *<b>' . $b . '</b>*</td></tr></table>';
    } else {
        echo '<center><table class="table4"><tr><td colspan="7" class="texto8h">Se han encontrado <b>' . $contar . '</b> resultados para: *<b>' . $b . '</b>*</td></tr></table></center>';
	echo '<center><table class="table4" id="tabla">';
	echo '<thead><tr>';
        echo '<th width="80">C&oacute;digo Producto</th><th>Nombre Producto</th><th>Unidad Medida</th><th>Codigo de Barras</th><th>ID C.M.</th><th>C&oacute;digo Cenabast</th><th>Acciones:</th>';
        echo '</tr></thead>';
        while ($row = mysql_fetch_array($data)) {
            printf("<tr><td><b>%s</b></td><td align='left'>%s</td><td>%s</td><td>%s</td><td>%s</td><td><b>%s</b></td><td width='80'>%s</td></tr>", $row["prd_codigo"], $row["prd_glosa"], $row["unimed_nombre"], $row["prd_barcode"], $row["prd_codcm"], $row["prd_cenabast"], '<input type="button" value=" enviar " class="boton1b" onclick="cerrar(this.id);" id="'. $row["prd_codigo"] .'">');
        }
    }
    echo '</table></center>';
    echo '<script type="text/javascript">$(document).ready(function () {$("#tabla").tablesorter();});</script>';
}
mysql_close($link);
?>