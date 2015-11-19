<?php
if (!(isset($_GET['ajax']) && $_GET['ajax']=='on'))
    require dirname(__FILE__).'/../web/theme/'.$cfg_profil['theme'].'/start_html.php';
?>