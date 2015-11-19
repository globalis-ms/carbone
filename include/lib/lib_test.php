<?php
/*
 * Fonction test_upload($name, $element)
 * -----
 * Permet de tester si l'upload est correct ou non
 * -----
 * @param   string      $name                   nom du champ upload
 * @param   array       $structure              structure formulaire du champ upload
 * -----
 * @return  array       $erreur                 tableau comportant les erreurs (vide si aucune erreur)
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function test_upload($name, $element) {

    $erreur=array();

    // Taille supérieur au upload_max_filesize (php)
    if($_FILES[$name.'_tmp']['error']==1)
        $erreur[]=STR_FORM_E_FATAL_UPLOAD_MAX_FILESIZE;

    // Taille supérieur au max_file_size (html) ou à la valeur indiquée dans la structure
    if($_FILES[$name.'_tmp']['error']==2 || (isset($element['maxsize']) && is_int($element['maxsize']) && $_FILES[$name.'_tmp']['size']>$element['maxsize']))
        $erreur[]=STR_FORM_E_FATAL_UPLOAD_FORM_SIZE;

    // Taille nulle (cnrs)
    if($_FILES[$name.'_tmp']['error']!=1 && $_FILES[$name.'_tmp']['size']==0)
        $erreur[]=STR_FORM_E_FATAL_UPLOAD_ZERO_SIZE;

    // Téléchargement partiel
    if($_FILES[$name.'_tmp']['error']==3)
        $erreur[]=STR_FORM_E_FATAL_UPLOAD_PARTIAL;

    // Pas de répertoire temporaire
    if($_FILES[$name.'_tmp']['error']==6)
        $erreur[]=STR_FORM_E_FATAL_UPLOAD_NO_TMP_DIR;

    // Ecriture impossible
    if($_FILES[$name.'_tmp']['error']==7)
        $erreur[]=STR_FORM_E_FATAL_UPLOAD_CANT_WRITE;

    // Type Mime incorrect
    if(isset($element['type']) && !empty($element['type']) && !in_array(strtolower($_FILES[$name.'_tmp']['type']), $element['type'])) {
        if(sizeof($element['type'])>1)
            $erreur[]=sprintf(STR_FORM_E_FATAL_UPLOAD_BAD_TYPE, strtolower($_FILES[$name.'_tmp']['type']), 's', 's', implode(', ', $element['type']));
        else
            $erreur[]=sprintf(STR_FORM_E_FATAL_UPLOAD_BAD_TYPE, strtolower($_FILES[$name.'_tmp']['type']), '', '', implode(', ', $element['type']));
    }

    // Type Mime incorrect
    if(isset($element['extension']) && !empty($element['extension']) && !in_array(strtolower(substr(strrchr($_FILES[$name.'_tmp']['name'], '.'), 1)), $element['extension'])) {
        if(sizeof($element['extension'])>1)
            $erreur[]=sprintf(STR_FORM_E_FATAL_UPLOAD_BAD_EXT, '.'.strtolower(substr(strrchr($_FILES[$name.'_tmp']['name'], '.'), 1)), 's', 's', '.'.implode(', .', $element['extension']));
        else
            $erreur[]=sprintf(STR_FORM_E_FATAL_UPLOAD_BAD_EXT, '.'.strtolower(substr(strrchr($_FILES[$name.'_tmp']['name'], '.'), 1)), '', '', '.'.implode(', .', $element['extension']));
    }

    // Si pas d'erreur, on complète la structure $_FILES par divers éléments
    // Le mode de renomage
    // Le chemin de stockage (CFG_PATH_FILE_UPLOAD par défaut)
    if(empty($erreur)) {
        if(isset($element['rename']))
            $_FILES[$name.'_tmp']['rename']=$element['rename'];
        else
            $_FILES[$name.'_tmp']['rename']='';

        if(isset($element['path_file']))
            $_FILES[$name.'_tmp']['path']=$element['path_file'];
        else
            $_FILES[$name.'_tmp']['path']=CFG_PATH_FILE_UPLOAD;
    }

    return($erreur);
}

/*
 * Fonction test_alpha($input)
 * -----
 * Permet de vérifier si la chaine en entrée est bien alpha
 * -----
 * @param   string      $input                  la chaine à tester
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function test_alpha($input) {
    return preg_match('/^[a-zA-Z]+$/', $input);
}

/*
 * Fonction test_alphanum($input)
 * -----
 * Permet de vérifier si la chaine en entrée est bien alphanum
 * -----
 * @param   string      $input                  la chaine à tester
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function test_alphanum($input) {
    return preg_match('/^[a-zA-Z0-9]+$/', $input);
}

/*
 * Fonction test_integer($input)
 * -----
 * Permet de vérifier si la chaine en entrée est bien un entiers naturels
 * -----
 * @param   string      $input                  la chaine à tester
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function test_integer($input) {
    return preg_match('/^[\+\-]?[0-9]+$/', $input);
}

/*
 * Fonction test_integer_positive($input)
 * -----
 * Permet de vérifier si la chaine en entrée est bien un entiers naturel, positif non nul
 * -----
 * @param   string      $input                  la chaine à tester
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: carine $
 * $Copyright: GLOBALIS media systems $
 */

