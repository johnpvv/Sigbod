<?php
//archivo busca y obtine la ultima devolucion de cocumentos realizada, para agregar una nueva automaticamente
include_once("../include/conn.php");
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
if (!isset($_POST['clave'])) {
    exit;
}

$link = Conectarse();
$val = $_POST['clave'];
$data = [];

    $sql2 = "SELECT subdoc_numdoc, subdoc_ai, subdoc_tipo, subdoc_rutpv FROM subdocumento WHERE subdoc_tipo='Devolucion' AND subdoc_rutpv='".$val."' ORDER BY subdoc_ai DESC LIMIT 1";
    $data2 = mysql_query($sql2, $link);
    $row2 = mysql_fetch_array($data2);
    $data["numdoc"] = $row2["subdoc_numdoc"] +1;

echo json_encode($data);
mysql_close($link);


?>