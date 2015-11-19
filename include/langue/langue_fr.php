<?php
//
// Chaines rubriques
//

define('STR_RUBRIQUE_UTILISATEUR',		'Utilisateurs');
define('STR_RUBRIQUE_EXEMPLE',       	'Exemples');
define('STR_RUBRIQUE_DIVERS',        	'Exemples Divers');
define('STR_RUBRIQUE_FORMULAIRE',    	'Formulaire');
define('STR_RUBRIQUE_SPECIAL',       	'Spécial');
define('STR_RUBRIQUE_EDITEUR',       	'Editeur');
define('STR_RUBRIQUE_FILE',          	'File');
define('STR_RUBRIQUE_DECONNEXION',   	'Déconnexion');
define('STR_RUBRIQUE_ACCUEIL',       	'Accueil');
define('STR_RUBRIQUE_PROFIL',        	'Mon profil');
define('STR_RUBRIQUE_CONNEXION',     	'Connexion');
define('STR_RUBRIQUE_ONGLET',        	'Onglet');
define('STR_RUBRIQUE_ONGLET_RETOUR', 	'Retour');
define('STR_RUBRIQUE_ONGLET_COMPTE', 	'Informations générales');
define('STR_RUBRIQUE_ONGLET_COULEUR',	'Couleurs');
define('STR_RUBRIQUE_ONGLET_THEME',  	'Thème');
define('STR_RUBRIQUE_PARAMETRE',        'Paramètres');

//
// Chaines généralistes
//

define('STR_OUI',       'Oui');
define('STR_NON',       'Non');
define('STR_MAYBE',     'Peut-être');
define('STR_OK',        'Ok');
define('STR_OR',        'Ou');
define('STR_BACK',      'Retour');
define('STR_SEARCH',    'Rechercher');
define('STR_PRINT',     'Imprimer');
define('STR_DOC',       'Documentation');
define('STR_RESET',     'Reset');
define('STR_DELETE',    'Effacer');
define('STR_RETOUR',    'Retour');
define('STR_CONNECT',   'utilisateur%s connecté%s');
define('STR_EN_LIGNE',  'En ligne');
define('STR_ALL',       'Tout');
define('STR_LOREM', 	'Hydram Lernaeam Typhonis filiam cum capitibus nouem ad fontem Lernaeum interfecit. haec tantam uim ueneni habuit ut afflatu homines necaret, et si quis eam dormientem transierat, uestigia eius afflabat et maiori cruciatu moriebatur.');


