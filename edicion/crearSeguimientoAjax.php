<?php
//999999 mail crear seguimiento
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../nosesion.php');
    exit();
}
if ($_SESSION['perfil'] > 1) {
    echo '<script>alert("Su Perfil no permite la visualizacion de esta pantalla, Favor contacte al Administrador.");history.back(-1);</script>';
    echo '<center><button class="boton" onclick="javascript:window.close()">Salir&nbsp;<img class="img" alt="Salir" title="salir de la pantalla de edicion" src="../img/salir.png"></button></center>';
    exit();
}
$usuario = $_SESSION["usuario"];
$date = date("Y-m-d H:i:s");
include("../include/conn.php");
include("../PHPMailer/generarMail.php");
$link = Conectarse(); //Variable de coneccion
/* ------------------arreglos para obtener datos desde pagina anterior------------------ */
$i = $_POST["contador"]; //cantidad de elementos totales
$id = $_POST["txtid"];
$fecha = date("Y-m-d H:i:s");
$nombre = strtoupper($_POST["txtnombre"]);
$descrip = strtoupper($_POST["txtdescrip"]);
$fven = $_POST["txtven"];
$resp = $_POST["txtresp"];
$pag = $_POST["txtpag"];
$est = $_POST["txtest"];
if ($est == "") {
    $est = 1;
} else {
    $est = 0;
}
$docs = [];
if ($nombre == "") {
    echo '<script>alert("Error, datos invalidos");</script>';
    exit();
}

/* ---------------------------------Llenar Arreglos------------------------------------- */
for ($j = 0; $j <= $i; $j++) {
    $docs[$j] = $_POST["txtdoc_" . $j]; //array con los codigos 
}

/* -------------------------actualizar datos de grupos en BBDD-------------------------- */
if ($pag == "modifica") {
    $sql1 = "UPDATE seguimiento SET seg_nombre='$nombre', seg_descrip='$descrip', seg_fven='$fven', seg_fcrea='$date',seg_resp='$resp', seg_estado='$est'  WHERE seg_id='$id' ";
    MySQL_query($sql1, $link) or die("<script>alert('Ha Ocurrido un Error: " . mysql_error() . "');</script>");
    $tipo = "MODIFICADO";
}
if ($pag == "crea") {
    $sql1 = "INSERT INTO seguimiento (seg_nombre, seg_descrip, seg_fcrea, seg_fven, seg_resp, seg_estado) VALUES ('$nombre','$descrip','$date','$fven','$resp','$est')";
    MySQL_query($sql1, $link) or die("<script>alert('Ha Ocurrido un Error: " . mysql_error() . "');</script>");
    $id = mysql_insert_id(); //obtener el ultimo id en la tabla movimiento
    $tipo = "CREADO";
}
$cuenta = 1;
for ($k = 0; $k < count($docs); $k++) {
    if ($docs[$k] != "") {
        $sql = "INSERT IGNORE INTO seguimiento_detalle(seg_id,seg_id_doc,seg_fecha) VALUES ('$id','$docs[$k]','$date')";
        MySQL_query($sql, $link) or die("<script>alert('Ha Ocurrido un Error: " . mysql_error() . "');</script>");
    } else {
        $cuenta++;
    }
}
/* -------------------actualizar datos de cuenta documentos en BBDD----------------------- */
$sql4 = "SELECT * FROM seguimiento_detalle WHERE seg_id='$id'";
$res4 = MySQL_query($sql4, $link)or die(mysql_error());
$contar4 = mysql_num_rows($res4);
$sql5 = "UPDATE seguimiento SET seg_cant='$contar4' WHERE seg_id='$id' ";
MySQL_query($sql5, $link) or die("<script>alert('Ha Ocurrido un Error: " . mysql_error() . "');</script>");
echo "<script>alert('Felicidades... los datos se han " . $tipo . " correctamente, con el ID: " . $id . ". Ademas, se enviará un correo para informar el estado del Seguimiento.');</script>";
echo"<script>location.href='crearSeguimiento.php?idseg=" . $id . "'</script>";
$_SESSION['seg'] = $id;
/* ----------------------Enviar Notificaciones al Usuario asignado----------------------- */

$res3 = mysql_query("SELECT usuario_nombre, usuario_apellidos, usuario_email FROM usuarios where usuario_id='$resp'", $link);
$row3 = mysql_fetch_array($res3);
$mailuser = $row3["usuario_email"];
$usuarionotif = $row3["usuario_nombre"] . " " . $row3["usuario_apellidos"];

if ($est == 0) {
    $mail->ClearAddresses();
    $mail->Subject = "Notificacion Seguimiento Numero: " . $id . " Ha sido Cerrado";
    $mail->Body = "<b>Estimado(a) " . $usuarionotif . ":</b><br> 
	Se ha cerrado el seguimiento, con ID Numero: " . $id . ", (" . $nombre . ") por lo que se ha dado por CONCLUIDO.
	<br> Puede ver el seguimiento en el siguiente enlace: <a href='http://10.6.23.74/Dropbox/sigbod/edicion/crearSeguimiento.php?idseg=" . $id . "'><b>Ver enlace</b><a>
	<br><br>Mensaje Generado Automaticamente por Sistema Sigbod.";
    $mail->AddAddress($mailuser);
    if (!$mail->Send()) {
        echo "<script>alert('Aviso: Falló el envío de Notificacion.... Error: " . $mail->ErrorInfo . "');history.back();</script>";
    }
} else {
    $mail->ClearAddresses();
    $mail->Subject = "Notificacion Seguimiento " . $tipo . " ";
    $mail->Body = "<b>Estimado(a) " . $usuarionotif . ":</b><br> 
	Se ha " . $tipo . " el seguimiento, con ID Numero: " . $id . ", (" . $nombre . ") por lo que usted ha sido designado para revisarlo. 
	<br> Puede ver el seguimiento en el siguiente enlace: <a href='http://10.6.23.74/Dropbox/sigbod/edicion/crearSeguimiento.php?idseg=" . $id . "'><b>Ver enlace</b><a>
	<br>Fecha de Vencimiento: " . date("d/m/Y", strtotime($fven)) . "
	<br><br>Mensaje Generado Automaticamente por Sistema Sigbod.";
    $mail->AddAddress($mailuser);
    if (!$mail->Send()) {
        mysql_query("INSERT INTO mail (mail_doc,mail_user,mail_estado,mail_fecha,mail_dest,mail_pag) VALUES ('0','$usuario','0','$fecha','$mailuser','$tipo.Seguimiento')", $link);
        echo "<script>alert('Aviso: Falló el envío de Notificacion." . $mail->ErrorInfo . "');history.back();</script>";
    } else {
        mysql_query("INSERT INTO mail (mail_doc,mail_user,mail_estado,mail_fecha,mail_dest,mail_pag) VALUES ('0','$usuario','1','$fecha','$mailuser','$tipo.Seguimiento')", $link);
    }
}
mysql_close($link);

