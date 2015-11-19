<?php
/*
 * Fonction get_couleur()
 * -----
 * Retourne un tableau des différentes couleur
 * -----
 *
 * -----
 * @return  array                       liste des couleurs
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function get_couleur() {

    $tableau = file('../data/couleur.dat');

    foreach ($tableau as $key=>$value) {
        $tmp[trim($value)]=ucfirst(trim($value));
    }

    return $tmp;
}

/*
 * Fonction  get_poste()
 * -----
 * Retourne un tableau de suggestions de postes (pour le champ input avec la clef suggest)
 * -----
 *
 * -----
 * @return  array                       liste des postes
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function get_poste() {
    return array(
        'Chef de projet',
        'Développeur',
        'Démonstrateur',
        'Commercial',
        'Stagiaire',
        'Directeur',
        'Formateur',
        'Webmestre',
        'Webdesigner',
        'Administrateur système',
        'Administrateur réseau',
        'Administrateur bases de données',
    );
}

/*
 * Fonction  yml(&$data, &$legende, &$name)
 * -----
 * Export au format yml
 * -----
 * @param  array                            la liste des données
 * @param  array                            la liste des legendes
 * @param  string                           le nom du context (utile pour le nomage de l'export)
 * -----
 * @return  string                          le flux au format désiré
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function yml(&$data, &$legende, &$name) {
    $tmp = '';
    $tmp .= "%YAML 1.1\n";
    $tmp .= "---\n";

    foreach($data as $a => $b) {
        $tmp .= " user_id: ".$b['user_id']."\n";
        foreach($legende as $c => $d) {
            if ($d['export'] == 1) {
                $tmp .= " $d[field]: \"".$b[$d['field']]."\"\n";
            }
        }
        $tmp .= "-\n";
    }
    
    $tmp = substr($tmp, 0, -2). "...\n\n";

    //
    // Envoi des entêtes et du flux
    //

    header('content-disposition: attachment; filename='.$name.'_'.date('YmdHis').'.yml');
    header('content-type: text/yml; charset=utf-8');
    echo $tmp;
}
?>