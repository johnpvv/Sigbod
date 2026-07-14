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
        include("../include/conn.php");
        $link = Conectarse();

        $user = $_SESSION['usuario'];
        $pass = md5($_POST["clave"]);
        $sql = "select * from usuarios where usuario_rut='$user' and usuario_password='$pass'";

        $res = MySQL_query($sql, $link);
        $row = MySQL_Fetch_array($res);

        $user1 = $row["usuario_rut"];
        $pass1 = $row["usuario_password"];
        $estado = $row["usuario_estado"];
        if ($user1 == "") {
            echo "<center><h2 class='texto'>Contraseña Incorrecta</h2></center>";
        } else if ($estado == 0) {
            echo "<center><h2 class='texto'>Usuario Inactivo...</h2></center>";
        } else {
            if ($user == $user1 and $pass == $pass1) {
                $_SESSION["usuario"] = $user1;
                $_SESSION["perfil"]=$row["usuario_perfil"];

                echo"<script>location.href='cambiopwbd.php'</script>";
            } else {
                echo"<center><h2 class='texto'>Contraseña Incorrecta...</h2></center>";
            }
        }
        mysql_close($link);
        ?>
        <center><input type="button" value="Ingrese Nuevamente" class="boton" onclick="javascript:history.back(-1)"></center>
    </body>
</html>