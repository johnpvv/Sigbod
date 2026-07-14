<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
include("../include/conn.php");
$link = Conectarse(); //Variable de conexion
$b = $_POST['b'];
$chk = $_POST['chkall'];
$chkrev = $_POST['chkrev'];

if ($chkrev == "true") {
    $sql = "SELECT * FROM grupo_productos WHERE grupo_prd_rev='1' ORDER BY grupo_prd_id";
    $varsql = " grupo_prd_rev='1' AND ";
} else {
    $sql = "";
    $varsql = "";
}
if ($chk == "true") {
    $sql = "SELECT * FROM grupo_productos ORDER BY grupo_prd_id";
} else {
}
if (!empty($b)) {
    $sql = "SELECT * FROM grupo_productos WHERE " . $varsql . " (grupo_prd_id LIKE '%" . $b . "%' OR grupo_prd_nombre LIKE '%" . $b . "%' OR grupo_prd_descrip LIKE '%" . $b . "%') ORDER BY grupo_prd_id";
}
if ($sql != "") {
    $result = mysql_query($sql, $link);
    $contar = mysql_num_rows($result);
    $_SESSION['query'] = $sql;
    if ($contar === 0) {
        echo '<table class="table2a">';
        echo '<tr><td class="texto8b" colspan="6">No Se han Encontrado Registros que contengan: ' . $b . '</td></tr>';
        echo'</table>';
    } else {
        echo '<table class="table2a" id="tabladoc">';
        echo '<thead><tr>
            <th width="100">ID grupo</th>
            <th width="280">Nombre Grupo</th>
            <th>Decripción</th>
            <th width="80">N° Articulos</th>
            <th>Fecha de Creaci&oacute;n</th>
            <th>Nivel Stock Grupo</th>
            <th data-sorter="false" data-filter="false" width="80">Acciones</th> 
            </tr></thead>';
        while ($row = mysql_fetch_array($result)) {
            $tiporev=$row["grupo_prd_rev"];
            if($tiporev==1){
                $stock='<b style = "color:red; font-size: 13px;">Niveles Insuficientes</b>';
            }else{
                $stock='<b style = "color:blue; font-size: 13px;">Stock Normal</b>';
            }
            printf("<tr><td><b>%s</b></td><td><b>%s<b></td><td>%s</td><td>%s</td><td>%s</td><td><b>%s</b></td><td align='center'>%s</td></tr>", $row["grupo_prd_id"], $row["grupo_prd_nombre"], $row["grupo_prd_descrip"], $row["grupo_prd_cuenta"], date("d/m/Y H:i:s", strtotime($row["grupo_prd_fechacrea"])),$stock ,"<a href=../edicion/crearGrupo.php?id=" . $row["grupo_prd_id"] . ">
            <img border='0' alt='editar grupo' title='Editar grupo: " . $row["grupo_prd_id"] . "' src='../img/edit1.png' width='25' height='25'></a>&nbsp;|&nbsp;<a href=../herramientas/stockGrupo.php?id=" . $row["grupo_prd_id"] . "><img border='0' alt='stock grupo' title='stock grupo: " . $row["grupo_prd_nombre"] . "' src='../img/ped1.png' width='25' height='25'></a>");
        }
        echo'</table>';
        echo '<table class="table2a">';
        echo '<tr><td class="texto8b" colspan="9">Se han Encontrado: ' . $contar . ' Registros.</td></tr>';
        echo'</table>';
        echo '<script type="text/javascript">$(document).ready(function () {$("#tabladoc").tablesorter();});</script>';
    }
    mysql_free_result($result);
    mysql_close($link);
}
?>

