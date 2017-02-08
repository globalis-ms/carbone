<?php
/*
 * Fonction form_template($require, $tpl, $libelle, $label, $legende, $form)
 * -----
 * Mise en forme de l'élément formulaire en fonction du template
 * -----
 * @param   string      $require                champ requis ou non
 * @param   string      $tpl                    template d'affichage
 * @param   string      $label                  label du champ
 * @param   string      $libelle                libellé du champ
 * @param   string      $legende                legende
 * @param   string      $form                   la chaine formulaire
 * -----
 * @return  string                              le flux HTML de mise en forme
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function form_template($require, $tpl, $libelle, $label, $legende, $form, $name, $accesskey) {

    // Construction de la structure checkbox

    $tr_open=FALSE;
    $tr_close=FALSE;
    $td_close=TRUE;

    if(strstr($tpl, '[')) {
        $tr_open=TRUE;
        $tpl = str_replace('[', '', $tpl);
    }

    if(strstr($tpl, ']')) {
        $tr_close=TRUE;
        $tpl = str_replace(']', '', $tpl);
    }

    if(substr($tpl, -3, 3)=='...') {
        $td_close=FALSE;
        $tpl = substr($tpl, 0, -3);
    }

    if(strstr($tpl, '(') && strstr($tpl, ')')) {
        $search = "/\(([^)]*)\)/msi";
        preg_match_all($search, $tpl, $matches);

        foreach ($matches[0] as $key => $value) {
            $cols = explode('|', $matches[1][$key]);
            $classe = '';
            foreach($cols as $col) {
                $tmp = explode(',', $col);
                if(!isset($tmp[1])) {
                    $tmp[1] = 'sm';
                }
                $classe.='col-'.$tmp[1].'-'.$tmp[0].' ';
            }

            $tpl = str_replace($value, "</div><div class=\"".$classe."\">", $tpl);
        }

        $tpl = ltrim($tpl);
        if(substr($tpl, 0, 6) == "</div>")
            $tpl = substr($tpl, 6);

    }

    if($td_close) {
        $tpl=$tpl."</div>";
    }

    if($tr_open)
        $tpl="<div class=\"form-group\" data-field=\"".$name."\">".$tpl;

    if($tr_close)
        $tpl=$tpl."</div>\n";

    // Remplacement des éléments

    if($require != '')
        $libelle=$libelle.' '.STR_FORM_REQUIRE_SYMBOL;

    if($accesskey!='')
        $tpl = str_replace('{libelle}', ' <label class="control-label" for="'.$name.'" accesskey="'.$accesskey.'">'.$libelle.'</label>', $tpl);
    else
        $tpl = str_replace('{libelle}', ' <label class="control-label" for="'.$name.'">'.$libelle.'</label>', $tpl);

    $tpl = str_replace('{label}', $label, $tpl);

    if(!empty($legende) && $legende[0]=='_')   // Affichage tooltips
        $tpl = str_replace('{legende}', '<span class="glyphicon glyphicon-info-sign" data-toggle="popover" data-placement="right" data-trigger="hover" data-content="'.substr($legende, 1).'"></span>', $tpl);
    else                                        // Affichage Classique
        $tpl = str_replace('{legende}', '<span class="legende">'.$legende.'</span>', $tpl);

    $tpl = str_replace('{form}', $form, $tpl);

    // Retour du template

    return $tpl;
}

/*
 * Fonction form_parser($structure, $visu = FALSE)
 * -----
 * Parsing de la structure du formulaire à mettre en forme
 * -----
 * @param   array       $structure              structure du formulaire
 * @param   boolean     $visu                   si TRUE, mode visu (sinon FALSE, par défaut)
 * -----
 * @return  string                              le flux HTML du formulaire
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function form_parser($structure, $visu = FALSE) {
    global $session;

    // Simplification de la structure

    $structure=form_clean($structure);

    // Cas particulier du mode visualisation
    // On retravaille le tableau $structure afin de présenter uniquement des champs de type info

    if($visu){
        $is_view_back=TRUE;
        $structure_visu=array();
        foreach($structure as $k => $v){

            // On supprime les legendes (sauf pour les fieldset)

            if ($structure[$k]['item'] != 'fieldset'){
                $structure[$k]['legende']='';
            }

            switch($structure[$k]['item']){
                case 'select' :

                    //print_rh($structure[$k]);

                    $structure[$k]['item'] = 'info';
                    $value = '';
                    if(!empty($structure[$k]['value'])){
                        foreach($structure[$k]['value'] as $option_key => $option_value){
                            if($k[0]!='_') {     // Le _ identifie un champ potentiellement dangereux et sujet aux xss
                                // Select multiple
                                if(is_array($structure[$k]['selected']) && in_array($option_key, $structure[$k]['selected'])){
                                    $value.= nl2br(htmlentities($option_value, ENT_COMPAT, 'UTF-8')).'<br />';
                                }
                                // Select simple
                                elseif(is_string($structure[$k]['selected']) && $structure[$k]['selected'] == $option_key){
                                    $value.= nl2br(htmlentities($option_value, ENT_COMPAT, 'UTF-8'));
                                    break;
                                }
                            }
                            else {
                                // Select multiple
                                if(is_array($structure[$k]['selected']) && in_array($option_key, $structure[$k]['selected'])){
                                    $value.= $option_value.'<br />';
                                }
                                // Select simple
                                elseif(is_string($structure[$k]['selected']) && $structure[$k]['selected'] == $option_key){
                                    $value = $option_value;
                                    break;
                                }
                            }
                        }
                    }

                    $structure[$k]['value'] = $value;

                    break;

                case 'radiobox' :

                    $structure[$k]['item'] = 'info';
                    $value = '';
                    if(isset($structure[$k]['radiobox']) && !empty($structure[$k]['radiobox'])){
                        foreach($structure[$k]['radiobox'] as $radiobox){
                            if($structure[$k]['checked'] == $radiobox['value']){
                                $value = $radiobox['label'];
                                break;
                            }
                        }
                    }
                    $structure[$k]['value'] = $value;

                    break;

                case 'checkbox' :

                    $structure[$k]['item'] = 'info';
                    $value = '';
                    if(isset($structure[$k]['checkbox']) && !empty($structure[$k]['checkbox'])){
                        foreach($structure[$k]['checkbox'] as $checkbox){
                            if(is_array($structure[$k]['checked'])) {
                                if(in_array($checkbox['value'], $structure[$k]['checked'])){
                                    $value.= $checkbox['label'].'<br />';
                                }
                            }
                            else {
                                if($structure[$k]['checked'] == $checkbox['value']) {
                                    $value = $checkbox['label'];
                                    break;
                                }
                            }
                        }
                    }
                    $structure[$k]['value'] = $value;

                    break;

                case 'input'    :

                    $structure[$k]['item'] = 'info';
                    $value = $structure[$k]['value'];
                    if(isset($structure[$k]['type']) && $structure[$k]['type'] == 'password'){
                        if($structure[$k]['value'] != ''){
                            $value = str_repeat('*', mb_strlen($structure[$k]['value'],'UTF-8'));
                        }
                    }
                    if($k[0]!='_')          // Le _ identifie un champ potentiellement dangereux et sujet aux xss
                        $structure[$k]['value'] = htmlentities($value, ENT_COMPAT, 'UTF-8');
                    else
                        $structure[$k]['value'] = $value;

                    break;

                case 'textarea' :

                    $structure[$k]['item'] = 'info';
                    if($k[0]!='_')          // Le _ identifie un champ potentiellement dangereux et sujet aux xss
                        $structure[$k]['value'] = nl2br(htmlentities($structure[$k]['value'], ENT_COMPAT, 'UTF-8'));
                    else
                        $structure[$k]['value'] = nl2br($structure[$k]['value']);

                    break;

                case 'upload' :

                    $structure[$k]['item'] = 'info';

                    if($structure[$k]['value'] != ''){

                        $value = $structure[$k]['value'];

                        if(!isset($structure[$k]['path_file']))
                            $path_file = CFG_PATH_FILE_UPLOAD;
                        else
                            $path_file = $structure[$k]['path_file'];

                        if(!isset($structure[$k]['path_http']))
                            $path_http = CFG_PATH_HTTP_UPLOAD;
                        else
                            $path_http = $structure[$k]['path_http'];


                        $structure[$k]['value']  = sprintf("<a class=\"btn btn-default\" data-toggle=\"modal\" data-target=\"#%s\"><i class=\"glyphicon glyphicon-eye-open\"></i> ".STR_FORM_VIEW."</a> \n", crc32($value));
                        $structure[$k]['value'] .= sprintf("<div class=\"modal fade\" id=\"%s\" tabindex=\"-1\" role=\"dialog\" aria-hidden=\"true\">\n", crc32($value));
                        $structure[$k]['value'] .= "<div class=\"modal-dialog\">\n";
                        $structure[$k]['value'] .= "<div class=\"modal-content\">\n";
                        $structure[$k]['value'] .= "<div class=\"modal-header\">\n";
                        $structure[$k]['value'] .= "<button type=\"button\" class=\"close\" data-dismiss=\"modal\"><span aria-hidden=\"true\">&times;</span></button>\n";
                        $structure[$k]['value'] .= "<h4 class=\"modal-title\">".$value."</h4>\n";
                        $structure[$k]['value'] .= "</div>\n";
                        $structure[$k]['value'] .= "<div class=\"modal-body\">\n";

                        $info = get_file_info($path_file.'/'.$value);

                        if (!isset($info['width']) || $info['width'] === FALSE) {
                             $structure[$k]['value'] .= "<p><a href=\"".$path_http.'/'.$value."\" target=\"_blank\">".$value."</a></p>\n";
                        }
                        else {
                            $structure[$k]['value'] .= "<p><img class=\"img-responsive img-thumbnail\" src=\"".$path_http.'/'.$value."\"></p>\n";
                        }
                        $structure[$k]['value'] .= "</div>\n";
                        $structure[$k]['value'] .= "<div class=\"modal-footer\">\n";
                        $structure[$k]['value'] .= "<button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">".STR_OK."</button>\n";
                        $structure[$k]['value'] .= "</div>\n";
                        $structure[$k]['value'] .= "</div>\n";
                        $structure[$k]['value'] .= "</div>\n";
                        $structure[$k]['value'] .= "</div>\n\n";

                        //$structure[$k]['value'] = sprintf("<a href='#' %s><img src='%s' alt='%s' align='absmiddle'></a>\n", "OnClick=\"window.open('".$structure[$k]['path_http'].'/'.$structure[$k]['value']."','','height=".($info['height']+30).",width=".($info['width']+20).",status=yes,toolbar=no,menubar=no,location=no');return false;\"", $tmp_img, $structure[$k]['value']);
                    }

                    break;

                case 'form' :
                    if (isset($structure[$k]['view_back']) && $structure[$k]['view_back'] === FALSE){
                        $is_view_back=FALSE;
                    }

                    unset($structure[$k]);
                    break;

                case 'hidden' :
                    unset($structure[$k]);
                    break;

                case 'button' :
                    if (isset($structure[$k]['view']) && $structure[$k]['view'] === TRUE) {
                        $is_view_back=FALSE;
                    }
                    else {
                        unset($structure[$k]);
                    }
                    break;
            }
            if(isset($structure[$k]['value']) && $structure[$k]['item'] != 'upload'){
                $structure[$k]['value'].= ' ';
            }
            if(isset($structure[$k]['require'])){
                $structure[$k]['require'] = FALSE;
            }
            if(isset($structure[$k])){
                $structure_visu['view_'.$k]=$structure[$k];
            }
        }

        // Traitement éventuel du bouton de retour

        if ($is_view_back === TRUE){

            // Construction du bouton de retour

            $structure_visu['view_back'] = array(
                'item' => 'button',
                'value' => STR_RETOUR,
                'type' => 'button',
                'js' => 'onclick="window.location=\''.$session->url($_SERVER['PHP_SELF']).'\'"',
            );

            // Recherche d'un tpl eligible pour formater le bouton de retour

            foreach($structure_visu as $name => $element) {
                if($element['item'] == 'info') {
                    $tpl_length=mb_strlen($element['tpl'],'UTF-8');
                    if($element['tpl'][0]=='[' && $element['tpl'][$tpl_length-1]==']') {
                        $structure_visu['view_back']['tpl'] = $element['tpl'];
                        break;
                    }
                    else
                        $structure_visu['view_back']['tpl'] = '[(2){libelle}(8){form}(2){legende}]';
                }
            }
        }

        // print_rh($structure_visu);

        $structure=$structure_visu;
    }

    $tooltip=0; // Y-a t-il des tooltips a gerer

    $form = '';
    $form_hidden = '';  // Utilisé pour collecter les champs hidden

    // Détection d'un élément upload dans le formulaire
    $upload_detected = FALSE;
    foreach($structure as $name => $element){
        if($element['item'] == 'upload'){
            $upload_detected = TRUE;
            break;
        }
    }
    reset($structure);

    foreach($structure as $name=>$element) {

        $form_tmp = '';
        $tpl = '';
        $require= '';
        $libelle = '&nbsp;';
        $label = '&nbsp;';
        $legende = '&nbsp;';
        $js = '';
        $accesskey = '';
        $id= '';
        $class= '';
        //On enregistre le nom contextuel du formulaire
        switch ($element['item']) {

            // Champ form
            //
            // 'name' => array(
            //      'item'      => 'form',                          Element de type form
            //      'action'    => '[string]',                      URL action du formulaire
            //      'method'    => '[string]',                      Method (post, get, etc.) / post par défaut
            //      'target'    => '[string]',                      Target du formulaire
            //      'js'        => '[string]',                      Chaine JS avec événements et traitements
            // ),

            case 'form' :

                extract($element);

                if(!empty($format) && $format == 'inline') {
                    $format = 'form-inline';
                }
                else {
                    $format = 'form-horizontal';
                }

                if(empty($method))  $method='post';
                if(empty($target))  $target='_self';
                if(empty($js))      $js='';
                if(empty($class))   $class='well';

                if($upload_detected){
                    $enctype = 'enctype="multipart/form-data"';
                }else{
                    $enctype = '';
                }
                //On enregistre le nom contextuel du formulaire
                $form_name = $name;


                $form = sprintf('<form class="%s %s" role="form" action="%s" method="%s" id="%s" name="%s" target="%s" %s%s>'."\n", $format, $class, $action, $method, $name, $name, $target, $enctype, $js).$form;

                break;

            // Champ hidden
            //
            // 'name' => array(
            //      'item'      => 'hidden',                        Element de type hidden
            //      'value'     => '[string]',                      Valeur
            //  ),

            case 'hidden' :

                extract($element);

                if((empty($value))&&($value!='0'))   $value='';

                $form_hidden.= '<input type="hidden" name="'.$name.'" id="'.$name.'" value="'.htmlentities($value, ENT_COMPAT, 'UTF-8')."\"/>\n";

                break;

            // Champ info
            //
            // 'name' => array(
            //      'item'      => 'info',              Element de type info
            //      'value'     => '[string]',          Valeur
            //  ),

            case 'info' :

                extract($element);

                if((empty($value))&&($value!='0'))   $value='';

                $form_tmp = '<p class="form-control-static '.$class.'" id="'.$name.'" '.$js.'>'.$value.'</p>';

                break;

            // Champ upload
            //
            // 'name' => array(
            //      'item'      => 'upload',                        Element de type hidden
            //      'tpl'       => '[string]',                      Template d'affichage
            //      'libelle'   => '[string]',                      Libellé du champ
            //      'legende'   => '[string]',                      Legende
            //      'label'     => '[string]',                      Label du champ
            //      'path_file' => '[string]',                      Chemin de stockage fichier
            //      'path_http' => '[string]',                      Chemin de stockage http
            // ),

            case 'upload' :

                extract($element);

                if(empty($value))        $value='';
                if(empty($path_file))    $path_file=CFG_PATH_FILE_UPLOAD;
                if(empty($path_http))    $path_http=CFG_PATH_HTTP_UPLOAD;

                $form_tmp.="\n";
                $form_tmp.= '<input type="hidden" name="'.$name.'" value="'.$value."\"/>\n";

                if(!empty($value)) {
                    $tmp_ext=explode('.', basename($path_file.'/'.$value));
                    $tmp_ext=$tmp_ext[sizeof($tmp_ext) - 1];
                    if(file_exists(CFG_PATH_FILE_IMAGE.'/upload/'.$tmp_ext.'.png'))
                        $tmp_img=CFG_PATH_HTTP_IMAGE.'/upload/'.$tmp_ext.'.png';
                    else
                        $tmp_img=CFG_PATH_HTTP_IMAGE.'/upload/default.png';

                    if(isset($require) && $require==TRUE) {
                        $form_tmp .= sprintf('<input type="file" name="%s"/>'."\n", $name.'_tmp');
                    }

                    $form_tmp .= sprintf("<a class=\"btn btn-link\" data-toggle=\"modal\" data-target=\"#%s\"><i class=\"glyphicon glyphicon-eye-open\"></i> ".STR_FORM_VIEW."</a> \n", crc32($value));

                    $form_tmp .= sprintf("<div class=\"modal fade\" id=\"%s\" tabindex=\"-1\" role=\"dialog\" aria-hidden=\"true\">\n", crc32($value));
                    $form_tmp .= "<div class=\"modal-dialog\">\n";
                    $form_tmp .= "<div class=\"modal-content\">\n";
                    $form_tmp .= "<div class=\"modal-header\">\n";
                    $form_tmp .= "<button type=\"button\" class=\"close\" data-dismiss=\"modal\"><span aria-hidden=\"true\">&times;</span></button>\n";
                    $form_tmp .= "<h4 class=\"modal-title\">".$value."</h4>\n";
                    $form_tmp .= "</div>\n";
                    $form_tmp .= "<div class=\"modal-body\">\n";
                    $info = get_file_info($path_file.'/'.$value);

                    if (!isset($info['width']) || $info['width'] === FALSE){
                        $form_tmp .= "<p><a href=\"".$path_http.'/'.$value."\" target=\"_blank\">".$value."</a></p>\n";
                    }
                    else {
                        $form_tmp .= "<p><img class=\"img-responsive img-thumbnail\" src=\"".$path_http.'/'.$value."\"></p>\n";
                    }
                    $form_tmp .= "</div>\n";
                    $form_tmp .= "<div class=\"modal-footer\">\n";
                    $form_tmp .= "<button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">".STR_OK."</button>\n";
                    $form_tmp .= "</div>\n";
                    $form_tmp .= "</div>\n";
                    $form_tmp .= "</div>\n";
                    $form_tmp .= "</div>\n\n";

                    if(!isset($require) || $require !== TRUE) {
                        $form_tmp .= sprintf("<button type=\"submit\" class=\"btn btn-default\" %s><i class=\"glyphicon glyphicon-trash\"></i> ".STR_FORM_DELETE."</button>\n", "onclick=\"var test=confirm('".substr(STR_FORM_DELETE_CONFIRMATION, 0, -4)." ?');
                        if(test) {
                            if(jQuery && $(this).closest('form').data('formValidation')) {
                                $(this).closest('form').formValidation('destroy');
                            }
                            document.".$form_name.".".$name.".value='delete|'+document.".$form_name.".".$name.".value;
                        } else {
                            return false;
                        }\"");
                    }
                }
                else {
                    $form_tmp .= sprintf(' <input type="file" name="%s"/>'."\n", $name.'_tmp');
                }

                break;

            // Champ button
            //
            // 'name' => array(
            //      'item'      => 'button',                        Element de type button
            //      'tpl'       => '[string]',                      Template d'affichage
            //      'libelle'   => '[string]',                      Libellé du champ
            //      'legende'   => '[string]',                      Legende
            //      'label'     => '[string]',                      Label du champ
            //      'value'     => '[string]',                      Valeur
            //      'type'      => '[string]',                      Type de bouton (submit, reset, etc.)
            //      'js'        => '[string]',                      Chaine JS avec événements et traitements
            // ),

            case 'button' :

                extract($element);

                if(empty($value))   $value='';
                if(empty($type))    $type='submit';
                if(empty($class))   $class='btn btn-default';

                $form_tmp = sprintf('<button class="%s" type="%s" id="%s" name="%s" value="%s" %s>%s</button>', $class, $type, $name, $name, $value, $js, $value);

                break;

            // Champ input
            //
            // 'name' => array(
            //      'item'      => 'input',                         Element de type input
            //      'tpl'       => '[string]',                      Template d'affichage
            //      'libelle'   => '[string]',                      Libellé du champ
            //      'legende'   => '[string]',                      Legende
            //      'label'     => '[string]',                      Label du champ
            //      'value'     => '[string]',                      Valeur
            //      'type'      => '[string]',                      Type (text ou password) / text par défaut
            //      'size'      => '[string]',                      Taille du champ
            //      'js'        => '[string]',                      Chaine JS avec événements et traitements
            // ),

            case 'input' :

                unset($size);
                unset($maxlength);
                unset($type);
                unset($prepend);
                unset($append);
                unset($placeholder);

                extract($element);

                if(empty($type))                    $type='text';
                if(empty($value) && $value!=='0')   $value='';
                if(empty($maxlength))               $maxlength='';
                if(empty($id))                      $id=$name;
                if(empty($prepend))                 $prepend='';
                if(empty($append))                  $append='';
                if(empty($placeholder))             $placeholder='';

                $form_tmp = '';

                if($prepend != '' || $append != '') {

                    $form_tmp .= "<div class=\"input-group\">\n";

                    if($prepend != '') {
                        $form_tmp .= sprintf("<span class=\"input-group-addon\"><i class=\"%s\"></i></span>", $prepend);
                    }
                }

                if($maxlength=='')
                    $form_tmp .= sprintf('<input type="%s" class="form-control" name="%s" id="%s" value="%s" placeholder="%s" %s/>', $type, $name, $id, htmlentities($value, ENT_COMPAT, 'UTF-8'), htmlentities($placeholder, ENT_COMPAT, 'UTF-8'), $js);
                else
                    $form_tmp .= sprintf('<input type="%s" class="form-control" name="%s" id="%s" value="%s" placeholder="%s"
                     maxlength="%s" %s/>', $type, $name, $id, htmlentities($value, ENT_COMPAT, 'UTF-8'), htmlentities($placeholder, ENT_COMPAT, 'UTF-8'), $maxlength, $js);

                if($prepend!='' || $append != '') {
                    if($append != '') {
                        $form_tmp .= sprintf("<span class=\"input-group-addon\"><i class=\"%s\"></i></span>", $append);
                    }

                    $form_tmp .= "\n</div>";
                }

                break;

            // Champ textarea
            //
            // 'name' => array(
            //      'item'      => 'textarea',                      Element de type textarea
            //      'tpl'       => '[string]',                      Template d'affichage
            //      'libelle'   => '[string]',                      Libellé du champ
            //      'legende'   => '[string]',                      Legende
            //      'label'     => '[string]',                      Label du champ
            //      'value'     => '[string]',                      Valeur
            //      'rows'      => '[string]',                      Taille rows
            //      'cols'      => '[string]',                      Taille cols
            //      'js'        => '[string]',                      Chaine JS avec événements et traitements
            // ),

            case 'textarea' :

                extract($element);

                if(empty($rows))        $rows=2;
                if(empty($cols))        $cols=40;
                if(empty($id))          $id=$name;

                $form_tmp = sprintf('<textarea class="form-control" id="%s" name="%s" rows="%s" cols="%s" %s>%s</textarea>'."\n", $id, $name, $rows, $cols, $js, $value);

                break;

            // Champ checkbox
            //
            // 'name' => array(
            //      'item'      => 'checkbox',                      Element de type checkbox
            //      'tpl'       => '[string]',                      Template d'affichage
            //      'libelle'   => '[string]',                      Libellé du champ
            //      'legende'   => '[string]',                      Legende
            //      'label'     => '[string]',                      Label du champ
            //      'checked'   => '[array]',                       Check
            //      'checkbox'  => '[array]',
            //          array(                                      Tableau de 0 à n avec tous les éléments checkbox
            //              'tpl'       => '[string]',              Template d'affichage
            //              'libelle'   => '[string]',              Libellé du champ
            //              'label'     => '[string]',              Label
            //              'value'     => '[string]',              Valeur
            //          ),
            //      'js'        => '[string]',                      Chaine JS avec événements et traitements
            // ),

            case 'checkbox' :
                unset($format);
                extract($element);

                $form_tmp .="<div id=\"".$name."\">";

                foreach($checkbox as $indice=>$item){


                    extract($item);

                    if(empty($tpl))         $tpl = '';
                    if(empty($legende))     $legende = '';
                    if(empty($libelle))     $libelle = '';
                    if(empty($label))       $label = '';
                    if(empty($js))          $js='';
                    if(empty($id))          $id=$name;
                    if(empty($format))      $format= 'inline';

                    if(is_array($checked)) {
                        if(in_array($value, $checked))
                            $check = ' checked';
                        else
                            $check = '';
                    }
                    elseif(is_string($checked)) {
                        if($value==$checked)
                            $check = ' checked';
                        else
                            $check = '';
                    }
                    else
                        $check = '';

                    $tmp = sprintf('<input type="checkbox" id="%s_%02d" name="%s[]" value="%s" %s %s />', $id, $indice, $name, $value, $check, $js);

                    $tpl = str_replace('{form}', $tmp, $tpl);
                    $tpl = str_replace('{label}', $label, $tpl);
                    $tpl = str_replace('{legende}', $legende, $tpl);

                    if($format == 'vertical') {
                        $form_tmp .= "<div class=\"checkbox\">\n";
                    }

                    $form_tmp .= "<label class=\"checkbox-inline\">".$tpl."</label>\n";

                    if($format == 'vertical') {
                        $form_tmp .= "</div>\n";
                    }
                }

                $form_tmp .="\n</div>";

                extract($element);

                break;

            // Champ radiobox
            //
            // 'name' => array(
            //      'item'      => 'radiobox',                      Element de type checkbox
            //      'tpl'       => '[string]',                      Template d'affichage
            //      'libelle'   => '[string]',                      Libellé du champ
            //      'legende'   => '[string]',                      Legende
            //      'label'     => '[string]',                      Label du champ
            //      'checked'   => '[array]',                       Check
            //      'radiobox'  => '[array]',
            //          array(                                      Tableau de 0 à n avec tous les éléments checkbox
            //              'tpl'       => '[string]',              Template d'affichage
            //              'libelle'   => '[string]',              Libellé du champ
            //              'label'     => '[string]',              Label
            //              'value'     => '[string]',              Valeur
            //          ),
            //      'js'        => '[string]',                      Chaine JS avec événements et traitements
            //      ),

            case 'radiobox' :

                unset($format);
                extract($element);

                $form_tmp .="<div id=\"".$name."\">";

                foreach($radiobox as $indice=>$item){
                    extract($item);

                    if(empty($tpl))         $tpl = '';
                    if(empty($legende))     $legende = '';
                    if(empty($libelle))     $libelle = '';
                    if(empty($label))       $label = '';
                    if(empty($js))          $js='';
                    if(empty($id))          $id=$name;
                    if(empty($format))      $format= 'inline';

                    if($value == $checked)
                        $check = ' checked';
                    else
                        $check = '';

                    $tmp = sprintf('<input type="radio" id="%s_%02d" name="%s" value="%s" %s %s />', $id, $indice, $name, $value, $check, $js);

                    $tpl = str_replace('{form}', $tmp, $tpl);
                    $tpl = str_replace('{label}', $label, $tpl);
                    $tpl = str_replace('{legende}', $legende, $tpl);

                    if($format == 'vertical') {
                        $form_tmp .= "<div class=\"radio\">\n";
                    }

                    $form_tmp .= "<label class=\"radio-inline\">".$tpl."</label>\n";

                    if($format == 'vertical') {
                        $form_tmp .= "</div>\n";
                    }
                }

                $form_tmp .="\n</div>";

                extract($element);

                break;

            // Champ select
            //
            // 'name' => array(
            //      'item'      => 'select',                        Element de type select
            //      'tpl'       => '[string]',                      Template d'affichage
            //      'libelle'   => '[string]',                      Libellé du champ
            //      'legende'   => '[string]',                      Legende
            //      'label'     => '[string]',                      Label du champ
            //      'value'     => 'array',                         Tableau des elements
            //      'selected'  => '[mixed]',                       Chaine (si select simple) ou tableau (si select multiple) des elements selectionnés
            //      'multiple'  => '[bool]',                        Multiple (0 non, 1 oui)
            //      'size'      => '[string]',                      Taille
            //      'js'        => '[string]',                      Chaine JS avec événements et traitements
            // ),

            case 'select' :

                extract($element);

                if(empty($size))    $size='10';
                if(empty($id))      $id=$name;

                if($multiple===TRUE)
                    $form_tmp = sprintf ('<select class="form-control" name="%s%s" id="%s" %s size="%s" %s>'."\n", $name, '[]', $id, ' multiple', $size, $js);
                else
                    $form_tmp = sprintf ('<select class="form-control" name="%s" id="%s" %s>'."\n", $name, $id, $js);

                foreach($value as $key=>$val) {
                    // Cas des optgroup
                    if(isset($optgroup) && $optgroup==TRUE && (isset($key[0]) && $key[0]=='<'))
                        $form_tmp.=sprintf(" <optgroup label=\"%s\">\n", $val);
                    elseif(isset($optgroup) && $optgroup==TRUE && (isset($key[mb_strlen($key,'UTF-8')-1]) && $key[mb_strlen($key,'UTF-8')-1]=='>'))
                        $form_tmp.=sprintf(" </optgroup>\n");
                    else {
                        // Pour les selects simples
                        if(!is_array($selected)) {
                            if(is_array($val))
                                $form_tmp.=sprintf(" <option value=\"%s\"%s>%s</option>\n", $key, strcmp($selected,$key)?"":" selected=\"selected\"", $val[0]);
                            else
                                $form_tmp.=sprintf(" <option value=\"%s\"%s>%s</option>\n", $key, strcmp($selected,$key)?"":" selected=\"selected\"", $val);
                        }
                        // Pour les selects multiples
                        elseif(is_array($selected)){
                            if(is_array($val)){
                                reset($selected);
                                $form_tmp.=sprintf(" <option value=\"%s\" ", $key);
                                foreach($selected as $sel) {
                                    $form_tmp.=sprintf("%s",strcmp($sel,$key)?"":" selected=\"selected\"");
                                }
                                $form_tmp.=sprintf(">%s</option>\n",$val[0]);
                            }
                            else{
                                reset($selected);
                                $form_tmp.=sprintf(" <option value=\"%s\" ", $key);
                                foreach($selected as $sel) {
                                    $form_tmp.=sprintf("%s",strcmp($sel,$key)?"":" selected=\"selected\"");
                                }
                                $form_tmp.=sprintf(">%s</option>\n",$val);
                            }
                        }
                    }
                }

                if(isset($optgroup) && $optgroup==TRUE)
                    $form_tmp.=sprintf(" </optgroup>\n");

                $form_tmp.="</select>";

                break;

            // Champ fieldset
            //
            // 'name' => array(
            //      'item'      => 'layer',                         Element de type layer
            //      'libelle'   => '[string]',                      Libellé du champ
            //      'type'      => '[string]',                      Type, on ou off, suivant que c'est une ouverture ou une fermeture de section
            // ),

            case 'fieldset' :

                extract($element);

                if($legende == '&nbsp;') {
                    $legende = '';
                }

                if(empty($libelle)) $libelle='';
                if(empty($type))    $type='';

                $foo=explode("<label class", form_template($require, $tpl, '', '', '', $form_tmp, $name, $accesskey));

                $tpl='';

                if($type=='on') {
                    $form_tmp.=$foo[0];

                    if (!empty($js))
                        $form_tmp.="<fieldset ".$js.">\n";
                    else
                        $form_tmp.="<fieldset>\n";

                    $form_tmp.="<legend>".$legende."</legend>\n";
                }
                else {
                    $form_tmp.="</fieldset>\n";
                    $form_tmp.="</div>\n";
                    $form_tmp.="</div>\n";
                }

                break;
        }

        if($form_tmp!='') {
            if(!empty($tpl)) {
                $form.=form_template($require, $tpl, $libelle, $label, $legende, $form_tmp, $name, $accesskey);
                if(!empty($legende) && $legende[0]=='_')
                    $tooltip++;
            }
            else {
                $form.=$form_tmp;
            }
        }
    }



    foreach($structure as $name => $element) {
        if($element['item'] == 'form' && isset($element['legende'])){
            $form .= '<div class="form-legend">'.$element['legende']."</div>\n";
            break;
         }
    }

    if($form_hidden!='')
        $form.=$form_hidden;
    if($visu)
        $form="<div class=\"form-horizontal form-zone\">\n".$form."\n</div>\n";
    else
        $form .="</form>\n";

    if($tooltip>0) { // Oui, il y a des tooltips a gerer !

        $form.= '
        <script type="text/javascript"><!--
        $(document).ready(function() {
            $("[data-toggle=\"popover\"]").popover();
        });
        // --></script>
        ';

    }

    return $form;
}

/*
 * Fonction form_check($structure)
 * -----
 * Verification du formulaire
 * -----
 * @param   array       $structure              structure du formulaire
 * -----
 * @return  array                               le tableau d'erreur
 *                                                  'fatal' => array()
 *                                                  'warning' => array()
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function form_check($structure) {

    // Si POST est vide, on ressort

    if(empty($_POST))
        return TRUE;

    // Simplification de la structure

    $structure=form_clean($structure);

    // Construction du tableau d'erreurs

    $form_error=array(
        'fatal' => array(),
        'warning' => array(),
    );

    foreach($structure as $name=>$element) {
        $flag_require=FALSE;
        if(!empty($element['require']) && ($element['require'])==TRUE) {
            switch($element['item']) {
                case 'upload':
                    if((!empty($_FILES[$name.'_tmp']) && $_FILES[$name.'_tmp']['tmp_name']=='' ) && (empty($_POST[$name]))) {
                        if($_FILES[$name.'_tmp']['error']==1) {
                            $form_error['fatal'][$name][] = sprintf(STR_FORM_E_FATAL_UPLOAD_MAX_FILESIZE, $element['libelle'], ini_get('upload_max_filesize'));
                            $flag_require=TRUE;
                        }
                        else {
                            $form_error['fatal'][$name][] = sprintf(STR_FORM_E_FATAL_FIELD_REQUIS, $element['libelle']);
                            $flag_require=TRUE;
                        }
                    }
                    break;

                case 'select':
                    if(empty($_POST[$name])) {
                        $form_error['fatal'][$name][] = sprintf(STR_FORM_E_FATAL_FIELD_REQUIS, $element['libelle']);
                        $flag_require=TRUE;
                    }
                    break;

                case 'checkbox':
                case 'radiobox':
                    if(!isset($_POST[$name])) {
                        $form_error['fatal'][$name][] = sprintf(STR_FORM_E_FATAL_FIELD_REQUIS, $element['libelle']);
                        $flag_require=TRUE;
                    }
                    break;

                default:
                    if(empty($_POST[$name]) && isset($_POST[$name]) && $_POST[$name]!=='0') {
                        $form_error['fatal'][$name][] = sprintf(STR_FORM_E_FATAL_FIELD_REQUIS, $element['libelle']);
                        $flag_require=TRUE;
                    }
                    break;
            }
        }

        // Cas des champs classiques
        if(($flag_require==FALSE) && isset($_POST[$name]) && is_exist($_POST[$name])) {
            //Cas du champ select
            if($element['item']=='select') {
                if(is_array($_POST[$name]) && $element['multiple']==true){
                    if(count(array_intersect($_POST[$name], array_keys($element['value']))) != count($_POST[$name])) {
                         $form_error['fatal'][$name][] = sprintf(STR_FORM_E_FATAL_FIELD_SAISIE, $element['libelle']);
                    }
                } elseif(!is_array($_POST[$name]) && $element['multiple'] == false) {
                    if(in_array($_POST[$name], array_keys($element['value'])) == false) {
                        $form_error['fatal'][$name][] = sprintf(STR_FORM_E_FATAL_FIELD_SAISIE, $element['libelle']);
                    }
                } else {
                    $form_error['fatal'][$name][] = sprintf(STR_FORM_E_FATAL_FIELD_SAISIE, $element['libelle']);
                }
            }

            //Cas du champ radiobox
            if($element['item']=='radiobox') {
                $valid =false;
                foreach($element['radiobox'] as $value)
                {
                    if($_POST[$name]==$value['value']) {
                        $valid =true;
                        break;
                    }
                }
                if($valid == false) {
                    $form_error['fatal'][$name][] = sprintf(STR_FORM_E_FATAL_FIELD_SAISIE, $element['libelle']);
                }
            }

            //Cas du champ checkbox
            if($element['item']=='checkbox') {
                //On vérifie que la donnée en post est bien un tableau
                if(is_array($_POST[$name])) {
                    $values = array();
                    foreach($element['checkbox'] as $value)
                    {
                        $values[] = $value['value'];
                    }
                    if(count(array_intersect($_POST[$name], $values)) != count($_POST[$name])) {
                        $form_error['fatal'][$name][] = sprintf(STR_FORM_E_FATAL_FIELD_SAISIE, $element['libelle']);
                    }
                } else {
                    $form_error['fatal'][$name][] = sprintf(STR_FORM_E_FATAL_FIELD_SAISIE, $element['libelle']);
                }
            }
            if(!empty($element['test']) && (!isset($element['test']['test_type_controle']) || in_array('php',$element['test']['test_type_controle']))) {
                if($element['item']=='select'){
                    if(call_user_func($element['test']['test_user_function'], $element['selected'])==FALSE) {
                        if(!isset($element['test']['test_error_message']))
                            $element['test']['test_error_message']=STR_FORM_E_FATAL_FIELD_SAISIE;

                        if(strstr($element['test']['test_error_message'],'%s'))
                            $form_error['fatal'][$name][] = sprintf($element['test']['test_error_message'], $element['libelle']);
                        else
                            $form_error['fatal'][$name][] = $element['test']['test_error_message'];
                    }
                }
                elseif($element['item']=='upload') {
                    // On ne fait rien / Problématique déportée plus loin
                }
                else {
                    if(call_user_func($element['test']['test_user_function'], $element['value'])==FALSE) {
                        if(!isset($element['test']['test_error_message']))
                            $element['test']['test_error_message']=STR_FORM_E_FATAL_FIELD_SAISIE;

                        if(strstr($element['test']['test_error_message'],'%s'))
                            $form_error['fatal'][$name][] = sprintf($element['test']['test_error_message'], $element['libelle']);
                        else
                            $form_error['fatal'][$name][] = $element['test']['test_error_message'];
                    }
                }
            }
        }

        // Cas du champ upload
        if(($flag_require==FALSE) && (!empty($_FILES[$name.'_tmp'])) && ($_FILES[$name.'_tmp']['name']!='')) {
            if(!empty($element['test']) && (!isset($element['test']['test_type_controle']) || in_array('php',$element['test']['test_type_controle']))) {
                $tmp=call_user_func($element['test']['test_user_function'], $name, $element);
                if(!empty($tmp)) {
                    //$foo="\n<ul>- ".implode("\n<br>- ", $tmp).'</ul>';
                    $foo="\n<ul><li>".implode("\n</li><li>", $tmp).'</li></ul>';

                    if(!isset($element['test']['test_error_message']))
                        $element['test']['test_error_message']=STR_FORM_E_FATAL_FIELD_SAISIE;

                    if(strstr($element['test']['test_error_message'],'%s'))
                        $form_error['fatal'][$name][] = sprintf($element['test']['test_error_message'], $element['libelle']).$foo;
                    else
                        $form_error['fatal'][$name][] = $element['test']['test_error_message'].$foo;
                }
            }
        }
    }

    // print_rh($form_error);

    return $form_error;
}
/*
 * Fonction form_message($libelle, $message, $css='error', $type='normal')
 * -----
 * Affichage de messages
 * -----
 * @param   string       $libelle               le titre
 * @param   midex        $message               le message ou le tableau de messages
 * @param   string       $css                   la css d'affichage: error (par défaut), warning, info ou success
 * @param   string       $type                  le type de message: normal (par défaut), modal ou session
 * @param   boolean      $selecteur             colorisation des labels: TRUE (par défaut) ou FALSE
 * -----
 * @return  string                              le flux HTML
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function form_message($libelle, $message, $css='danger', $type='normal', $selecteur=TRUE) {
    return message($libelle, $message, $css, $type, $selecteur);
}

function form_clean($structure) {

    global $cfg_profil;

    //--------------------------------------------------------------
    // Simplification de la structure
    //--------------------------------------------------------------


    $tmp=check_post($cfg_profil['acl'], FALSE);

    if(!empty($tmp)) {
        $tmp=explode('|', $tmp);
        foreach($structure as $k => $v){
            if(!in_array($k, $tmp))
                unset($structure[$k]);
        }
    }

    return $structure;
}
