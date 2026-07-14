<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
include("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
$buscar = $_POST['b'];
$subdoc = $_POST["chksub"];
if ($subdoc != "") {
    if($subdoc=="1"){
        $subdoc = " AND doc_estado='' ";    
    }else{
        $subdoc = " AND doc_estado='$subdoc' ";
    }
}else {
    $subdoc = "";
}
if (!empty($buscar) and strlen($buscar) >= 3) {
    buscar($buscar,$subdoc);
}
function buscar($saldo,$subdoc) {
    $sql = "SELECT * "
                . "FROM proveedores, documento "
                . "WHERE doc_rutpv=prov_rut ". $subdoc 
                . "AND (prov_nombre LIKE '%" . $saldo . "%' or doc_id LIKE '%" . $saldo . "%' or prov_rut LIKE '%" . $saldo . "%' or doc_ndoc LIKE '%" . $saldo . "%' or doc_noc LIKE '%" . $saldo . "%' or doc_obs LIKE '%" . $saldo . "%' or doc_origen LIKE '%" . $saldo . "%' or doc_estado LIKE '%" . $saldo . "%' or concat_ws(' ', doc_origen, doc_norigen) LIKE '%" . $saldo . "%')"
                . "ORDER BY doc_id DESC";
    $data = mysql_query($sql);
    $contar = mysql_num_rows($data);
    if ($contar === 0) {
        echo '<center><table class="table4"><tr><td colspan="7" class="texto4c">No se han encontrado resultados para: *<b>' . $saldo . '</b>*</td></tr></table>';
    } else {
        echo '<center><table class="table4"><tr><td colspan="7" class="texto8h">Se han encontrado <b>' . $contar . '</b> resultados para: *<b>' . $saldo . '</b>*</td></tr></table></center>';
	echo '<center><form name="form"><table class="table4" id="tabla">';
	echo '<thead><tr>';
        echo '<th width="30">ID</th><th width="70">RUT</th><th width="250">Razon Social</th><th width="50">N°DOC</th><th width="80">Tipo Doc</th><th width="70">Estado</th><th data-sorter="false" data-filter="false">Todo<input type="checkbox" name="chktodo" id="chktodo" onclick="selectall(document.forms[0])" /></th>';
        echo '</tr></thead>';
        $i=0;
        while ($row = mysql_fetch_array($data)) {
            $estado = $row["doc_estado"];            
        if ($estado == "") {$estado = "Pendiente"; $stylo="style=color:red;font-weight:600;";}else{ $stylo="";}
            printf("<tr><td><b>%s</b></td><td>%s</td><td align='left'>%s</td><td><b>%s</b></td><td>%s</td><td $stylo>%s</td><td><b>%s</b></td></tr>", $row["doc_id"],number_format($row["prov_rut"], 0, '.', '.') . "-" . $row["prov_dv"], $row["prov_nombre"], $row["doc_ndoc"], $row["doc_tipodoc"], $estado, '<input type="checkbox" name="'. $row["doc_id"] .'" id="check_'. $i .'">');//<input type="button" value=" enviar " class="boton1b" onclick="cerrar(this.id);" id="'. $row["doc_id"] .'">
        
         $i++;   
        }
    }
    echo '</table></form></center>';
    echo '<script type="text/javascript">$(document).ready(function () {$("#tabla").tablesorter();});</script>';
}
mysql_close($link);
?>