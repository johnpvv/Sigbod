<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<html>
    <head>
        <script type="text/javascript" src="js/validarut.js"></script>
        <link rel="stylesheet" href="css/estilos.css" />
        <link rel="shortcut icon" href="img/favicon.ico" />
        <title>SigBod - Validacion</title>       
    </head>
    <body class="fondo">
        <?php
        session_start();
        include("include/conn.php");
        include("include/ip.php");
        $link = Conectarse();
        echo "<center><span class='texto5a'>Procesando y validando Datos, por favor espere...</span></center>";
        $user = $_POST['rut'];
        $pass = md5($_POST["clave"]);
        $sql = "SELECT * FROM usuarios WHERE usuario_rut='$user' AND usuario_password='$pass'";
        $res = MySQL_query($sql, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/></center>");
        $row = MySQL_Fetch_array($res);
        $user1 = $row["usuario_rut"];
        $pass1 = $row["usuario_password"];
        $estado = $row["usuario_estado"];
        $ip = getRealIP();
        if ($ip == "::1") {
            $ip = "Servidor";
        }
        if ($user1 == "") {
            echo '<script>alert("El Usuario o Contraseña son Incorrectos, favor intente Nuevamente...");history.back(-1);</script>';
        } elseif ($estado == 0) {
            echo "<center><h2 class='texto'>Usuario Inactivo... Debe contactar al Administrador.</h2></center>";
        } else {
            if ($user == $user1 and $pass == $pass1) {
                $_SESSION["usuario"] = $user1;
                $_SESSION["perfil"] = $row["usuario_perfil"];
                $unid = $row["usuario_institucion"];
                $sql3 = "SELECT uni_nombre, uni_id FROM unid_oper WHERE uni_id='$unid'";
                $res3 = mysql_query($sql3) or die(mysql_error());
                $row3 = mysql_fetch_array($res3);
                $_SESSION["unidad"] = $row3["uni_nombre"];
                $res1 = mysql_query("SELECT * FROM usuario_log WHERE usuario_rut ='$user1'", $link);
                $row1 = MySQL_Fetch_array($res1);
                $cuenta = intval($row1["usuario_contador"]); //asignar como numero entero
                $fechaAnterior = $row1["usuario_log_fecha"];
                $_SESSION["fecha"] = $fechaAnterior;
                $_SESSION['nvstockid'] = 1; //Para refrescar solo una vez la revision de stock, y los contadores
                $_SESSION['nvstockanalisis'] = 1; //Para refrescar solo una vez la revision del analisis del stock y solicitudes recientes
                $_SESSION['oc'] = 1;//para refrescar la oc y descargue desde mp
                $fecha = date('Y-m-d H:i:s');
                if ($cuenta === 0) {
                    $cuenta1 = $cuenta + 1;
                    $sql2 = "INSERT INTO usuario_log (usuario_rut,usuario_log_fecha, usuario_fecha_anterior, usuario_contador, usuario_ip, usuario_activo) VALUES ('$user1','$fecha','$fecha','$cuenta1','$ip','1')";
                    mysql_query($sql2, $link);
                } else {
                    $cuenta = $cuenta + 1;
                    $sql1 = "UPDATE usuario_log SET usuario_rut ='$user1' , usuario_log_fecha ='$fecha',usuario_fecha_anterior ='$fechaAnterior', usuario_contador='$cuenta', usuario_ip='$ip', usuario_activo='1' WHERE usuario_rut='$user1'";
                    mysql_query($sql1, $link);
                }
                if ($cuenta < 4) {
                    $av = 4 - $cuenta;
                    echo'<script>alert("Bienvenido a Sigbod!\nComo usuario nuevo puede consultar la ayuda al lado derecho superior de la barra de menus, si necesita mas ayuda o desea realizar alguna sugerencia, favor comuníquese con el administrador.\nEspero que esta herramienta sea Util para Usted.\nSaludos!\n(Este aviso desaparecerá en ' . $av . ' Inicio(s) de sesion más)");</script>';
                }
                echo"<script>location.href='principal.php'</script>";
            } else {
                echo"<center><h2 class='texto'>El Usuario o Contraseña no Corresponden...</h2></center>";
            }
        }
        mysql_close($link);
        ?>
    <center><button type="button" class="boton" onclick="window.location = 'index.php'">Ingrese Nuevamente &nbsp;&nbsp;<img class='img' alt='volver' title='Volver a Login' src='img/undo.png'></button></center>
</body>
</html>