<?php

require("src/PHPMailer.php");
require("src/SMTP.php");
require("src/Exception.php");
//se debe activar openSSL en archivo php.ini
$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->IsSMTP(); // enable SMTP
$mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
$mail->SMTPAuth = true; // authentication enabled autenticación tls el puerto 587; ssl, usa el puerto 465.
//$mail->SMTPSecure = 'tsl'; // secure transfer enabled REQUIRED for Gmail tsl para otro
$mail->SMTPSecure = 'STARTTLS'; //secure para outlook

/****************************************configuracion para envio por gmail***********************************************/
/*$mail->Host = "smtp.gmail.com";
$mail->Port = 587; // or 587
$mail->IsHTML(true);
$mail->Username = ("2019sigbod@gmail.com");
$mail->Password = "johnsigbod";
$mail->SetFrom("2019sigbod@gmail.com", "Alertas Sigbod");*/

/****************************************configuracion para envio por outlook***********************************************/
$mail->Host = "smtp.office365.com";
$mail->Port = 587; // or 587
$mail->IsHTML(true);
$mail->Username = ("sigbod2019@outlook.com");
$mail->Password = "Johnsigbod";
$mail->SetFrom("sigbod2019@outlook.com", "Alertas Sigbod");

/****************************************configuracion basica para envio de email***********************************************/
/*$mail->Subject = "Asignacion Nuevo Seguimiento";
$mail->Body = "mensaje de prueba";
$mail->AddAddress("j.vaccarella.v@gmail.com");
if (!$mail->Send()) {
    echo "<script>alert('Error, no se pudo notificar al Usuario Seleccionado');history.back();</script>";
} else {
	echo "<script>alert('Se ha enviado la notificacion correctamente');history.back();</script>";
}	echo "<script>alert('".$mail->ErrorInfo;."');history.back();</script>";
*/
?>