define('STR_BACKOFFICE_ORDER_ASC',      'ASC');
define('STR_BACKOFFICE_ORDER_DESC',     'DESC');
define('STR_BACKOFFICE_HELP_TITRE',     'Aide');
define('STR_BACKOFFICE_HELP_OUTIL',     'Cette zone vous permet d\'affiner le flux de données retournées:');
define('STR_BACKOFFICE_HELP_FILTRE',    '<ul><li>Afin de filtrer les données suivant des critères pré-définis (<strong>%s</strong>), saisissez une occurence dans la zone de saisie et cliquez sur le bouton <strong>&laquo;OK&raquo;</strong> ou sélectionnez une des valeurs proposées.</li><li>Pour annuler l\'effet d\'un filtre, cliquer sur le bouton <strong>&laquo;Effacer&raquo;</strong> qui lui est éventuellement associé.</li><li>Pour voir à nouveau l\'ensemble des données retournées et annuler l\'effet de tous les filtres, cliquer sur le bouton <strong>&laquo;Reset&raquo;</strong></li></ul>');
define('STR_BACKOFFICE_HELP_DATA',      'Cette zone présente l\'ensemble des données retournées:');
define('STR_BACKOFFICE_HELP_ORDER',     '<ul><li>Cliquer sur l\'entête d\'une colonne (<strong>%s</strong>) pour ordonner les données par ordre croissant ou décroissant</li>');
define('STR_BACKOFFICE_HELP_ACTION',    '<li>Cliquer sur <strong>&laquo;%s&raquo;</strong> pour %s un élément</li>');
define('STR_BACKOFFICE_HELP_NAVIGATION','</ul>Par défaut, le flux de données retournées va s\'afficher à raison de <strong>%s</strong> éléments par page. Vous pouvez modifier ce nombre d\'élément en cliquant sur une des valeurs proposées sous le tableau, à gauche (<strong>Affichage par %s</strong>). Si le flux de données est trop important pour être affiché sur une seule page, vous pouvez avoir à naviguer page par page en utilisant les liens présents sous le tableau, au centre. Ils vous permettront d\'accéder aux différentes pages de résultat.<br/>');
define('STR_BACKOFFICE_HELP_EXPORT',    'Des liens présents sous le tableau, à droite, vous permettent d\'obtenir un export dans divers formats (<strong>%s</strong>).');
define('STR_BACKOFFICE_FILTRE',         'Cliquez sur le bouton <strong>&laquo;Reset&raquo;</strong>, à droite, afin de ré-initialiser vos options de recherche.');
define('STR_BACKOFFICE_EXPORT_FORMAT_PDF',      'pdf');
define('STR_BACKOFFICE_EXPORT_FORMAT_EXCEL',    'excel');
define('STR_BACKOFFICE_EXPORT_FORMAT_CSV',    	'csv');

define('STR_FORM_AFFICHAGE_TOTAL', 'Nombre total d\'éléments :');
define('STR_FORM_SUBMIT', 'Valider');
define('STR_FORM_LOGIN_HELP', 'Aide');
define('STR_FORM_ADD', 'Ajouter');
define('STR_FORM_VIEW', 'Visualiser');
define('STR_FORM_EDIT', 'Modifier');
define('STR_FORM_ACTIVE', 'Activer');
define('STR_FORM_DESACTIVE', 'Désactiver');
define('STR_FORM_DELETE', 'Supprimer');
define('STR_FORM_DELETE_ALL', 'Supprimer la sélection');
define('STR_FORM_ACTIVE_ALL', 'Activer la sélection');
define('STR_FORM_DESACTIVE_ALL', 'Désactiver la sélection');
define('STR_FORM_DELETE_CONFIRMATION', 'Confirmer la suppression de ');
define('STR_FORM_AFFICHAGE_PAR', 'Affichage par');
define('STR_FORM_CARACTERE_LEFT', ' caractère(s) restant(s) (max %s)');
define('STR_FORM_REQUIRE_SYMBOL', '(&#8226;)');
define('STR_FORM_LEGENDE', 'Les champs '.STR_FORM_REQUIRE_SYMBOL.' sont obligatoires');

//
// Chaines de gestion des erreurs FATAL : FORM
//

define('STR_FORM_E_FATAL', 'Des erreurs de saisie sont présentes sur le formulaire');
define('STR_FORM_E_FATAL_FIELD_REQUIS', 'Le champ &laquo;%s&raquo; est requis');
define('STR_FORM_E_FATAL_FIELD_SAISIE', 'Le champ &laquo;%s&raquo; n\'est pas saisi correctement');

//
// Chaines de gestion des erreurs FATAL : UPLOAD
//

