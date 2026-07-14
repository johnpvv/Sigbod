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
$chkest = $_POST['chkest'];
$fecha = strtotime(date("Y-m-d"));
$date = date("Y-m-d");

if ($chkest == "true") {
    $est = "";
} else {
    $est = " AND seg_estado=1 ";
}

if ($chkrev == "true") {
    $fven = " AND seg_fven <= '$date' ";
} else {
    $fven = "";
}

if ($chk == "true") {
    $sql = "SELECT * FROM seguimiento, usuarios WHERE seg_resp=usuario_id" . $fven . $est . " ORDER BY seg_id DESC";
}
if (!empty($b)) {
    $sql = "SELECT * FROM seguimiento, usuarios WHERE seg_resp=usuario_id " . $fven . $est . " AND (seg_id LIKE '%" . $b . "%' OR seg_nombre LIKE '%" . $b . "%' OR seg_descrip LIKE '%" . $b . "%') ORDER BY seg_id DESC";
}
if ($sql != "") {
    $result = mysql_query($sql, $link);
    $contar = mysql_num_rows($result);
    $_SESSION['query'] = $sql;
    //echo $sql;
    if ($contar === 0) {
        echo '<table class="table2a">';
        echo '<tr><td class="texto8b" colspan="6">No Se han Encontrado Registros con los Filtros Seleccionados.</td></tr>';
        echo'</table>';
    } else {
        echo '<table class="table2a" id="tabladoc">';
        echo '<thead><tr>
            <th width="80">ID Seguimiento</th>
            <th width="80">Nombre Seguimiento</th>
            <th width="350">Descripción</th>
            <th width="80">N° Documentos</th>
            <th width="80">Fecha de Modificaci&oacute;n</th>
            <th width="80">Fecha de Vencimiento</th>
            <th width="100">Responsable</th>
            <th width="50">Estado</th>
            <th data-sorter="false" data-filter="false" width="50">Acciones</th> 
            </tr></thead>';
        while ($row = mysql_fetch_array($result)) {
            $venc = strtotime($row["seg_fven"]);
            $estado = $row["seg_estado"];
            if ($estado == 0) {
                $fechaven = "<img class='imgcopy' src='../img/error.png' title='Seguimiento Cerrado'>";
            } else {
                if ($venc <= $fecha) {
                    $fechaven = '<b style = "color:red; font-size: 13px;">Seguimiento Vencido (' . date("d/m/Y", $venc) . ')</b>';
                } else {
                    $fechaven = '<b style = "color:blue; font-size: 13px;">Seguimiento Pendiente (' . date("d/m/Y", $venc) . ')</b>';
                }
            }
            if ($estado == 1) {
                $estado = "Activo";
            } else {
                $estado = "Inactivo";
            }
			$descrip= "-".str_replace("\n","<br>-",$row["seg_descrip"]);
            printf("<tr><td><b>%s</b></td><td><b>%s<b></td><td style='text-align:left;'><i>%s</i></td><td>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td><td><b>%s</b></td><td align='center'>%s</td></tr>", $row["seg_id"], $row["seg_nombre"], $descrip, $row["seg_cant"], date("d/m/Y H:i:s", strtotime($row["seg_fcrea"])), $fechaven, $row["usuario_nombre"] . ' ' . $row["usuario_apellidos"], $estado, "<a href=../edicion/crearSeguimiento.php?idseg=" . $row["seg_id"] . ">
            <img border='0' alt='editar seg' title='Editar seg: " . $row["seg_id"] . "' src='../img/edit1.png' width='25' height='25'></a>");
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

