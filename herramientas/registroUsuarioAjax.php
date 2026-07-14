<?php

session_start();
if (!isset($_SESSION['usuario']) && isset($_SESSION['cambiar'])) {
    header('Location: ../noSesion.php');
    exit();
}
if (!isset($_POST['rut'])) {
    echo'<script>alert("Error, Parametros de carga incorrectos...");window.location = "../principal.php";</script>';
    exit();
}

include("../include/conn.php");
include("../PHPMailer/generarMail.php");
$link = Conectarse();
$r = $_POST['rut'];
$n = strtoupper(trim($_POST['nom']));
$a = strtoupper(trim($_POST['ap']));
$fnac = $_POST["fnac"];
$se = $_POST['sex'];
$fechaIn = date("Y-m-d");
$email = strtolower(trim($_POST['email']));
$instituc = $_POST['instituc'];
$fono = trim($_POST['txtfono']);
$passw = md5($_POST['pass0']);
$dir = $_POST['dir'];
$fecha = date("Y-m-d H:i:s");
if (!isset($_SESSION["cambiar"])) {
    $perfil = "2"; //perfil defecto digitador
} else {
    $perfil = $_POST['perf'];
}
$sql = "INSERT INTO usuarios(usuario_rut,usuario_nombre,usuario_apellidos,usuario_fechaNac,usuario_sexo,usuario_fechaIngreso,usuario_email,usuario_institucion, usuario_perfil, usuario_telefono, usuario_password,usuario_direccion,usuario_estado)VALUES('$r','$n','$a','$fnac','$se','$fechaIn','$email','$instituc','$perfil','$fono','$passw','$dir','1')";
mysql_query($sql, $link) or die('<script>alert("Ha ocurrido un Error al Crear la Cuenta: ' . mysql_error() . '");</script>');

/* ----------------------Enviar Notificaciones al Usuario asignado----------------------- */
$usuarionotif = $n . ' ' . $a;
$mail->ClearAddresses();
$mail->Subject = "Registro Nuevo Usuario en Sistema SigBod.";
$mail->Body = "<b>Estimado(a) " . $usuarionotif . ":</b><br> 
	Se ha Creado una cuenta en sistema SigBod, segun los datos proporcionados. 
	<br> El Usuario es su RUT, con digito verificador, y su Password, la proporcionada al momento del Registro.
	<br>El Password se puede cambiar en: <b>Menu principal->Administracion->Usuarios->Cambiar Password</b>
	<br>Le damos la bienvenida a esta Herramienta, que se encuentra en constantes mejoras, para ser un aporte a su trabajo.
        <br>Para Acceder, visite el siguiente link: <a href='http://10.6.23.74/Dropbox/sigbod/index.php'><b>Ver enlace</b><a>
	<br><br>Mensaje Generado Automaticamente por Sistema Sigbod.";
$mail->AddAddress($email);
if (!$mail->Send()) {
    mysql_query("INSERT INTO mail (mail_doc,mail_user,mail_estado,mail_fecha,mail_dest,mail_pag) VALUES ('0','$r','0','$fecha','$email','registroUsuario')", $link);
    echo "<script>alert('Aviso: Falló el envío de Notificacion." . $mail->ErrorInfo . "');history.back();</script>";
} else {
    mysql_query("INSERT INTO mail (mail_doc,mail_user,mail_estado,mail_fecha,mail_dest,mail_pag) VALUES ('0','$r','1','$fecha','$email','registroUsuario')", $link);
}
echo'<script>alert("El Usuario: ' . $n . ' ' . $a . ' se ha registrado correctamente...");window.location = "../principal.php";</script>';
mysql_close($link);
?>
