<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<html>
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>
        <script src="../js/tablesorter.min.js" type="text/javascript"></script>        
        <script type="text/javascript">
            $(document).ready(function () {
                $(window).scroll(function () {
                    if ($(this).scrollTop() > 100) {
                        $('#scroll').fadeIn();
                    } else {
                        $('#scroll').fadeOut();
                    }
                });
                $('#scroll').click(function () {
                    $("html, body").animate({scrollTop: 0}, 600);
                    return false;
                });
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#tablamov").tablesorter();
            });
        </script>
        <title>SigBod - Mails Enviados</title>   
    </head>   
    <body class="fondo">
        <?php
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../index.php');
            exit();
        }
        if ($_GET['id'] == "") {
            echo"<script>alert('Error, no hay datos para mostrar.');javascript:window.close();</script>";
            exit();
        }
        include("../include/conn.php");
        $link = Conectarse(); //Variable de conexion
        $cod = $_GET['id'];
        $comilladob = '"';
        $comillasim = "'";
        $sql = "SELECT * FROM mail,usuarios,estado WHERE mail_doc='$cod' AND mail_user=usuario_rut AND estado_id=mail_estado ORDER BY mail_id DESC";
        $res = MySQL_query($sql, $link)or die(mysql_error());
        $contar = mysql_num_rows($res);
        if ($contar == 0) {
            echo '<script>alert("El documento seleccionado, no registra Mails enviados.");
                    window.close();</script>';
        }
        echo '<table class="table5" style="width:800px;">';
        echo '<tr><td colspan="5" class="texto">Correos enviados por Documento</td></tr>';
        echo '<tr><td colspan="5" class="texto4">N° de Solicitudes Encontradas: ' . $contar;
        echo '</table>';
        echo '<table class="table7" id="tablamov" style="width:800px;">';
        echo '<thead><tr><th width="60">N° Solicitud</th><th width="80">Estado</th><th width="100">Fecha Envío</th><th width="180">Destinatario</th><th width="250">Usuario Solicitante</th></thead>';
        while ($row = mysql_fetch_array($res)) {
            $estado = $row["estado_nombre"];
            printf("<tr><td><b>%s</b></td><td>%s</td><td>%s</td><td><b>%s</b></td><td><b>%s</b></td></tr>", $row["mail_id"], $estado, date("d/m/Y H:i:s", strtotime($row["mail_fecha"])), $row["mail_dest"], $row["usuario_nombre"]." ".$row["usuario_apellidos"]);
        }
        echo '</table><table class="table7" style="width:800px;">';
        echo '<tr><td colspan="5" class="texto8a"></td></tr>';
        echo '<tr><td colspan="5" class="texto5"><center><button class="boton" type="button" onclick="window.close()">Salir&nbsp;<img class="img" alt="principal" title="Salir" src="../img/salir.png"></button>';
        echo '</table>';
        mysql_close($link);
        ?>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>