function test_integer_positive($input) {
    return preg_match('/^[0-9]+$/', $input);
}

/*
 * Fonction test_float($input)
 * -----
 * Permet de vérifier si la chaine en entrée est bien un flottant
 * -----
 * @param   string      $input                  la chaine à tester
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function test_float($input) {
    return preg_match('/^[\+\-]?[0-9]+([.,][0-9]+)?$/', $input);
}

/*
 * Fonction test_mail($input)
 * -----
 * Permet de vérifier si la chaine en entrée est une adresse email valide
 * -----
 * @param   string      $input                  la chaine à tester
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function test_mail($input) {
    return preg_match('/^([0-9a-zA-Z]([-.\w]*[_0-9a-zA-Z])*@(([0-9a-zA-Z])+([-\w]*[0-9a-zA-Z])*\.)+[a-zA-Z]{2,9})$/i', $input);
}

/*
 * Fonction test_url($input)
 * -----
 * Permet de vérifier si la chaine en entrée est une url valide (cf RFC 2396)
 * -----
 * @param   string      $input                  la chaine à tester
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function test_url($input) {
    return (preg_match('/^(http|https|ftp)\:\/\/([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.[a-zA-Z]{2,4})(\:[0-9]+)*(\/[^\/][a-zA-Z0-9\.\,\?\'\\/\+&%\$#\=~_\-]*)*\/?$/', $input));
}

/*
 * Fonction test_date($input)
 * -----
 * Permet de vérifier si la chaine en entrée est une date au format dd-mm-yyyy
 * -----
 * @param   string      $input                  la chaine à tester
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: carine $
 * $Copyright: GLOBALIS media systems $
 */

function test_date($input) {
    return preg_match('/^([0-9]{2})[-]([0-9]{2})[-]([0-9]{4})$/', $input, $date) && checkdate($date[2], $date[1], $date[3]);
}

/*
 * Fonction test_iso_date($input)
 * -----
 * Permet de vérifier si la chaine en entrée est une date au format ISO 8601 (yyyy-mm-dd)
 * -----
 * @param   string      $input                  la chaine à tester
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function test_iso_date($input) {
    return preg_match('/^([0-9]{4})[-]([0-9]{2})[-]([0-9]{2})$/', $input, $date) && checkdate($date[2], $date[3], $date[1]);
}

/*
 * Fonction test_iso_time($input)
 * -----
 * Permet de vérifier si la chaine en entrée est une heure au format ISO 8601 (hh:ii:ss)
 * Attention le format ISO 8601 autorise 00:00:00 et 24:00:00 pour minuit.
 * -----
 * @param   string      $input                  la chaine à tester
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function test_iso_time($input) {
    return (preg_match('/^(([0-1][0-9])|(2[0-3])):[0-5][0-9]:[0-5][0-9]$/', $input) || ($input=='24:00:00'));
}

/*
 * Fonction test_date_time($input)
 * -----
 * Permet de vérifier si la chaine en entrée est une date au format dd-mm-yyyy hh:ii:ss
 * Attention sont autorisées 00:00:00 et 24:00:00 pour minuit.
 * -----
 * @param   string      $input                  la chaine à tester
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: carine $
 * $Copyright: GLOBALIS media systems $
 */

