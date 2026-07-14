<!DOCTYPE html>
<html lang="es">
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />          
        <title>SigBod - Enviar Mail</title>
    </head>
    <body class="fondo"> 

        <?php
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../noSesion.php');
            exit();
        }
        include("../include/conn.php");
        include("../PHPMailer/generarMail.php");
        $link = Conectarse();
        $id = $_GET["id"];
        $rut = $_GET["rt"];
        $fecha = date("Y-m-d H:i:s");
        $sql1 = "SELECT * FROM documento, proveedores WHERE doc_id='$id' AND doc_rutpv='$rut' AND prov_rut=doc_rutpv ORDER BY doc_noc DESC";
        $data = mysql_query($sql1);
        $row = mysql_fetch_array($data);
        $prov = $row["prov_nomcontacto"];
        $mail1 = $row["prov_email"];
        $user = $_SESSION['usuario'];
        $sql2 = "SELECT * FROM usuarios WHERE usuario_rut='$user'";
        $data2 = mysql_query($sql2);
        $row2 = mysql_fetch_array($data2);
        $mailuser = $row2["usuario_email"];
        if ($mail1 == "") {
            echo '<script>alert("No se puede enviar Mail al proveedor, ya que no existe Mail registrado. favor revisar en ficha del proveedor...");window.close();</script>';
        } else {
            echo "Procesando, Favor espere...";
            $mail->ClearAddresses();
            $mail->Subject = "Facturas Pendientes Hospital San Borja Arriaran (Urgente)";
            $mail->Body = "<b>Estimado(a) " . iconv("UTF-8", "WINDOWS-1252", $prov) . ":</b><br> 
            Le saludamos cordialmente, y le solicitamos favor adjuntar los respaldos firmados de recepcion conforme de los siguientes documentos, para generar la regularizacion en nuestros sistemas, y posterior pago de los mismos.<br>
			<table border='1'><thead><tr><th>Nro. Documento</th><th>Tipo Documento</th><th>Proveedor</th><th>Fecha Documento</th><th>Orden de Compra</th><th>Monto Total</th></tr></thead>
			<tr><td><b>" . $row["doc_ndoc"] . "</b></td><td>" . $row["doc_tipodoc"] . "</td><td>" . number_format($row["prov_rut"], 0, '.', '.') . "-" . $row["prov_dv"] . "&nbsp;" . " " . iconv("UTF-8", "WINDOWS-1252", $row["prov_nombre"]) . "</td><td>" . date("d/m/Y", strtotime($row["doc_fechadoc"])) . "</td><td>" . $row["doc_noc"] . "</td><td>$" . number_format($row["doc_montototal"], 0, ',', '.') . "</td></tr></table><br>
			Estos respaldos, se componen de lo siguiente, segun sea el caso:<br>
			<b>-Hoja de Consumo, con informacion del paciente intervenido.<br>
			-Copia cedible firmada de la Factura o Guia de Despacho asociada a la Misma.<br>
			-Protocolos operatorios, guias manuales, u otros que demuestren la fecha del procedimiento, y los productos facturados con cargo al paciente (si aplica).</b>
            <br><br>
			Favor responder a los siguientes correos:<br>
			john.vaccarella@redsalud.gob.cl<br>
			jocelyn.almeida@redsalud.gob.cl
            <br><br>	Muchas Gracias. <br>Equipo Bodega Insumos Clinicos, Hospital Clinico San Borja Arriaran.<br>
			Amazonas No. 619, Santiago Centro.<br>
			<b style='font-size:11px;'>mensaje generado automaticamente por sistema Sigbod, v.1.27. (Se han omitido algunos acentos por compatibilidad)</b>";
            $mail->AddAddress($mail1);
            $mail->addCC($mailuser);
            $rs = mysql_query("SELECT * FROM const_config WHERE const_cat ='mail' AND const_est='1'", $link); //constantes de configuracion, reconoce todos los categoria mail guardados
            while ($row1 = mysql_fetch_array($rs)) {
                $mail->addCC($row1["const_val"]);
            }
            if (!$mail->Send()) {
                mysql_query("INSERT INTO mail (mail_doc,mail_user,mail_estado,mail_fecha,mail_dest,mail_pag) VALUES ('$id','$user','0','$fecha','$mail1','mailFact')", $link);
                echo "<script>alert('Aviso: Falló el envío de Notificacion." . $mail->ErrorInfo . "');window.close();</script>";
            } else {
                mysql_query("INSERT INTO mail (mail_doc,mail_user,mail_estado,mail_fecha,mail_dest,mail_pag) VALUES ('$id','$user','1','$fecha','$mail1','mailFact')", $link);
                echo '<script>alert("Se ha enviado correctamente el correo a: ' . $prov . ' y ' . $mailuser . '");window.close();</script>';
            }
        }
        mysql_close($link);
        ?>  
    </body>
</html>