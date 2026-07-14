<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<html>
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <title>SigBod - Crear OC</title>       
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
    </head>   
    <body class="fondo">
        <?php
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../index.php');
            exit();
        }
        if ($_SESSION['pedido'] == 0) {
            echo"<script>javascript:history.go(-2)</script>";
            exit();
        }
        $usuario = $_SESSION["usuario"];
        $fecha = $_POST["fecha"];
        $date = date("Y-m-d");
        $datetime = date("Y-m-d H:i:s");
        include("../include/conn.php");
        $link = Conectarse(); //Variable de coneccion
        /* ------------------arreglos para obtener datos desde pagina anterior------------------ */
        $i = $_POST["contador"]; //cantidad de elementos totales
        $rut1 = $_POST["rutpv"];
        $dv = $_POST["dv"];
        $oc = $_POST["txtoc"];
        $obs = $_POST["txtcom"];
        $nompv = $_POST["nompv"];
        $cantsol = [];
        $preciooc = [];
        $codigo = [];
        for ($j = 0; $j < $i; $j++) {
            $cantsol[$j] = $_POST["txtreq_" . $j]; //array con las cantidades solicitadas
            $preciooc[$j] = $_POST["txtprec_" . $j]; //array con los precios para modificar
            $codigo[$j] = $_POST["txtcod_" . $j]; //array con los codigos 
        }
        /* ------------------actualizar datos de saldo oc en BBDD y mostrar resultado en pantalla------------------ */
        echo '<table class="table3" align="center">';
        echo '<tr><th colspan="9" class="texto4"><h1>La OC: ' . $oc . ' se ha creado correctamente.</h1></th></tr>';
        echo '<tr><th colspan="9"><h3>Proveedor: ' . $rut1 . '-' . $dv . ' &nbsp;&nbsp;  ' . $nompv . ' ,&nbsp;&nbsp;  Fecha Creación: ' . date("d/m/Y") . '</h3></th></tr>            
                <tr><th width="70">Codigo Interno</th><th>Codigo CM</th><th width="350">Glosa</th>
                <th>Empaque</th><th width="100">Precio Unitario orden de Compra</th><th width="100">Precio Unitario en Sistema</th><th width="30">Cantidad Solicitada</th>
                <th width="30">Cantidad Recibida</th><th width="30">Cantidad Pendiente</th></tr>';
        for ($k = 0; $k < count($codigo); $k++) {
            $sql3 = "INSERT INTO saldos(saldo_clave, saldo_oc, saldo_fechaoc, saldo_rutprov, saldo_codigo, saldo_preciounit, saldo_solicitado, saldo_recibido, saldo_pendiente,saldo_fechacarga) "
                    . "VALUES ('$oc$codigo[$k]','$oc','$fecha','$rut1','$codigo[$k]','$preciooc[$k]','$cantsol[$k]','0','$cantsol[$k]','$date')";
            MySQL_query($sql3, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
            $sql = "SELECT * FROM productos, unimed WHERE (prd_codigo ='$codigo[$k]') AND prd_unimed=unimed_id"; //agrupar en parentesis los campos para que se ejecute primero la accion
            $data = mysql_query($sql);
            $sql2 = "UPDATE proveedores set prov_estado ='1' WHERE prov_rut='$rut1'"; //agrupar en parentesis los campos para que se ejecute primero la accion
            $data2 = mysql_query($sql2);
            while ($row = mysql_fetch_array($data)) {
                echo '<tr><td align="center"><b>' . $codigo[$k] . '<b></td><td align="center"><b>' . $row["prd_codcm"] . '<b></td><td align="left">' . $row["prd_glosa"] . '</td><td>' . $row["unimed_nombre"] . '</td><td>$ ' . number_format($preciooc[$k], 2, ',', '.') . '</td><td>$ ' . number_format($row["prd_precio"], 2, ',', '.') . '</td><td align="center"><b>' . $cantsol[$k] . '</b></td><td align="center"><b>0</b></td><td align="center"><b>' . $cantsol[$k] . '</b></td></tr>';
            }
        }
        $sql4 = "INSERT IGNORE INTO oc_obs (oc_obs_oc, oc_obs_det, oc_obs_fecha, oc_obs_user) VALUES ('$oc', '$obs','$datetime','$usuario')";
        MySQL_query($sql4, $link);
        echo '</table>';
        $_SESSION['pedido'] = 0;
        mysql_close($link);
        ?>
        <br/>		
    <center>	
        <form>
            <button class="boton" type="button" onclick="javascript:history.back(-1)">Volver Atrás&nbsp;&nbsp;<img class='img' alt='volver' title='Volver a pantalla anterior' src='../img/undo.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="boton" type="button" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="boton" type="button" onclick="window.print();">Imprimir&nbsp;<img class="img" alt="imprimir pantalla" title="imprimir pantalla" src="../img/print.png"></button>
        </form>
    </center>
</body>
</html>