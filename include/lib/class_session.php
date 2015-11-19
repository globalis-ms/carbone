<?php
/*
 * Classe session()
 * -----
 * Classe de gestion des sessions (compatible PHP3, PHP4 et PHP5)
 * -----
 *
 * -----
 *
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

class session {

    // {{{ properties

    var $session_name           = CFG_SESSION_NAME;         // Nom de la variable de session passée en GET
    var $session_table_name     = CFG_SESSION_TABLE;        // Nom de la table de session
    var $session_delay          = CFG_SESSION_DELAY;        // Delais de validite d'une session
    var $session_trans          = CFG_SESSION_TRANS;        // Mode de transmission : url ou cookie
    var $session_level          = CFG_SESSION_LEVEL;        // Mode de sécuité : 0 = standard, 1 = contrôle HTTP_USER_AGENT, 2 = session volatile, 3 = session volatile + contrôle HTTP_USER_AGENT
    var $session_path           = CFG_SESSION_PATH;         // Exemple /carbone_v44/ (utile uniquement pour le mode cookie)

    var $session_variable       = array();                      // Tableau associatif qui contiendra les variables de session
    var $session_expired        = FALSE;                        // Flag qui annonce que la session a été expiré, toutes les données sont donc perdue, un nouvelle session est ouverte
    var $session_id             = FALSE;                        // Identifiant de session
    var $session_id_old         = FALSE;                        // Identifiant de session précédent
    var $session_action         = 'INSERT INTO';                // Action initiale lors du stop

    // }}}
    // {{{ open()

    /**
     * Debut de la session
     * Initialisation avec un nouvel identifiant de session
     * Ou utilisation de l'ancien et recuperation de variables enregistrées
     *
     * @param   string
     *
     * @return  string
     */

    function open() {
        global $db;

        // Bit sur contrôle HTTP_USER_AGENT à 1

        if((($this->session_level&1)==1) && $this->session_id!='') {
            $user_agent=substr(md5($_SERVER['HTTP_USER_AGENT']), 0, 4);
            $prefix_session=substr($this->session_id,0,8);
            $suffix_session=substr($this->session_id, -4);

            $user_agent=sprintf("%08x", hexdec($suffix_session)*hexdec($user_agent));

            if($user_agent!==$prefix_session)
                $control_user_agent=FALSE;
            else
                $control_user_agent=TRUE;
        }
        else
            $control_user_agent=TRUE;

        if($this->session_id && $control_user_agent==TRUE)
        {
            // Requete dans la base pour recuperer les variables de session
            $sql = 'SELECT data FROM '.$this->session_table_name.' WHERE ';
            $sql.= 'session_id = '.$db->qstr($this->session_id).' ';
            $sql.= 'AND ';
            $sql.= 'expire > '.$db->qstr(date('YmdHis'));

            $row=$db->getone($sql);

            // Session valide et non expirée
            if ($row!==FALSE) {
                $this->session_action = 'UPDATE';
                $this->session_variable = unserialize(base64_decode($row));

                if(is_array($this->session_variable)) {
                    foreach($this->session_variable as $var => $val)
                        $GLOBALS[$var] = $val;
                }

                $this->session_id_old = $this->session_id;

                // Bit sur session volatile à 1

                if(($this->session_level&2)==2) {
                    $this->session_id = $this->set_session_id();
                    if($this->session_trans=='cookie')
                        setcookie($this->session_name, $this->session_id, 0, $this->session_path);
                }
            }
            // Session valide mais expirée
            else {
                $this->destroy();
                $this->session_id = $this->set_session_id();
                $this->session_expired = TRUE;

                if($this->session_trans=='cookie')
                    setcookie($this->session_name, $this->session_id, 0, $this->session_path);
            }
        }
        else {
            $this->session_id = $this->set_session_id();
            $this->session_expired = TRUE;

            if($this->session_trans=='cookie')
                setcookie($this->session_name, $this->session_id, 0, $this->session_path);
        }

        //
        // Contrôle eventuelle du token de session
        //

        if(($this->session_level&4)==4) {
            if(isset($_GET['token']) && ($_GET['token']!=$this->session_variable['session_token']))
                die('<b>Security error</b>: CSRF, invalid token');
        }
    }

    // }}}
    // {{{ register()

    /**
     * Enregistre une variable dans la session.
     *
     * @param   string
     *
     * @return  string
     */

    function register($var, $value=FALSE) {
        if($value!==FALSE)
            $GLOBALS[$var]=$value;

        $this->session_variable[$var] = $GLOBALS[$var];

        return TRUE;
    }

    // }}}
    // {{{ unregister()

    /**
     * Supprime une variable dans la session
     *
     * @param   string
     *
     * @return  string
     */

    function unregister($var) {
        unset($this->session_variable[$var]);
        return TRUE;
    }

    // }}}
    // {{{ get_var()

    /**
     * Retourne la valeur de la variable passée en argument.
     * Cette valeur peut etre une chaine un entier ou même un tableau.
     *
     * @param   string
     *
     * @return  string
     */

    function get_var($var) {
        if(isset($this->session_variable[$var])) return $this->session_variable[$var];
        else return FALSE;
    }

    // }}}
    // {{{ url()

    /**
     * Ajout de l'id de session dans l'url en méthode GET.
     *
     * @param   string      $url                    url à compléter
     * @param   string      $html                   TRUE par defaut (remplace les & par des &amp;) ou FALSE
     *
     * @return  string
     */

    function url($url='', $html=TRUE) {

        //
        // On ne garde que le premier ?
        //

        $question_mark=substr_count($url,'?');

        if($question_mark>1) {
            $url=strrev(preg_replace('/\?/', ';pma&', strrev($url), ($question_mark-1)));
        }

        //
        // Si mode url
        //

        if($this->session_trans=='url') {
            if(strpos($url,'?'))
                $url = str_replace('?', '?'.urlencode($this->session_name).'='.$this->session_id.'&amp;', $url);
            else
                $url.= '?'.urlencode($this->session_name).'='.$this->session_id;

        }

        if(!$html) {
            $url=str_replace('&amp;', '&', $url);
        }

        return $url;
    }

    // }}}
    // {{{ get_session_id()

    /**
     * Retourne l'ID de session.
     *
     * @param   string
     *
     * @return  string
     */

    function get_session_id() {
        return $this->session_id;
    }

    // }}}
    // {{{ set_session_id()

    /**
     * Fabrique l'ID de session.
     *
     * @param   string
     *
     * @return  string
     */

    function set_session_id() {

        $this->session_id=md5(uniqid(''));

        // On evite les sessions se terminant par 4 zero

        while(substr($this->session_id, -4)==="0000") {
            $this->session_id=md5(uniqid(''));
        }

        // Bit sur le contrôle HTTP_USER_AGENT à 1

        if(($this->session_level&1)==1) {

            // On ajoute un peu de bruit

            $user_agent=substr(md5($_SERVER['HTTP_USER_AGENT']), 0, 4);
            $suffix_session=substr($this->session_id, -4);
            $user_agent=sprintf("%08x", hexdec($suffix_session)*hexdec($user_agent));

            $this->session_id=$user_agent.$this->session_id;
        }

        return $this->session_id;
    }

    // }}}
    // {{{ close()

    /**
     * Enregistrement des données (variables), et mise a jour du delai de fin de session.
     *
     * @param   string
     *
     * @return  string
     */

    function close() {
        global $db;

        if(isset($this->session_id))
        {
            $sql = $this->session_action.' '.$this->session_table_name.' ';

            // UPDATE
            if($this->session_action=='UPDATE') {
                $sql .= 'SET ';
                $sql .= 'session_id         = '.$db->qstr($this->session_id).', ';
                $sql .= 'data               = '.$db->qstr(addslashes(base64_encode(serialize($this->session_variable)))).', ';
                $sql .= 'expire             = '.$db->qstr(date('YmdHis',mktime(date('H'),date('i'),(date('s')+$this->session_delay),date('m'),date('d'),date('Y')))).' ';
                $sql .= 'WHERE session_id   = '.$db->qstr($this->session_id_old);
            }

            // INSERT
            else {

                // Bit sur le token de session à 1

                if(($this->session_level&4)==4)
                    $this->register('session_token', substr(md5(uniqid()), 0, 8));

                $sql .= '(session_id, data, expire) ';
                $sql .= 'VALUES (';
                $sql .= $db->qstr($this->session_id).', ';
                $sql .= $db->qstr(addslashes(base64_encode(serialize($this->session_variable)))).', ';
                $sql .= $db->qstr(date('YmdHis',mktime(date('H'),date('i'),(date('s')+$this->session_delay),date('m'),date('d'),date('Y'))));
                $sql.= ')';
            }

            $db->execute($sql);
        }
    }

    // }}}
    // {{{ logout()

    /**
     * Suppression de la session dans la table.
     *
     * @param   string
     *
     * @return  string
     */

    function destroy() {
        global $db;

        // Purge des sessions qui traine

        $sql = 'DELETE FROM '.$this->session_table_name.' WHERE ';
        $sql.= 'expire < '.$db->qstr(date('YmdHis'));

        $db->execute($sql);

        // Purge de la session en court

        $sql = 'DELETE FROM '.$this->session_table_name.' WHERE ';
        $sql.= 'session_id = '.$db->qstr($this->session_id_old);

        $db->execute($sql);

        unset($this->session_id);
    }

    // }}}
    // {{{ __construct()

    /**
     * Constructeur, identifiant de session en cour, nom de l'instance de l'objet db.
     *
     * @param   string
     *
     * @return  string
     */

    function __construct() {
        // Nom de la table session
        $this->session_table_name = CFG_SESSION_TABLE;

        // Delais de validité de la session
        $this->session_delay = CFG_SESSION_DELAY;

        // Nom de la variable de session passé en GET
        // Initialisation de la session avec l'id passé en GET

        if($this->session_trans!='cookie' && !empty($_GET[$this->session_name])) {
            $this->session_id = strip_tags($_GET[$this->session_name]);
        }
        elseif($this->session_trans=='cookie' && !empty($_COOKIE[$this->session_name])) {
            $this->session_id = strip_tags($_COOKIE[$this->session_name]);
        }

        $this->open();
    }
    // }}}
    // {{{ count()

    /**
     * Methode permettant de compter le nombre de sessions actives
     *
     * @return  int     le nombre de session en cours
     */
    function count() {
        global $db;

        $sql = 'SELECT COUNT(session_id) AS nb FROM '.$this->session_table_name.' WHERE ';
        $sql.= 'expire > '.$db->qstr(date('YmdHis')).' ';
        $sql.= 'AND ';
        $sql.= 'data != '.$db->qstr('YTowOnt9');

        return $db->getone($sql);
    }
    // }}}
    // {{{ user()

    /**
     * Methode permettant de lister les utilisateurs actifs
     *
     * @return  array     les infos des utilisateurs actifs
     */
    function user() {
        global $db;

        $user=array();

        $sql = 'SELECT session_id, data, expire FROM '.$this->session_table_name.' WHERE ';
        $sql.= 'expire > '.$db->qstr(date('YmdHis')).' ';
        $sql.= 'ORDER BY expire DESC';

        $recordset = $db->execute($sql);

        while($row = $recordset->fetchrow()) {
            $data=unserialize(base64_decode($row['data']));
            $foo=$row['expire'];
            $bar=mktime(substr($foo, 8, 2), substr($foo, 10, 2), substr($foo, 12, 2), substr($foo, 4, 2), substr($foo, 6, 2), substr($foo, 0, 4));
            $last=date('H:i:s',($bar-CFG_SESSION_DELAY)).'<br>';
            if(isset($data['session_user_id']) && !isset($user[$data['cfg_profil']['nom'].' '.$data['cfg_profil']['prenom']])) {
                $data['cfg_profil']['expire']=$last;
                $user[$data['cfg_profil']['nom'].' '.$data['cfg_profil']['prenom']]=$data['cfg_profil'];
            }
        }

        return $user;
    }

    // }}}
}
?>