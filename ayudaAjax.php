<?php
sleep(1);//pausar la carga
include_once("include/conn.php");
$link = Conectarse();
$comilladob = '"';
$comillasim = "'";

$rs = mysql_query("SELECT * FROM manuales WHERE manual_estado=1", $link);
$contar = mysql_num_rows($rs);
    if ($contar === 0) {
        echo "No se han encontrado Documentos.";
    } else {        
        echo '<table id="tabladoc" class="table2d">';
        echo '<thead><tr><th style="width:80px;">N° Manual.</th><th style="width:450px;">Nombre Manual</th><th style="width:100px; word-wrap: break-word;" data-sorter="false" data-filter="false">Archivos:</th></tr></thead>';
        while ($row = mysql_fetch_array($rs)) {
            $link1 = str_replace("..", ".",$row["manual_url"]);
            printf("<tr><td><i>%s</i></td><td align='left'><b>%s</b></td><td>%s</td></tr>", $row["manual_id"], $row["manual_nombre"], "<a href=".$link1." target='_blank' title='Ver Manual' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=720, width=960 left=10 top=10" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='ver documento' title='ver Documento' src='img/pdf.png' width='25' height='24'></a>");
        }
    }
    echo '</table>';
    echo '<script type="text/javascript">$(document).ready(function () {$("#tabladoc").tablesorter();});</script>';
mysql_close($link);
?>