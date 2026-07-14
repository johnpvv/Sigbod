<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
include("../include/conn.php");
$link = Conectarse(); //Variable de conexion
$b = $_POST['b'];
$chk = $_POST['chkall'];
if (!empty($b) or $chk == "true") {
    if ($chk == "true") {
        $sql = "SELECT * FROM usuarios, perfiles, estado, unid_oper WHERE usuario_perfil=perfil_id and usuario_estado=estado_id AND uni_id=usuario_institucion ORDER BY usuario_id DESC";
    } else {
        $sql = "SELECT * FROM usuarios, perfiles, estado, unid_oper WHERE usuario_perfil=perfil_id and usuario_estado=estado_id AND uni_id=usuario_institucion AND (REPLACE ( usuario_rut, '.', '' ) LIKE '%" . $b . "%' OR usuario_nombre LIKE '%" . $b . "%' OR usuario_apellidos LIKE '%" . $b . "%' OR uni_nombre LIKE '%" . $b . "%') ORDER BY usuario_apellidos";
    }
    $result = mysql_query($sql, $link);
    $contar = mysql_num_rows($result);
    $_SESSION['query'] = $sql;
    if ($contar === 0) {
        echo '<table class="table2a">';
        echo '<tr><td class="texto8b" colspan="9">No Se han Encontrado Registros que contengan: '.$b.'</td></tr>';
        echo'</table>';
    } else {
        echo '<table class="table2a">';
        echo '<tr>
            <th width="100">RUT</th>
            <th width="280">Nombre Usuario</th>
            <th>Email</th>
            <th width="100">Tel&eacute;fono</th>
            <th>Perfil</th> 
            <th>Estado Usuario</th> 
            <th>Unidad o Instituci&oacute;n</th>
            <th>Fecha de Creaci&oacute;n</th>
            <th>Acciones</th> 
        </tr>';
        while ($row = mysql_fetch_array($result)) {
            printf("<tr><td><b>%s</b></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td><td>%s</td><td align='center'>%s</td></tr>", $row["usuario_rut"], $row["usuario_nombre"] . " &nbsp;" . $row["usuario_apellidos"], $row["usuario_email"], "<a href='https://api.whatsapp.com/send?phone=".$row["usuario_telefono"]."&text=hola'>".$row["usuario_telefono"]."</a>", $row["perfil_nombre"], $row["estado_nombre"], $row["uni_nombre"], date("d/m/Y", strtotime($row["usuario_fechaIngreso"])), "<a href=../edicion/ModificarUsuario.php?id=" . $row["usuario_id"] . ">
            <img border='0' alt='editar usuario' title='Editar Usuario: " . $row["usuario_nombre"] . "' src='../img/edit1.png' width='25' height='25'></a>");
        }
        echo '<tr><td class="texto8b" colspan="9">Se han Encontrado: ' . $contar . ' Registros.</td></tr>';
        echo'</table>';
    }
    mysql_free_result($result);
    mysql_close($link);
}
?>