function test_date_time($input) {
    $date = substr($input, 0, 10);
    $sep  = substr($input, 10, 1);
    $time = substr($input, 11, 8);

    if (strlen($input) == 19 && test_date($date) && $sep == ' ' && test_iso_time($time))
        return TRUE;
    else
        return FALSE;
}
/*
 * Fonction test_iso_date_time($input)
 * -----
 * Permet de vérifier si la chaine en entrée est une date au format ISO 8601 (yyyy-mm-dd hh:ii:ss)
 * Attention le format ISO 8601 autorise 00:00:00 et 24:00:00 pour minuit.
 * -----
 * @param   string      $input                  la chaine à tester
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function test_iso_date_time($input) {
    $date = substr($input, 0, 10);
    $sep  = substr($input, 10, 1);
    $time = substr($input, 11, 8);

    if (strlen($input) == 19 && test_iso_date($date) && $sep == ' ' && test_iso_time($time))
        return TRUE;
    else
        return FALSE;
}

/*
 * Fonction is_positive($input)
 * -----
 * Permet de vérifier si la variable en entrée est positive (entier ou flottant).
 * -----
 * @param   string      $input                  la variable à tester
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: romain $
 * $Copyright: GLOBALIS media systems $
 */

function is_positive($input) {
    if(preg_match('/^[\-][0-9]+[\.]?[0-9]+$/', $input))
        return FALSE;
    else
    	return TRUE;
}

/*
 * Fonction is_exist($input)
 * -----
 * Permet de vérifier si la variable en entrée existe.
 * Attention, 0 (entier) ou "0" (chaine) retourne empty par défaut.
 * D'ou l'utilité de cette fonction
 * -----
 * @param   string      $input                  la variable à tester
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function is_exist($input) {
    if (isset($input)) {
        if (empty($input) && ($input !== 0) && ($input !== '0'))
           return FALSE;
       else
           return TRUE;
    }
    else
       return FALSE;
}

/*
 * Fonction test_length($input, $limit)
 * -----
 * Permet de vérifier si la longueur de la chaine en entrée
 * Est égale à la valeur limite
 * -----
 * @param   string      $input                  la chaine à tester
 * @param   int         $limit                  la limite
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function test_length($input, $limit) {
    if(mb_strlen($input, 'UTF-8') == $limit)
        return TRUE;
    else
        return FALSE;
}

/*
 * Fonction test_max_length($input, $limit)
 * -----
 * Permet de vérifier si la longueur de la chaine en entrée
 * Est inférieure ou égale à la valeur limite
 * -----
 * @param   string      $input                  la chaine à tester
 * @param   int         $limit                  la limite
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function test_max_length($input, $limit) {
    if(mb_strlen($input, 'UTF-8') <= $limit)
        return TRUE;
    else
        return FALSE;
}

/*
 * Fonction test_min_length($input, $limit)
 * -----
 * Permet de vérifier si la longueur de la chaine en entrée
 * Est supérieure ou égale à la valeur limite
 * -----
 * @param   string      $input                  la chaine à tester
 * @param   int         $limit                  la limite
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function test_min_length($input, $limit) {
    if(mb_strlen($input, 'UTF-8') >= $limit)
        return TRUE;
    else
        return FALSE;
}

/*
 * Fonction test_ip($input)
 * -----
 * Permet de vérifier si la chaine en entrée est bien une ip valide
 * -----
 * @param   string      $input                  l'ip à tester
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function test_ip($input) {
    if(long2ip(ip2long($input))==$input)
        return TRUE;
    else
        return FALSE;
}

/*
 * Fonction test_password($input)
 * -----
 * Permet de vérifier si la chaine en entrée est bien un password valide
 * -----
 * @param   string      $input                  le password à tester
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function test_password($input) {
    if(mb_strlen($input, 'UTF-8') >= 4)
        return TRUE;
    else
        return FALSE;
}
?>