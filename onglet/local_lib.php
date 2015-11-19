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
    return utf8_encode_mixed(array(
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
    ));
}
?>