        </div><!-- /.container-fluid --> 

                    
        <footer class="footer">

            <div class="container">
                <p class="text-muted">
                    <?php
                        $flux='';
                        $flux.=CFG_TITRE.' V'.CFG_VERSION.' ('.ucwords(str_replace('_', ' ', CFG_CLASS)).'::'.ucwords(CFG_TYPE).')'.' - '.CFG_DATE;
                        if(!empty($session_user_id))
                            $flux.= ' - '.$cfg_profil['nom'].' '.$cfg_profil['prenom']."\n";
                        
                        if ($indicateur_db === TRUE){
                            $foo=@$session->count();
        
                            $flux.= ' - '.$foo.' ';
                            if($foo > 1)
                                $flux.=sprintf(STR_CONNECT, 's', 's');
                            else
                                $flux.=sprintf(STR_CONNECT, ' ', ' ');
                        }
                        
                        echo $flux;
                    ?>    
                
                    <span class="pull-right">
                        <?php
                            $flux='';
                            if(defined("CFG_DOC") && CFG_DOC==TRUE) {                    
                                $url=CFG_PATH_HTTP.'/divers/doc/';
                                $flux.='&nbsp;<a href="'.$url.'" target="_blank">'.STR_DOC.'</a>';
                            }
                            
                            echo $flux;    
                        ?>        
                    </span>
                </p>
            </div>
        </footer>           
    </body>
    
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
</html>