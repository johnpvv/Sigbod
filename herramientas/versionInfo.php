<!DOCTYPE html>
<!-- fecha archivo 03/03/2019
Sigbod John Vaccarella
-->
<html>
    <head>
        <script type="text/javascript" src="../js/validarut.js"></script>
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <title>SigBod - Version</title>       
    </head>
    <body class="fondo">
        <?php
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../index.php');
            exit();
        }
        include("../include/conn.php");
        $link = Conectarse();
        $sql = "select * from version where version_estado=1";
        $res = MySQL_query($sql, $link);
        $row = MySQL_Fetch_array($res);
        $ver_nombre = $row["version_nombre"];
        $ver_num = $row["version_num"];
        $ver_obs = $row["version_obs"];
        $date_row = date_create($row["version_fecha"]);
        $date = date_format($date_row, 'd/m/Y');
        mysql_close($link);
        ?>       
        <table border="0" style="width: 320px;" class="centrado">
            <tr>	
                <td colspan=2 align="center">
                    <p class="caja_rojo">Version Actual:</p>
                    <p align='center' class='texto6'><?=$ver_num ?> <?=$ver_nombre?>, Fecha: <?=$date?></p>
                    <p class='caja_rojo'>Detalle:</p>
                    <p align='center' class='texto6'><?=$ver_obs?></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="button" class="boton" value="Salir" onclick="window.close()"/>
                </td>
            </tr>
        </table>
    </body>
</html>     