<?php
// Verificamos si se ha enviado un archivo
if(isset($_FILES["archivo"])) {
    $nombre_archivo = $_FILES["archivo"]["name"];
    $tipo_archivo = $_FILES["archivo"]["type"];
    $tamano_archivo = $_FILES["archivo"]["size"];
    
    // Verificamos que el archivo sea un documento de Word o Excel
    if(($tipo_archivo == "application/vnd.ms-word") || ($tipo_archivo == "application/vnd.ms-excel")) {
        
        // Movemos el archivo temporal a una ubicación permanente
        $ubicacion_archivo = "../archivos/$nombre_archivo";
        move_uploaded_file($_FILES["archivo"]["tmp_name"], $ubicacion_archivo);
        
        // Cargamos la biblioteca TCPDF
        require_once('include/tcpdf/tcpdf.php');
        
        // Creamos un nuevo objeto TCPDF y configuramos el formato del documento
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Agregamos una página al documento
        $pdf->AddPage();
        
        // Convertimos el archivo a texto y lo agregamos al documento PDF
        $texto_archivo = file_get_contents($ubicacion_archivo);
        $pdf->writeHTML($texto_archivo, true, false, true, false, '');
        
        // Enviamos el PDF al navegador
        $pdf->Output("$nombre_archivo.pdf", 'I');
    } else {
        echo "El archivo debe ser un documento de Word o Excel.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Subir archivo y convertir a PDF</title>
</head>
<body>
    <form method="post" enctype="multipart/form-data">
        <label>Selecciona un archivo en Word o Excel:</label>
        <input type="file" name="archivo">
        <br><br>
        <input type="submit" value="Subir archivo y convertir a PDF">
    </form>
</body>
</html>
