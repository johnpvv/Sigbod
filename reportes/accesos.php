<!DOCTYPE html>
<!--
Sigbod John Vaccarella
25/01/2021
-->
<html>
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
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
        <title>SigBod - Accesos al Sistema</title> 
    </head>
    <body class="fondo">
        <?php
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../index.php');
            exit();
        }
        include("../include/conn.php");
        $user = $_SESSION["usuario"];
        $link = Conectarse(); //Variable de coneccion
        $result = mysql_query("SELECT * FROM usuarios u INNER JOIN usuario_log ul ON u.usuario_rut=ul.usuario_rut 
				INNER JOIN perfiles p ON p.perfil_id= u.usuario_perfil 
				INNER JOIN estado e ON e.estado_id= u.usuario_estado 
                                INNER JOIN unid_oper UOP ON UOP.uni_id = u.usuario_institucion 
                                ORDER BY u.usuario_nombre", $link)or die(mysql_error());
        ?>       
        <table class="table2a">
            <tr>
                <td colspan="10" class="texto"><img class="imgico" alt='Usuarios' title='Usuarios' src='../img/user.png'>&nbsp;&nbsp;&nbsp;ACCESOS AL SISTEMA</td>
            </tr>
        </table>
        <table class="table2a" id="tabladoc">
            <thead><tr>
                    <th width="150">Nombre Usuario</th>
                    <th width="100">Rut</th>
                    <th width="120">Email</th>
                    <th width="50">Tel&eacute;fono</th>
                    <th width="50">Estado Usuario</th> 
                    <th width="100">Unidad Operativa</th>
                    <th width="50">Accesos al Sistema</th>
                    <th width="90">IP de Acceso</th>
                    <th width="60">Conexión</th>
                    <th width="80">Fecha Ultimo Ingreso</th>
                </tr></thead>
            <?php
            while ($row = mysql_fetch_array($result)) {
                $estado = $row["usuario_activo"];
                if ($estado == 1) {
                    $estado = "<span style='color:green;'>Activo</span>";
                } else {
                    $estado = "<span style='color:red;'>Inactivo</span>";
                }
                printf("<tr><td>%s</td><td><b>%s</b></td><td>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td><td>%s</td><td>%s</td><td><b>%s</b></td><td><b>%s</b></td></tr>", $row["usuario_nombre"] . ' ' . $row["usuario_apellidos"], $row["usuario_rut"], $row["usuario_email"], $row["usuario_telefono"], $row["estado_nombre"], $row["uni_nombre"], $row["usuario_contador"], $row["usuario_ip"], $estado, date("d/m/Y ; h:i:s A", strtotime($row["usuario_log_fecha"])));
            }
            mysql_free_result($result);
            mysql_close($link);
            ?>
        </table>
        <table class="table2a">
            <tr>
                <td colspan="10">
                    <button class="botonnormal" type="button" onclick="window.location = '../principal.php'">Volver&nbsp;&nbsp;<img class='img' alt='volver' title='Volver a pantalla anterior' src='../img/undo.png'></button>
                </td>
            </tr>
        </table>
        <script type="text/javascript">$(document).ready(function () {
                $("#tabladoc").tablesorter();
            });</script>
        <a href = "javascript:void(0);" id = "scroll" title = "Ir Arriba" style = "display: none;">Arriba<span></span></a>
    </body>
</html>