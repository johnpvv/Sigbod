<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
include '../include/conn.php';

if (($_REQUEST['Subir']) and ( $_FILES['imagen']['tmp_name'])) {
    $nomfe = "fondo";
    list($nombre, $extension) = split("\.", $_FILES['imagen']['name']);
    $destino = '../img/fondo/' . $nomfe . '.' . strtolower($extension);
    copy($_FILES['imagen']['tmp_name'], $destino);
    echo '<script>alert("Imagen Subida");</script>';
}
?>
<html>
    <head>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css"/>
        <link href="../css/tabla.css" rel="stylesheet" type="text/css"/>
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <title>SigBod</title> 
        <script type="text/javascript">
            function checkFile() {
                var fileElement = document.getElementById("uploadFile");
                var fileExtension = "";
                if (fileElement.value.lastIndexOf(".") > 0) {
                    fileExtension = fileElement.value.substring(fileElement.value.lastIndexOf(".") + 1, fileElement.value.length);
                }
                if (fileExtension.toLowerCase() === "jpg") {
                    return true;
                }
                else {
                    alert("Seleccionar solo archivos jpg");
                    return false;
                }
            }
        </script> 
    </head>
    <body class="fondo">		
        <form id="form1" name="form1" method="post" enctype="multipart/form-data" onsubmit= "return checkFile()">
            <table class="table2">
                <tr>
                    <td class="texto">Cargar Imagen de Fondo</td>
                </tr>
                <tr>                    
                    <td class="texto6">Seleccionar el archivo a Cargar:&nbsp;&nbsp;<input type="file" name="imagen" id="uploadFile" class="boton" accept=".jpg"/></td>
                </tr>
                <tr>
                    <td align="center"><button class="botonnormal" type="button" onclick="window.location = '../principal.php'">Volver&nbsp;&nbsp;<img class='img' alt='volver' title='Volver a pantalla anterior' src='../img/undo.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" name="Subir" id="Subir" value="Subir" class="botonnormal">Subir Imagen&nbsp;&nbsp;<img class='img' alt='subir' title='Subir imagen' src='../img/up.png'></button></td>
                </tr>
            </table>
        </form>    
    </body>
</html>