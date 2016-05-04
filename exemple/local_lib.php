<?php
/*
 * Fonction rename_example($data)
 * -----
 * Génération d'un nom de fichier (pour renomage après upload)
 * -----
 * @param   string      $data                   nom du champ upload
 * -----
 * @return  string                              nom du fichier final
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function rename_example($data) {
    return mb_strtolower(microtime(TRUE).strrchr($_FILES[$data.'_tmp']['name'], '.'),'UTF-8');
}
?>