define('STR_FORM_E_FATAL_UPLOAD_MAX_FILESIZE', 'Le fichier &laquo;%s&raquo; excède la taille maximale autorisée (UPLOAD_MAX_FILESIZE=%s)');
define('STR_FORM_E_FATAL_UPLOAD_FORM_SIZE', 'Le fichier téléchargé excède la taille maximale autorisée (HTML)');
define('STR_FORM_E_FATAL_UPLOAD_ZERO_SIZE', 'Le fichier téléchargé a une taille nulle');
define('STR_FORM_E_FATAL_UPLOAD_PARTIAL', 'Le fichier n\'a été que partiellement téléchargé');
define('STR_FORM_E_FATAL_UPLOAD_NO_FILE', 'Aucun fichier n\'a été téléchargé');
define('STR_FORM_E_FATAL_UPLOAD_NO_TMP_DIR', 'Le dossier temporaire est manquant');
define('STR_FORM_E_FATAL_UPLOAD_CANT_WRITE', 'Echec de l\'écriture du fichier sur disque');
define('STR_FORM_E_FATAL_UPLOAD_BAD_TYPE', 'Type MIME &laquo;%s&raquo; non valide (type%s accepté%s : %s)');
define('STR_FORM_E_FATAL_UPLOAD_BAD_EXT', 'Extension &laquo;%s&raquo; non valide (extension%s acceptée%s : %s)');

//
// Chaines de gestion des erreurs WARNING
//

define('STR_FORM_E_WARNING', 'A titre indicatif');

//
// Page login (login.php)
//

define('STR_LOGIN_TITRE', 'Bienvenue');
define('STR_LOGIN_FIELDSET_LOGIN', 'Identifiez-vous');
define('STR_LOGIN_FIELDSET_PERDU', 'Mot de passe perdu');
define('STR_LOGIN_LIBELLE_EMAIL', 'Email');
define('STR_LOGIN_LIBELLE_EMAIL_PLACEHOLDER', 'Saisissez votre Email');
define('STR_LOGIN_LIBELLE_PASSWORD', 'Mot de passe');
define('STR_LOGIN_LIBELLE_PASSWORD_PLACEHOLDER', 'Saisissez votre Mot de passe');
define('STR_LOGIN_MESSAGE', 'Hydram Lernaeam Typhonis filiam cum capitibus nouem ad fontem Lernaeum interfecit. haec tantam uim ueneni habuit ut afflatu homines necaret, et si quis eam dormientem transierat, uestigia eius afflabat et maiori cruciatu moriebatur.');
define('STR_LOGIN_E_FATAL', 'Ce compte est invalide (vérifiez &laquo;Email&raquo; et &laquo;Password&raquo;)');
define('STR_LOGIN_E_WARNING', 'Un email vient de vous être envoyé.');
define('STR_LOGIN_EMAIL_SUJECT','Mot de passe perdu');
define('STR_LOGIN_EMAIL_BODY','Vous recevez ce message car vous avez perdu votre mot de passe d\'accès à l\'application %s. Merci de cliquer sur le lien %s afin d\'initialiser un nouveau mot de passe.');
define('STR_LOGIN_EMAIL_BODY_HTML','<font face=Arial size=2>Vous recevez ce message car vous avez perdu votre mot de passe d\'accès à l\'application <strong>%s</strong>. Merci de cliquer sur le lien %s afin d\'initialiser un nouveau mot de passe.</font>');

//
// Page accueil (index.php)
//

define('STR_ACCUEIL_TITRE', 'Bienvenue sur Carbone');

//
// Page erreur (erreur.php)
//

define('STR_ERREUR_TITRE', 'Erreur');
define('STR_ERREUR_MESSAGE', 'Vous ne pouvez pas accéder à cet espace du site !');
define('STR_ERREUR_MESSAGE_BACK', '<br/>Cliquez <a href=\'%s\'>ici</a> pour revenir à la page précédente');

//
// Page parametre (parametre/index.php)
//

define('STR_PARAMETRE_TITRE', 'Paramètres');

//
// Rubrique utilisateur (utilisateur/index.php)
//

define('STR_UTILISATEUR_TITRE', 'Gestion des utilisateurs');

