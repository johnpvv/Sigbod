<?php

include_once("../include/conn.php");
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}

$link = Conectarse();
$fecha_ini = date("Y-01-01 00:00:00");
$fecha_fin = date("Y-12-31 23:59:59");

$sql = 'SELECT count(doc_id) AS Total, doc_estado FROM sigbod.documento WHERE doc_fechadoc BETWEEN "' . $fecha_ini . '" AND "' . $fecha_fin . '" GROUP BY doc_estado';

$data = mysql_query($sql, $link);
while ($row = mysql_fetch_array($data)) {
    if ($row["doc_estado"] == "") {
        $sql1 = 'SELECT doc_obs, doc_estado, count(doc_id) AS Total1 FROM sigbod.documento WHERE doc_fechadoc BETWEEN "' . $fecha_ini . '" AND "' . $fecha_fin . '" AND doc_obs LIKE "%reclam%" GROUP BY doc_estado';
        $data1 = mysql_query($sql1, $link);
        $row1 = mysql_fetch_array($data1);
        $totalrec = $row1["Total1"];
        $totalpend = $row["Total"] - $totalrec;
        $estado = "Pendiente";
        $total= $totalpend;        
    } else {
        $estado = $row["doc_estado"];
        $total= $row["Total"];
    }
    $dato1[] = array("valor" => $total, "nombre" => $estado);
}
$dato1[] = array("valor" => $totalrec, "nombre" => "Reclamadas");
echo json_encode($dato1);
mysql_close($link);
?>