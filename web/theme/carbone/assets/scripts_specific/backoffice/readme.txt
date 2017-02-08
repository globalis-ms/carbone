jquery.backoffice.js


///////////////////////////////
// backoffice_confirm        //
///////////////////////////////


Description
===========

$(element).backoffice_group ( message, options )

Permet d'appliquer au click sur `element` l'exécution d'une action de groupe APRES un message de confirmation passé en paramètre.
Le message de confirmation sera affiché sous forme de notice si le plugin jQuery Notice de Carbone est inclus dans la page.
Dans le cas contraire, une confirmation standard sera utilisée.

Paramètres
==========
element  : Elément jQuery représentant un lien d'action de groupe d'une brique backoffice, ou sélecteur CSS correspondant
message  : Message à afficher dans la boîte de confirmation.
options  : Options d'affichage si la boîte de confirmation est affichée sous forme de Notice. (voir readme de jquery.notice.js)

Exemple d'utilisation
=====================

<script type="text/javascript">
$('.group_del_group').backoffice_group("Confirmer la suppression groupée?");
</script>


///////////////////////////////
// backoffice_group          //
///////////////////////////////


Description
===========

$(element).backoffice_confirm ( message, options )

Permet d'appliquer au click sur `element` l'exécution d'une action locale APRES un message de confirmation passé en paramètre.
Le message de confirmation sera affiché sous forme de notice si le plugin jQuery Notice de Carbone est inclus dans la page.
Dans le cas contraire, une confirmation standard sera utilisée.
Quelquesoit le type de confirmation, la méthode `backoffice_message` sera appliqué au message AVANT l'affichage

Paramètres
==========
element  : Elément jQuery représentant un lien d'action locale d'une brique backoffice, ou sélecteur CSS correspondant
message  : Message à afficher dans la boîte de confirmation.
options  : Options d'affichage si la boîte de confirmation est affichée sous forme de Notice. (voir readme de jquery.notice.js)

Exemple d'utilisation
=====================

<script type="text/javascript">
$('.local_del').backoffice_confirm("Confirmer la suppression de %2 %3 ?");
</script>


///////////////////////////////
// backoffice_action_group   //
///////////////////////////////


Description
===========

backoffice_action_group ( element, action )

Permet d'effectuer une action de groupe d'une brique backoffice.
Cette fonction est principalement destinée à être utilisé dans la méthode backoffice_group.

Paramètres
==========
element : Elément jQuery représentant un lien d'action de groupe d'une brique backoffice, ou sélecteur CSS correspondant
action  : URL à transmettre au formulaire d'action de groupe.

Exemple d'utilisation
=====================

<script type="text/javascript">
backoffice_action_group(".group_active_group", "http://192.168.1.28/carbone/utilisateur/index.php?action=active_group");
</script>


///////////////////////////////
// backoffice_message        //
///////////////////////////////


Description
===========

backoffice_message ( element, message )

Retourne une chaîne formatée à partir d'un message en utilisant des informations d'une brique backoffice correspondant
à la ligne de l'élément html passé en paramètre.

Paramètres
==========
element : Elément jQuery d'une brique backoffice, ou sélecteur CSS correspondant
message : Message à enrichir.

Pour enrichir un message, il faut y insérer des marqueurs correspondants aux colonnes de la brique backoffice.
La nomenclature des marqueurs est %n où n correspond à un numéro de colonne de la brique backoffice

Exemple d'utilisation
=====================

Prenons un tableau avec la structure suivante:
---------------------------------------------------------
| Id | Prenom   | Nom       | Actions                   |
---------------------------------------------------------
| 1  | Armel    | FAUVEAU   | <a id="del-1">Suppr</a>   |
| 2  | Fred     | HOVART    | <a id="del-2">Suppr</a>   |
| 3  | Arnaud   | BUCHOUX   | <a id="del-3">Suppr</a>   |
| 4  | Julien   | OGER      | <a id="del-4">Suppr</a>   |
---------------------------------------------------------

Cette structure est volontairement simplifiée par rapport à une brique backoffice normale où les actions
sont encapsulées dans un tableau imbriqué et où les liens d'actions n'ont pas d'identifiant. 
Malgré cette subtilité, le comportement de backoffice_message sera le même.

<script type="text/javascript">
var msg = backoffice_message("#del-1", "Confirmer la suppression du compte de %2 %3?");
// msg vaut alors `Confirmer la suppression du compte de Armel FAUVEAU?`

var msg = backoffice_message("#del-4", "Confirmer la suppression du compte n°%1?");
// msg vaut alors `Confirmer la suppression du compte n°4?`
</script>

La fonction n'est efficace que si le paramètre `element` correspond à un seul élément de la brique backoffice.
Si ce paramètre correspond à plusieurs éléments html, le message risque d'être enrichi avec de mauvaises informations.


///////////////////////////////
// backoffice_row            //
///////////////////////////////


Description
===========

backoffice_row (element)

Retourne un objet JSON avec l'ensemble des éléments TD d'une brique backoffice situé sur la même ligne que `element`.
Cette fonction est principalement destinée à être utilisé dans les méthodes backoffice_message et backoffice_hover.

Paramètres
==========
element : Elément jQuery d'une brique backoffice ou sélecteur CSS correspondant

Exemple d'utilisation
=====================

Prenons un tableau avec la structure suivante:
---------------------------------------------------------
| Id | Prenom   | Nom       | Actions                   |
---------------------------------------------------------
| 1  | Armel    | FAUVEAU   | <a id="del-1">Suppr</a>   |
| 2  | Fred     | HOVART    | <a id="del-2">Suppr</a>   |
| 3  | Arnaud   | BUCHOUX   | <a id="del-3">Suppr</a>   |
| 4  | Julien   | OGER      | <a id="del-4">Suppr</a>   |
---------------------------------------------------------

Cette structure est volontairement simplifiée par rapport à une brique backoffice normale où les actions
sont encapsulées dans un tableau imbriqué et où les liens d'actions n'ont pas d'identifiant. 
Malgré cette subtilité, le comportement de backoffice_row sera le même.

<script type="text/javascript">
var content = backoffice_row("#del-1");
</script>

Dans cet exemple la variable content vaudra :
{
1: <td>1</td>,
2: <td>Armel</td>,
3: <td>FAUVEAU</td>,
4: <td><a id="del-1">Suppr</a></td>
}