define('STR_UTILISATEUR_LIBELLE_STATUS', 'Status');
define('STR_UTILISATEUR_LIBELLE_ACTIF', 'Actif');
define('STR_UTILISATEUR_LIBELLE_INACTIF', 'Inactif');
define('STR_UTILISATEUR_LIBELLE_TOUS', 'Tous');
define('STR_UTILISATEUR_LIBELLE_NOM', 'Nom');
define('STR_UTILISATEUR_LIBELLE_PRENOM', 'Prénom');
define('STR_UTILISATEUR_LIBELLE_EMAIL', 'Email');
define('STR_UTILISATEUR_LIBELLE_PASSWORD', 'Mot de passe');
define('STR_UTILISATEUR_LIBELLE_PASSWORD_CONFIRMATION', 'Confirmation');
define('STR_UTILISATEUR_LIBELLE_POSTE', 'Poste');
define('STR_UTILISATEUR_LIBELLE_ACL', 'Profil');
define('STR_UTILISATEUR_LIBELLE_ACL_ADMIN',	'Administrateur');
define('STR_UTILISATEUR_LIBELLE_ACL_USER',	'Utilisateur');
define('STR_UTILISATEUR_LIBELLE_ACL_GUEST',	'invité');
define('STR_UTILISATEUR_LIBELLE_COULEUR', 'Couleurs');
define('STR_UTILISATEUR_LIBELLE_LANGUE', 'Langue');
define('STR_UTILISATEUR_LIBELLE_THEME', 'Thème');
define('STR_UTILISATEUR_LIBELLE_DATE', 'Date');
define('STR_UTILISATEUR_LIBELLE_DATE_DEBUT', 'Arrivée');
define('STR_UTILISATEUR_LIBELLE_DATE_FIN', 'Départ');
define('STR_UTILISATEUR_LIBELLE_NOMBRE', 'Nombre total d\'utilisateur(s):');
define('STR_UTILISATEUR_FIELDSET_THEME', 'Changement du thème et de la langue');
define('STR_UTILISATEUR_LEGENDE_PASSWORD', 'Saisissez le mot de passe.');
define('STR_UTILISATEUR_LEGENDE_PASSWORD_MODIF', 'Si vous souhaitez changer le mot de passe de l\'utilisateur, tapez en un nouveau deux fois de suite. Sinon, laissez les champs vides.');
define('STR_UTILISATEUR_LEGENDE_PASSWORD_CONFIRMATION', 'Veuillez saisir une deuxième fois le mot de passe.');
define('STR_UTILISATEUR_E_FATAL_1', 'Le champ &laquo;'.STR_UTILISATEUR_LIBELLE_PASSWORD.'&raquo; comporte des caractères invalides');
define('STR_UTILISATEUR_E_FATAL_2', 'Le champ &laquo;'.STR_UTILISATEUR_LIBELLE_PASSWORD.'&raquo; doit faire au moins 4 caractères');
define('STR_UTILISATEUR_E_FATAL_3', 'Cet &laquo;'.STR_UTILISATEUR_LIBELLE_EMAIL.'&raquo; existe déjà en base');
define('STR_UTILISATEUR_E_FATAL_4', 'Les mots de passes ne correspondent pas');
define('STR_UTILISATEUR_E_WARNING_1', 'Le champ &laquo;'.STR_UTILISATEUR_LIBELLE_PASSWORD.'&raquo; correspond au champ &laquo;'.STR_UTILISATEUR_LIBELLE_PRENOM.'&raquo; (weak)');
define('STR_UTILISATEUR_E_INFO_1', 'L\'ajout a été effectué !');
define('STR_UTILISATEUR_E_INFO_2', 'La modification a été effectuée !');
define('STR_UTILISATEUR_E_INFO_3', 'La suppression a été effectuée !');
define('STR_UTILISATEUR_MESSAGE', '<strong>Remarque</strong><br>Pour insérer un élément sur 3 colonnes comme par exemple du texte ou du HTML (balise &lt;hr&gt;), il suffit d\'utiliser le type info.');
define('STR_UTILISATEUR_EXPLICATION_1', 'Dans cet exemple, on revient sur la brique backoffice si aucune erreur.');
define('STR_UTILISATEUR_EXPLICATION_2', 'Dans cet exemple, on reste sur le formulaire en mode édition (action=edit).');

