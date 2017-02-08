Plugin à appliquer sur un champ select multiple.

Usage :

Par défaut (simple click pour faire transiter les éléments et on garde l'ordre)
$('#id').multiselect();

Double click pour faire transiter les éléments 
$('#id').multiselect({event:'dblclick'});

On ne garde pas l'ordre
$('#id').multiselect({order:false});

Combinaison des 2 paramétrages précédents
$('#id').multiselect({event:'dblclick',order:false});

On assigne une classe CSS différentes pour les 2 containers
$('#id').multiselect({selectClass:'other',cloneClass:'other_clone'});