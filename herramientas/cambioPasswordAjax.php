<?php

session_start();
include("../include/conn.php");
$link = Conectarse();

$user = $_SESSION['usuario'];
$pass = md5($_POST["key"]);
$sql = "select * from usuarios where usuario_rut='$user' and usuario_password='$pass'";

$res = MySQL_query($sql, $link);
$row = MySQL_Fetch_array($res);

$user1 = $row["usuario_rut"];
$pass1 = $row["usuario_password"];
$estado = $row["usuario_estado"];
if ($user1 == "") {
    echo "<script>alert('Contraseña Incorrecta!');location.href='cambiopassword.php'</script>";
    $_SESSION["cambiar"]=0;
} else if ($estado == 0) {
    echo "<script>alert('Usuario Inactivo...')</script>";
} else {
    if ($user == $user1 and $pass == $pass1) {
        echo"<script>location.href='../edicion/cambiopwbd.php'</script>";
        $_SESSION["cambiar"]=1;
    } else {
        echo"<script>alert('Contraseña Incorrecta...');location.href='cambiopassword.php'</script>";
    }
}
mysql_close($link);
?>
