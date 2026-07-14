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

$sql1 = "SELECT * FROM documento WHERE doc_clave='" . $val . "'";
$data1 = mysql_query($sql1, $link);
$row1 = mysql_fetch_array($data1);
$contar1 = mysql_num_rows($data1);
if ($contar1 != 0) {
    $data["numdoc"] = $row1["doc_ndoc"];
    $data["id"] = $row1["doc_id"];
    $rut = $row1["doc_rutpv"];
} else {
    $sql2 = "SELECT * FROM subdocumento WHERE subdoc_clave='" . $val . "'";
    $data2 = mysql_query($sql2, $link);
    $row2 = mysql_fetch_array($data2);
    $data["numdoc"] = $row2["subdoc_numdoc"];
    $data["id"] = $row2["subdoc_id"];
    $rut = $row2["subdoc_rutpv"];
}
$sql3 = "SELECT * FROM proveedores WHERE prov_rut='" . $rut . "'";
$data3 = mysql_query($sql3);
$row3 = mysql_fetch_array($data3);
$data["prov"] = $row3["prov_nombre"];
echo json_encode($data);
mysql_close($link);
?>