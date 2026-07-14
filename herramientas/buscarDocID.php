<?php

include_once("../include/conn.php");
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
if (!isset($_POST['val'])) {
    exit;
}
$link = Conectarse();
$val = $_POST['val'];
$data = [];

$sql1 = "SELECT * FROM documento WHERE doc_id='" . $val . "'";
$data1 = mysql_query($sql1, $link);
$row1 = mysql_fetch_array($data1);
$contar1 = mysql_num_rows($data1);
if ($contar1 != 0) {
    $data["numdoc"] = $row1["doc_ndoc"];
    $data["doc_rut"] = $row1["doc_rut"];
    $rut = $row1["doc_rutpv"];
    $data["oc"] = $row1["doc_noc"];
    $data["tipo"] = $row1["doc_tipodoc"];
    $estado=$row1["doc_estado"];
    if($estado=="Anulado"){
        $data["est"] = "'A'";
    }else if($estado=="Recepcionado"){
        $data["est"] = "'R'";
    }else if($estado=="Devuelto"){
        $data["est"] = "'D'";
    }else{
        $data["est"] = "'P'";
    }    
}
$sql3 = "SELECT * FROM proveedores WHERE prov_rut='" . $rut . "'";
$data3 = mysql_query($sql3);
$row3 = mysql_fetch_array($data3);
$data["prov"] = $row3["prov_nombre"];
echo json_encode($data);
mysql_close($link);
?>