//
// Page profil (profil.php)
//

define('STR_PROFIL_TITRE', 'Gestion de mon profil');
define('STR_PROFIL_MESSAGE', 'Le profil a été mis à jour avec succés !');

//
// Page reset (reset.php)
//

define('STR_RESET_TITRE', 'Mot de passe perdu');
define('STR_RESET_E_FATAL_1', 'Cet &laquo;'.STR_UTILISATEUR_LIBELLE_EMAIL.'&raquo; est invalide');

//
// Rubrique exemple (exemple/index.php)
//

define('STR_EXEMPLE_TITRE', 'Exemples');

//
// Rubrique exemple / page formulaire (exemple/formulaire.php)
//

define('STR_FORMULAIRE_TITRE', 'Différents éléments de formulaire');
define('STR_FORMULAIRE_LIBELLE_RADIOBOX', 'Radiobox');
define('STR_FORMULAIRE_LIBELLE_CHECKBOX', 'Checkbox');
define('STR_FORMULAIRE_LIBELLE_INPUT', 'Input');
define('STR_FORMULAIRE_LIBELLE_INPUT_PASSWORD', 'Password');
define('STR_FORMULAIRE_LIBELLE_SELECT', 'Select');
define('STR_FORMULAIRE_LIBELLE_SELECT_MULTIPLE', 'Select Multiple');
define('STR_FORMULAIRE_LIBELLE_TEXTAREA', 'Textarea');
define('STR_FORMULAIRE_LIBELLE_TEXTAREA_COMPTEUR', '(Compteur)');
define('STR_FORMULAIRE_LIBELLE_CALENDAR', 'Calendrier');
define('STR_FORMULAIRE_LIBELLE_UPLOAD', 'Upload');
define('STR_FORMULAIRE_LIBELLE_MULTISELECT', 'Multiselect');
define('STR_FORMULAIRE_FIELDSET_OPTGROUP', 'Variantes OptGroup');

//
// Rubrique exemple / page file (exemple/file.php)
//

define('STR_FILE_TITRE', 'Gestion du upload');
define('STR_FILE_LIBELLE_FICHIER', 'Fichier');
define('STR_FILE_LIBELLE_ONLINE', 'En ligne');
define('STR_FILE_LIBELLE_TITRE', 'Titre');
define('STR_FILE_LIBELLE_URL', 'Url');

//
// Rubrique exemple / page editeur (exemple/editeur.php)
//

define('STR_EDITEUR_TITRE', 'Editeur WYSIWYG');
define('STR_EDITEUR_MESSAGE', 'Hydram Lernaeam Typhonis filiam cum capitibus nouem ad fontem Lernaeum interfecit. haec tantam uim ueneni habuit ut afflatu homines necaret, et si quis eam dormientem transierat, uestigia eius afflabat et maiori cruciatu moriebatur.');

//
// Rubrique exemple / page special (exemple/special.php)
//

define('STR_SPECIAL_TITRE', 'Spécial');
define('STR_SPECIAL_IP_LIBELLE', 'Adresse IP');
define('STR_SPECIAL_IP_LEGENDE', 'Exemple : 192.168.1.2');
define('STR_SPECIAL_IP_ERREUR', 'Le champ &laquo;Adresse IP&raquo; est invalide');

//
// Rubrique onglet (onglet/index.php)
//

define('STR_ONGLET_TITRE', 'Onglet');
define('STR_ONGLET_SOUS_TITRE', 'Gestion des utilisateurs');
define('STR_ONGLET_TITRE_ETAPE_1', 'Configuration du compte');
define('STR_ONGLET_TITRE_ETAPE_2', 'Configuration des couleurs');
define('STR_ONGLET_TITRE_ETAPE_3', 'Configuration du thème');
?>