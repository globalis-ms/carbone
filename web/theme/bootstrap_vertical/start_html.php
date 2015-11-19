<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="pragma" content="no-cache">
        <meta http-equiv="expires" content="0">
        <meta http-equiv="Cache-Control" content="no-cache">
        <meta http-equiv="Cache-Control" content="no-store">
        <meta http-equiv="Cache-Control" content="must-revalidate">
        <meta name="description" content="Framework Carbone">
        
        <title><?php echo CFG_TITRE.' V'.CFG_VERSION.'::'.RUBRIQUE_TITRE?></title>
        
        <?php
            echo "<link rel=\"icon\" type=\"image/png\" href=\"".CFG_PATH_HTTP."/favicon.png\" />";

            $css=array();
            $js=array();
           
            // Tableau des feuilles de style CSS
 
            $css[]="\t<link rel=\"stylesheet\" href=\"".CFG_PATH_HTTP_WEB."/theme/".$cfg_profil['theme']."/css/bootstrap.css\" type=\"text/css\" />\n";
            $css[]="\t<link rel=\"stylesheet\" href=\"".CFG_PATH_HTTP_WEB."/theme/".$cfg_profil['theme']."/css/bootstrap-theme.css\" type=\"text/css\" />\n";
            $css[]="\t<link rel=\"stylesheet\" href=\"".CFG_PATH_HTTP_WEB."/theme/".$cfg_profil['theme']."/css/carbone.css\" type=\"text/css\" />\n";
            $css[]="\t<link rel=\"stylesheet\" href=\"".CFG_PATH_HTTP_WEB."/theme/".$cfg_profil['theme']."/css/style.css\" type=\"text/css\" />\n";

            // Tableau des scripts JS
    
            $js[]="\t<script type=\"text/javascript\" src=\"".CFG_PATH_HTTP_WEB."/js/jquery-1.11.0.min.js\"></script>\n";
            $js[]="\t<script type=\"text/javascript\" src=\"".CFG_PATH_HTTP_WEB."/js/jquery-migrate-1.2.1.min.js\"></script>\n";
            $js[]="\t<script type=\"text/javascript\" src=\"".CFG_PATH_HTTP_WEB."/js/bootstrap-3.3.4.min.js\"></script>\n";
            
            // Chainage de l'ensemble et optimisation si nécessaire

            echo load_head($css, $js);
        ?>
    </head>
    
    <body>

        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container">
                    <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-responsive">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#"><?php echo CFG_TITRE.' V'.CFG_VERSION; ?></a>
                </div>
            </div><!-- /.container-fluid -->
        </nav>
                    
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3 sidebar">
                    <?php
                        echo get_menu_global(get_menu_acl($navigation, $cfg_profil['acl'])); 
                    ?>
                </div>

                <div class="col-md-9">
                    <h2><?php echo RUBRIQUE_TITRE; ?></h2>
                
                
            