<?php
//999998 mail crear seguimiento
session_start();
if (isset($_SESSION["cambiar"])) {
    header('Location: ../index.php');
    exit();
}
include("include/conn.php");
include("PHPMailer/generarMail.php");
$link = Conectarse();
$user = $_POST['rut'];
$date = $_POST["date"];
$fecha = date("Y-m-d H:i:s");
$sql = "SELECT * FROM usuarios WHERE usuario_rut='$user' AND usuario_fechaNac='$date'";
$res = MySQL_query($sql, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/></center>");
$row = MySQL_Fetch_array($res);
$userid = $row["usuario_id"];
$estado = $row["usuario_estado"];

if ($userid == "") {
    echo '<script>alert("Los datos Ingresados son Incorrectos, favor intente Nuevamente...");</script>';
} elseif ($estado == 0) {
    echo '<script>alert("Usuario Inactivo... Debe contactar al Administrador);</script>';
} else {
    $pass = md5(1234);
    $sql1 = "UPDATE usuarios SET usuario_password ='$pass' WHERE usuario_rut='$user' AND usuario_id='$userid'";
    mysql_query($sql1, $link);
    $cont = mysql_affected_rows();
    if ($cont != 0) {
        echo '<script>alert("La contraseña se ha restablecido exitosamente...\nEl valor por defecto es 1234. se puede cambiar en:\nMenu principal->Administracion->Usuarios->Cambiar Password.");</script>';
        /* ----------------------Enviar Notificaciones al Usuario asignado----------------------- */
        $mailuser = $row["usuario_email"];
        $usuarionotif = $row["usuario_nombre"] . " " . $row["usuario_apellidos"];
        $mail->ClearAddresses();
        $mail->Subject = "Restablecimiento de Password en sistema Sigbod";
        $mail->Body = "<b>Estimado(a) " . $usuarionotif . ":</b><br> 
	Se ha Restablecido su Password en el sistema SigBod, debido a una solicitud generada por Usted. 
	<br> La clave por defecto es : 1234
	<br>Se puede cambiar en: <b>Menu principal->Administracion->Usuarios->Cambiar Password</b>
	<br>Si usted no realiz&oacute; esta solicitud, favor contacte al Administrador.
	<br><br>Mensaje Generado Automaticamente por Sistema Sigbod.";
        $mail->AddAddress($mailuser);
        if (!$mail->Send()) {
                mysql_query("INSERT INTO mail (mail_doc,mail_user,mail_estado,mail_fecha,mail_dest,mail_pag) VALUES ('0','$user','0','$fecha','$mailuser','recuperarPass')", $link);
                echo "<script>alert('Aviso: Falló el envío de Notificacion." . $mail->ErrorInfo . "');</script>";
            } else {
                mysql_query("INSERT INTO mail (mail_doc,mail_user,mail_estado,mail_fecha,mail_dest,mail_pag) VALUES ('0','$user','1','$fecha','$mailuser','recuperarPass')", $link);
                echo '<script>alert("Se ha enviado correctamente el correo a: '. $mailuser . '");</script>';
            }
        echo "<script>location.href='principal.php'</script>";
    } else {
        echo '<script>alert("Ha ocurrido un error, o quizás la clave ya ha sido restaurada.\nLa clave por defecto es: 1234.\nFavor intente Nuevamente...");</script>';
    }
}
mysql_close($link);
?>
