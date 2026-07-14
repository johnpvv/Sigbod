<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
include("../include/conn.php");
include("../PHPMailer/generarMail.php");
$link = Conectarse();
$id = $_POST["txtId"];
$cod = $_POST["txtrut"];
$nom = strtoupper(trim($_POST["txtnombre"]));
$ap = strtoupper(trim($_POST["txtapellido"]));
$fono = trim($_POST["txtfono"]);
$mail1 = strtolower(trim($_POST["txtmail"]));
$instit = strtoupper($_POST["txtinstitucion"]);
$fnac = $_POST["anio"];
$perfil = number_format($_POST["txtperfil"]);
$estado = number_format($_POST["txtestado"]);
$dir = ucwords(strtolower($_POST["dir"]));
$fecha = date("Y-m-d H:i:s");

$sql = "UPDATE usuarios SET usuario_nombre ='$nom', usuario_apellidos='$ap',usuario_telefono='$fono',usuario_email='$mail1',usuario_institucion='$instit', usuario_fechaNac='$fnac' , usuario_perfil='$perfil', usuario_direccion='$dir',usuario_estado='$estado' WHERE usuario_rut='$cod'";
MySQL_query($sql, $link)or die(mysql_error());
$contar = mysql_affected_rows();
if ($contar == 0) {
    echo '<script>alert("Los Datos ingresados para Actualizar son inválidos...");history.go(-1);</script>';
} else {
    echo '<script>alert("Los Datos se han Actualizado Correctamente.");history.go(-1);</script>';
    $mail->ClearAddresses();
    $mail->Subject = "Modificacion Datos de Usuario";
    $mail->Body = "<b>Estimado(a) " . $nom . " " . $ap . ":</b><br> 
            Se han Modificado sus datos de registro en sistema Sigbod.<br>Si Usted no ha Realizado Alguna Modificacion, Favor Contacte al Administrador.
            <br><br>Mensaje Generado Automaticamente por Sistema Sigbod.";
    $mail->AddAddress($mail1);
    if (!$mail->Send()) {
        mysql_query("INSERT INTO mail (mail_doc,mail_user,mail_estado,mail_fecha,mail_dest,mail_pag) VALUES ('0','$cod','0','$fecha','$mail1','modificarUsuario')", $link);
        echo "<script>alert('Aviso: Falló el envío de Notificacion." . $mail->ErrorInfo . "');history.back();</script>";
    } else {
        mysql_query("INSERT INTO mail (mail_doc,mail_user,mail_estado,mail_fecha,mail_dest,mail_pag) VALUES ('0','$cod','1','$fecha','$mail1','modificarUsuario')", $link);
    }
}
mysql_close($link);
?>  
