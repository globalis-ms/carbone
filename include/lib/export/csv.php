<?php
//
// Export CSV
//

$tmp='';
$champ = array();
foreach($legende as $k => $v){
    if($legende[$k]['export']) {
        $tmp.="\"".str_replace('"', '\"', $legende[$k]['label'])."\";";
        $champ[$legende[$k]['field']] = '';
    }
}
$tmp.="\r\n";
foreach($data as $k => $v){
    foreach($champ as $field => $v2){
        $tmp.="\"".str_replace('"', '\"', $data[$k][$field])."\";";
    }
    $tmp.="\r\n";
}

//
// Envoi des entêtes et du flux
//

header('content-disposition: attachment; filename='.$name.'_'.date('YmdHis').'.csv');
header('content-type: text/csv; charset=utf-8');
echo $tmp;
?>