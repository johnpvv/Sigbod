<html>
    <head>
        <script type="text/javascript" src="js/validarut.js"></script>
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <title>SigBod</title>       
    </head>
    <body>
        <?php
        session_start();
        include_once("../include/conn.php");
        include("../PHPMailer/generarMail.php");
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../index.php');
            exit();
        }
        $link = Conectarse();
        $usuario = $_SESSION['usuario'];
        $passw = md5($_POST['pass']);
        $fecha = date("Y-m-d H:i:s");
        mysql_query("UPDATE usuarios SET usuario_password ='$passw' WHERE usuario_rut='$usuario'", $link);
        /* ----------------------Enviar Notificaciones al Usuario asignado----------------------- */
        $res = mysql_query("SELECT usuario_nombre, usuario_apellidos, usuario_email FROM usuarios where usuario_rut='$usuario'", $link);
        $row = mysql_fetch_array($res);
        $mailuser = $row["usuario_email"];
        $usuarionotif = $row["usuario_nombre"] . " " . $row["usuario_apellidos"];
        $mail->ClearAddresses();
        $mail->Subject = "Cambio de Password en sistema Sigbod";
        $mail->Body = "<b>Estimado(a) " . $usuarionotif . ":</b><br> 
	Se ha Cambiado su Password en el sistema SigBod, debido a una solicitud generada por Usted. 
	<br>Si usted no realiz&oacute; esta solicitud, favor contacte al Administrador.
	<br><br>Mensaje Generado Automaticamente por Sistema Sigbod.";
        $mail->AddAddress($mailuser);
        if (!$mail->Send()) {
            mysql_query("INSERT INTO mail (mail_doc,mail_user,mail_estado,mail_fecha,mail_dest,mail_pag) VALUES ('0','$usuario','0','$fecha','$mailuser','insertarPass')", $link);
            echo "<script>alert('Aviso: Falló el envío de Notificacion." . $mail->ErrorInfo . "');history.back();</script>";
        } else {
            mysql_query("INSERT INTO mail (mail_doc,mail_user,mail_estado,mail_fecha,mail_dest,mail_pag) VALUES ('0','$usuario','1','$fecha','$mailuser','insertarPass')", $link);
            echo "<script>alert('La contraseña se ha cambiado exitosamente');window.close();</script>";            
        }
        mysql_close($link);
        ?>
    <center><input type="button" class="boton" value="Salir" onclick="window.close()"/></center>
</body>
</html>