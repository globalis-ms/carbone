<?php

/**
 * class gxml
 *
 * @author ludovic
 **/

class gxml {

    // Object parser
    var $parser;
    var $node = array();
    var $index = 0;
    var $current_node = 0;
    var $exception = array();
    var $deport_parsing = FALSE;
    var $success = FALSE;

    /*
     * Méthode __construct($exception, $encoding)
     * -----
     * Constructeur
     * -----
     * @param   array   $exception              Tableau des nodes parents dont les nodes enfants ne doivent pas être parser (vide par défaut)
     * @param   string  $encoding               Encodage à utiliser lors du parsing du flux xml (ISO-8859-1 par défaut)
     * -----
     * @return  void
     * -----
     * $Author: ludovic $
     * $Copyright: GLOBALIS media systems $
     */

    function __construct($exception = array(), $encoding = 'ISO-8859-1') {
        $this->parser = xml_parser_create($encoding);
        $this->exception = $exception;
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, FALSE);
        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, "_tag_open", "_tag_close");
        xml_set_character_data_handler($this->parser, "_cdata");
    }

    /*
     *  ----------------
     *  Méthodes privées
     *  ----------------
     */

    function _tag_open($parser, $tag, $attributes) {
        $tag = strtolower($tag);
        $attributes = array_change_key_case($attributes, CASE_LOWER);
        if($this->deport_parsing) {

            $tab_attributes = array();
            foreach($attributes as $key => $value) {
                $tab_attributes[] = $key.'="'.$value.'"';
            }

            if(strtolower($tag) == 'br') {
                $this->deport_parsing .= '<br />';
            } else {
                $this->deport_parsing .= '<'.$tag.' '.implode(' ',$tab_attributes).'>';
            }

        } else {

            $last_node = $this->current_node;
            $this->index++;

            $element = array(
                'tag' => $tag,
                'level' => 0,
                'attributes' => $attributes,
                'cdata' => FALSE,
                'child' => array()
            );

            if(in_array($tag, $this->exception)) {
                $this->deport_parsing = ' ';
            }

            $this->current_node = $this->index;

            if($last_node) {
                $this->node[$last_node]['child'][] = $this->current_node;
                $element['parent'] = $last_node;
                $element['level'] = $this->node[$last_node]['level']+1;
            }

            $element['index'] = $this->index;
            $this->node[$this->index] = $element;

        }

    }

    function _cdata($parser, $cdata) {
        if($this->deport_parsing) {
            $this->deport_parsing .= $cdata;
        } else {
            $actual_data = $this->node[$this->current_node]['cdata'];

            if(!count($actual_data))
                $this->node[$this->current_node]['cdata'] = $cdata;
            else
                $this->node[$this->current_node]['cdata'] .= $cdata;

        }
    }

    function _tag_close($parser, $tag) {
        $tag = strtolower($tag);
        if(in_array($tag, $this->exception)) {
            $this->node[$this->current_node]['cdata'] = $this->deport_parsing;
            $this->deport_parsing = FALSE;
        }
        if($this->deport_parsing) {
            if(strtolower($tag) != 'br') {
                $this->deport_parsing .= '</'.$tag.'>';
            }
        } else {
            $this->node[$this->current_node]['cdata'] = trim($this->node[$this->current_node]['cdata']);
            if(isset($this->node[$this->current_node]['parent']))
                $this->current_node = $this->node[$this->current_node]['parent'];
        }
    }

    /*
     *  ------------------
     *  Méthodes publiques
     *  ------------------
     */

    /*
     * Méthode parse($data)
     * -----
     * Lance le parsing d'un flux xml
     * -----
     * @param   string  $data           flux xml à parser
     * @param   boolean $parser_free    indique si le parser doit être détruit après le parsing, true par défaut
     * -----
     * @return  boolean                 TRUE si le parsing a été effectué correctement, FALSE sinon
     * -----
     * $Author: ludovic $
     * $Copyright: GLOBALIS media systems $
     */

    function parse($data, $parser_free = TRUE) {
        $this->success = xml_parse($this->parser, $data);
        if($parser_free) {
            $this->parser_free();
        }
        return $this->success;
    }

    /*
     * Méthode error()
     * -----
     * Retourne la dernière erreur levée par le parseur
     * -----
     * @param   void
     * -----
     * @return  string              Code erreur et libellé de l'erreur
     * -----
     * $Author: ludovic $
     * $Copyright: GLOBALIS media systems $
     */

    function error() {
        $code_error = xml_get_error_code($this->parser);
        return '['.$code_error.'] => '.xml_error_string($code_error);
    }

    /*
     * Méthode parser_free()
     * -----
     * Tentative de destruction de l'analyseur courant.
     * -----
     * @return  boolean                 Cette méthode retourne FALSE si $this->parser n'est pas une référence valide ou, sinon, détruit l'analyseur et retourne TRUE.
     * -----
     * $Author: ludovic $
     * $Copyright: GLOBALIS media systems $
     */

    function parser_free() {
        return xml_parser_free($this->parser);
    }

    /*
     * Méthode get_element($index)
     * -----
     * Retourne les données d'un node du fichier xml à partir de son index dans le tableau de parsing
     * -----
     * @param   int $index          Numéro d'index du node recherché
     * -----
     * @return  mixed               Données du node (array) ou false si l'index n'existe pas
     * -----
     * $Author: ludovic $
     * $Copyright: GLOBALIS media systems $
     */

    function get_element($index) {
        if(isset($this->node[$index])) {
            return $this->node[$index];
        }
        return FALSE;
    }

    /*
     * Méthode find_element($tag_name, $filter, $attr, $first, $return_cdata)
     * -----
     * Retourne une liste de nodes correspondant au paramètres passés
     * -----
     * @param   string      $tag_name                   tag name des nodes recherchés
     * @param   array       $filter                     permet de filtrer par rapport aux données du tableau des nodes (tag, level, etc)
     * @param   array       $attr                       permet de filtrer par rapport aux attributs xml des nodes
     * @param   boolean     $first                      permet de retourner uniquement le premier node correspondant
     * @param   boolean     $return_cdata               permet de retourner uniquement le cdata des nodes correspondants
     * -----
     * @return  array                                   tableau des nodes correspondants
     * -----
     * $Author: ludovic $
     * $Copyright: GLOBALIS media systems $
     */

    function find_element($tag_name, $filter = array(), $attr = array(), $first = FALSE, $return_cdata = FALSE) {
        $result = array();
        if(count($this->node)) {
            foreach($this->node as $index => $element) {
                if(mb_strtoupper($element['tag'],'UTF-8') == mb_strtoupper($tag_name,'UTF-8')) {
                    $correct = TRUE;
                    if(is_array($attr) && count($attr)) {
                        foreach($attr as $id => $value) {
                            if(!isset($element['attributes'][$id]) || $element['attributes'][$id] != $value) {
                                $correct = FALSE;
                            }
                        }
                    }
                    if(is_array($filter) && count($filter)) {
                        foreach($filter as $type => $value) {
                            if(!isset($element[$type]) || $element[$type] != $value) {
                                $correct = FALSE;
                            }
                        }
                    }
                    if($correct) {
                        if($return_cdata) {
                            $result[] = $element['cdata'];
                        } else {
                            $result[] = $element;
                        }
                    }
                }
            }
        }
        if($first && isset($result[0])) {
            return $result[0];
        }
        return $result;
    }

    /*
     * Méthode find_child($element_index, $path, $attr, $first, $return_cdata)
     * -----
     * Retourne une liste de nodes correspondant au paramètres passés
     * -----
     * @param   int         $element_index              numéro d'index du node parent
     * @param   string      $path                       chemin xml à partir du node parent des nodes enfants recherchés
     * @param   array       $attr                       permet de filtrer par rapport aux attributs xml des nodes
     * @param   boolean     $first                      permet de retourner uniquement le premier node correspondant
     * @param   boolean     $return_cdata               permet de retourner uniquement le cdata des nodes correspondants
     * -----
     * @return  array                                   tableau des nodes correspondants
     * -----
     * $Author: ludovic $
     * $Copyright: GLOBALIS media systems $
     */

    function find_child($element_index, $path, $attr = array(), $first = FALSE, $return_cdata = FALSE) {

        if(isset($this->node[$element_index])) {

            $element_root = $this->node[$element_index];
            $list_path = explode('/', $path);
            $tmp = array();
            $current_root = array($element_root);

            for($i = 0; $i < count($list_path); $i++) {
                $current_path = $list_path[$i];
                $next_root = array();

                foreach($current_root as $root) {

                    foreach($root['child'] as $child_index) {

                        if($this->node[$child_index]['tag'] == $current_path) {

                            if($i == count($list_path)-1) {
                                $correct = TRUE;

                                if(is_array($attr) && count($attr)) {

                                    foreach($attr as $id => $value) {
                                        if(!isset($this->node[$child_index]['attributes'][$id]) || $this->node[$child_index]['attributes'][$id] != $value) {
                                            $correct = FALSE;
                                        }
                                    }
                                }

                                if($correct) {

                                    if($return_cdata) {
                                        $tmp[] = $this->node[$child_index]['cdata'];
                                    } else {
                                        $tmp[] = $this->node[$child_index];
                                    }
                                }

                            } else {
                                $next_root[] = $this->node[$child_index];
                            }
                        }
                    }
                }
                $current_root = $next_root;
            }

            if(count($tmp)) {
                if($first) {
                    return $tmp[0];
                }
                return $tmp;
            }
        }

        return FALSE;
    }

    /*
     * Méthode to_iso($flux, $charset, $method)
     * -----
     * Convertie un flux xml en iso-8859-1
     * -----
     * @param   string  $flux                      Flux à convertir
     * @param   string  $charset                   Charset du flux entrant (utf-8 par défaut)
     * @param   string  $method                    Méthode PHP à utiliser (iconv ou utf8_decode)
     * -----
     * @return  mixed                              Chaîne du flux converti ou false s'il y a une erreur
     * -----
     * $Author: ludovic $
     * $Copyright: GLOBALIS media systems $
     */

    function to_iso($flux, $charset_in="utf-8", $method='iconv') {

        // Remove BOM
        $flux = str_replace("\xef\xbb\xbf", '', $flux);

        // Convert charset
        if($method == "iconv") {
            $flux = iconv($charset_in, 'ISO-8859-1//TRANSLIT', $flux);
        } else if($method == "decode") {
            $flux = utf8_decode($flux);
        } else {
            return FALSE;
        }

        // replace utf
        $flux = str_replace($charset_in, 'iso-8859-1', $flux);

        return $flux;

    }

    /*
     * Méthode debug()
     * -----
     * Affiche le contenu du tableau des nodes
     * -----
     * @return  void
     * -----
     * $Author: ludovic $
     * $Copyright: GLOBALIS media systems $
     */

    function debug() {
        echo '<pre>';
        var_dump($this->node);
        echo '</pre>';
    }

} // END class
?>