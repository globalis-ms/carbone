jquery.notice.js

Ce fichier contient trois méthodes (notice, noticeRemove et confirm).
La première propose une implémentation simple d'affichage de boîtes modales.
La deuxième de retirer toute modale (notice ou confirm) qui est présente dans la page.
La dernière propose une alternative au fênetre de confirmation et pour la dernière.

///////////////////
// Notice        //
///////////////////

Description
===========

$.notice(element, options)
ou
$(element).notice(options)

Permet d'afficher un élément html dans une modale HTML. Cette fonction ne permet pas d'afficher plusieurs modales en même temps.

Paramètres
==========
element : Elément jQuery (ou son sélecteur CSS) OU code html à afficher dans la modale.
options : Tableau JSON de paramètres pour personnaliser l'affichage de la modale.

Les options possibles sont les suivantes :

clé         type        explication
---------------------------------------------------------------------------------------
close       bool        Paramètre l'affichage ou non d'une DIV permettant de fermer la modale. TRUE par défaut.
                        Cette DIV aura pour classe CSS `close` (Cette class n'est pas paramètrable).

duration    int         Détermine la durée d'affichage de la modale en millisecondes.
                        Cette valeur vaut `0` par défaut ce qui signifie que l'affichage est permanent.

height      int/bool    Permet de forcer la hauteur (en pixel) de la modale.
                        Cette valeur est à FALSE par défaut. La hauteur dépendra alors de la hauteur de l'élément à afficher
                        ou d'une propriété CSS `height` appliquée à la classe de la modale.

width       int/bool    Permet de forcer la largeur (en pixel) de la modale.
                        Cette valeur est à FALSE par défaut. La largeur dépendra alors de la largeur de l'élément à afficher
                        ou d'une propriété CSS `width` appliquée à la classe de la modale.

center      bool        Permet de forcer l'affichage de la modale au centre de la fenêtre. TRUE par défaut.
                        Si la valeur vaut FALSE, le positionnement de la modale devra être géré avec les CSS.

overlay     str/bool    Détermine la classe CSS de la zone d'overlay qui sera affichée derrière la modale
                        Si cette valeur vaut FALSE, la zone d'overlay ne sera pas créée.
                        Par défaut cette valeur vaut FALSE.

className   str/bool    Détermine la classe CSS de la modale.
                        Par défaut cette valeur vaut `notice-item`.

Exemple d'utilisation
=====================


<script type="text/javascript">

$('<div>Message à caractère informatif</div>').notice({
    close:      FALSE,
    duration:   3000,
    height:     200,
    width:      400,
    center:     FALSE,
    overlay:    'notice-overlay',
    className:  'notice-left'
});

</script>

///////////////////
// noticeRemove  //
///////////////////

Descriptions
============

$.noticeRemove()

Permet de retirer une fenêtre modale présente dans la page.

Exemple d'utilisation
=====================

<script type="text/javascript">

$('.close').click(function() {
    $.noticeRemove();
});

ou plus simplement

$('.close').click($.noticeRemove);

</script>

///////////////////
// Confirm       //
///////////////////

Description
===========

$.confirm(message, callback, options)

Affiche une fenêtre modale de type confirmation. Cette modale contiendra un message et deux boutons d'actions (Ok, Annuler)
Lorsque l'utilisateur clique sur un des boutons, une fonction de callback est exécutée en lui passant comme paramètre, la réponse de l'utilisateur (TRUE pour Ok et FALSE pour Annuler).

Paramètres
==========
message     : Message qui sera affichée dans la modale de confirmation
callback    : Fonction qui sera exécutée après le click sur un bouton d'action avec en paramètre le résultat de la confirmation.
options     : Tableau JSON de paramètres pour personnaliser le comportement et l'affichage de la modale.

Toutes les options de la méthode notice pour disponible pour la méthode confirm. Voici tout de même quelques différences:

clé         type        explication
---------------------------------------------------------------------------------------
close       bool        Contrairement à notice, ce paramètre vaut FALSE par défaut.

strOk       string      Permet de modifier le libellé du bouton "Ok"

strCancel   string      Permet de modifier le libellé du bouton "Annuler"
                        
Exemple d'utilisation
=====================

<script type="text/javascript">
$(function() {

    $('.bt_lambda').confirm('Confirmer l\'action ?', function(response) {
        if(response == true) {
            // do something if user click on 'Ok' button
        } else {
            // do something else if user click on 'Annuler' button
        }
    });

});
</script>