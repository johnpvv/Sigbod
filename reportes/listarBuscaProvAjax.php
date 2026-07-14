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
function buscar($saldo) {
    $sql = "SELECT * FROM proveedores WHERE (prov_rut LIKE '%" . $saldo . "%' or prov_nombre LIKE '%" . $saldo . "%'or prov_giro LIKE '%" . $saldo . "%'or prov_nomcontacto LIKE '%" . $saldo . "%') ORDER BY prov_nombre";
    $data = mysql_query($sql);
    $contar = mysql_num_rows($data);
    if ($contar === 0) {
        echo '<center><table class="table4"><tr><td colspan="7" class="texto4c">No se han encontrado resultados para: *<b>' . $saldo . '</b>*</td></tr></table>';
    } else {
        echo '<center><table class="table4"><tr><td colspan="7" class="texto8h">Se han encontrado <b>' . $contar . '</b> resultados para: *<b>' . $saldo . '</b>*</td></tr></table></center>';
	echo '<center><table class="table4" id="tabla">';
	echo '<thead><tr>';
        echo '<th width="80">RUT</th><th width="250">Razon Social</th><th width="100">Giro</th><th width="80">Contacto</th><th>Estado</th><th data-sorter="false" data-filter="false">Acciones:</th>';
        echo '</tr></thead>';
        while ($row = mysql_fetch_array($data)) {
            $estado = $row["prov_estado"];
            if ($estado == "0") {$estado = "No Vigente";} 
            else {$estado = "Vigente";}
            printf("<tr><td><b>%s</b></td><td align='left'>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td><td><b>%s</b></td></tr>", number_format($row["prov_rut"], 0, '.', '.') . "-" . $row["prov_dv"], $row["prov_nombre"], $row["prov_giro"], $row["prov_nomcontacto"], $estado, '<input type="button" value=" enviar " class="boton1b" onclick="cerrar(this.id);" id="'. $row["prov_rut"] .'">');
        }
    }
    echo '</table>';
    echo '<script type="text/javascript">$(document).ready(function () {$("#tabla").tablesorter();});</script>';
}
mysql_close($link);
?>