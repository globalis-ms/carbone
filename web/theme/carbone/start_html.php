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
        <title><?php echo CFG_TITRE.' V'.CFG_VERSION.'::'.RUBRIQUE_TITRE ?></title>
        <link rel="icon" type="image/png" href="<?php echo CFG_PATH_HTTP.'/favicon.png'; ?>">
        
        <link rel="stylesheet" type="text/css" href="<?php echo CFG_PATH_HTTP_WEB."/theme/".$cfg_profil["theme"]."/dist/styles/main.css"; ?>">
        <?php
        // Chargement des feuilles de styles spécifiques
        echo load_styles();
        ?>

        <script type="text/javascript" src="<?php echo CFG_PATH_HTTP_WEB."/theme/".$cfg_profil['theme']."/dist/scripts/main.js"; ?>"></script>
        <?php
        // Chargement des scripts spécifiques dans le <head> si l'optimisation d'emplacement des scripts n'est pas activée
        if ((CFG_OPTIMISATION_LEVEL&2) != 2) {
            echo load_scripts();
        }
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

                <!-- Collect the nav links, forms, and other content for toggling -->
                    
                <?php
                    echo get_menu_global(get_menu_acl($navigation, $cfg_profil['acl'])); 
                ?>
            </div><!-- /.container-fluid -->
        </nav>
                    
        <div class="container">
            <h2><?php echo RUBRIQUE_TITRE; ?></h2>