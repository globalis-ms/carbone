<?php
//
// Export Excel
//

$tmp='';
$tmp.='
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns:o="urn:schemas-microsoft-com:office:office" 
    xmlns:x="urn:schemas-microsoft-com:office:excel" 
    xmlns="http://www.w3.org/TR/REC-html40">
    <head>
        <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
        <style id="Classeur1_Styles">
        .excel { mso-number-format:\@; }
        </style>
    </head>
    <body>
    <div id="Classeur1" align=center x:publishsource="Excel">
';
$tmp.='<table border=1px cellpadding=0 cellspacing=0 width=100% style="border-collapse: collapse">';
$tmp.='<tr>';
$champ = array();
foreach($legende as $k => $v){
    if($legende[$k]['export']) {
        $tmp.='<th class=excel nowrap bgcolor="'.$couleur.'">'.$legende[$k]['label'].'</th>';
        $champ[$legende[$k]['field']] = '';
    }
}
$tmp.='</tr>';
foreach($data as $k => $v){
    $tmp.='<tr>';
    foreach($champ as $field => $v2){
        $tmp.='<td class=excel nowrap>'.$data[$k][$field].'</td>';
    }
    $tmp.='</tr>';
}
$tmp.='</table></div></body></html>';

//
// Envoi des entêtes et du flux
//

header('content-disposition: attachment; filename='.$name.'_'.date('YmdHis').'.xls');
header('content-type: application/ms-excel;');
echo $tmp;
?>