<?php
session_start();
include("include/conn.php");
$link = Conectarse();
$user=$_SESSION["usuario"];
//echo $user;
$sql1 = "UPDATE usuario_log SET usuario_activo='0' WHERE usuario_rut='$user'";
mysql_query($sql1, $link);
session_destroy();
mysql_close($link);
header('Location: index.php');
exit;
