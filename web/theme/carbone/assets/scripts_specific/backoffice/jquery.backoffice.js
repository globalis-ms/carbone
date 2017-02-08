/**
 * jQuery backoffice plugin 0.1
 *
 * Copyright (c) 2008 Armel FAUVEAU
 *
 * Revision: $Id$
 */

jQuery.fn.backoffice = function(context, ajax, url) {

    $(document).ready(function() {

        // Gestion de la pagination ajax

        if(ajax) {
            $('#'+context+" a:not([href*='action='],[href*='format='])").click(function() {
            $('#'+context+' .waiting').show();
                $.ajax({
                    type: 'GET',            
                    url: $(this).attr('href'),
                    data: 'ajax=on',
                    dataType: 'html',
                    success: function(msg) {
                        if(msg.substr(0, 10)!='<!DOCTYPE ') {
                            $('#'+context).html(msg);
                            $('#'+context).backoffice(context, ajax, url);
                        }
                        else
                            window.location=url;
                    },
                    error: function() {
                        alert('erreur !');
                    }
                });
                return false;
            });
            $('#'+context+' .waiting').fadeOut('slow');
        }
        
        // Gestion du masquage des actions 'global' et 'local'

        $('#'+context+' .bo-hide-on').click(function() {
            $('#'+context+' .bo-global, #'+context+' .bo-local, #'+context+' .bo-hide-on').hide();
            $('#'+context+' .bo-hide-off').show();

            document.cookie = 'carbone_cookie_backoffice='+cookie(context)+'; path=/';
        });
        $('#'+context+' .bo-hide-off').click(function() {
            $('#'+context+' .bo-global, #'+context+' .bo-local, #'+context+' .bo-hide-on').show();
            $('#'+context+' .bo-hide-off').hide();

            document.cookie = 'carbone_cookie_backoffice='+cookie(context)+'; path=/';
        });

        // Gestion du masquage des layers d'aide

        //$('#'+context+' .layer_titre').unbind('mousedown');
        //$('#'+context+' .layer_titre').mousedown(function(event) {
        //    $(this).next().toggle();
        //});

        // Gestion du click global sur les actions 'group'

        $('#'+context+'_group input[type=checkbox]:first').click(function() {
            if(this.checked) {
                $('#'+context+'_group').find('input[type=checkbox]').each(function(){
                    this.checked = true;
                });
            }
            else {
                $('#'+context+'_group').find('input[type=checkbox]').each(function(){
                    this.checked = false;
                });
            }
        });

    });

    // Capture du cookie 'carbone_cookie_backoffice'        

    function cookie(context) {
        var split, layer, valeur, debut, fin;

        split = '|';
        layer = context+split;
        valeur = '';

        if(document.cookie.indexOf('carbone_cookie_backoffice=')!=-1) {
            debut = document.cookie.indexOf('carbone_cookie_backoffice=')+26;
            fin   = document.cookie.indexOf(';',debut);
            if (fin < 0) fin = document.cookie.length;
            valeur=unescape(document.cookie.substring(debut,fin));
        }

        if(valeur.indexOf(layer)!=-1)
            valeur = valeur.replace(layer, '');
        else
            valeur = valeur+layer;

        return valeur;

    }

    function pause(millisecond) {
        var now = new Date();
        var exitTime = now.getTime() + millisecond;
        
        while(true) {
            now = new Date();
            if(now.getTime() > exitTime) return;
        }
    }
};

jQuery.fn.backoffice_confirm = function(message, options) {
    options = $.extend(true,{close:false},options);
    return this.each(function() {
        var msg = message;
        $(this).data('confirm', 1);
        $(this).click(function(e) {
            if(typeof $.notice == "function") {
                var item = $(this);
                if(item.data('confirm')) {
                    e.preventDefault();
                    e.stopPropagation();
                    $.confirm(backoffice_message(this, msg), function(response, element) {
                        if(response == true) {
                            item.removeData('confirm');
                            if(item.is('a') && item.attr('href').length)
                                location.href = item.attr('href');
                            item.click();
                            item.data('confirm',1);
                        }
                    }, options);
                }
            } else {
                if(confirm(backoffice_message(this, msg)))
                    return true;
                return false;
            }
        });
    });
};
    
jQuery.fn.backoffice_group = function(message, options) {
    options = $.extend(true,{close:false},options);
    return this.each(function() {
        $(this).click(function(e) {
            var item = $(this),
                action = item.attr('href') || false,
                msg = message;
                
            if(msg.length == 0) {
                backoffice_action_group(item, action);
                return false;
            }

            if(typeof $.notice == "function") {
                e.preventDefault();
                e.stopPropagation();
                $.confirm(msg, function(response, element) {
                    if(response == true)
                        backoffice_action_group(item, action);
                }, options);
            } else {
                if(confirm(msg))
                    backoffice_action_group(item, action);
            }
            return false;
        });
    });
};

function backoffice_action_group(element, action) {
    var form = $(element).closest('form');
    if(form && form.find('input:checked').length) {
        form.attr('action',action).submit();
        return true;
    }
    return false;
}

function backoffice_message(element, message) {
    // On récupère le contenu "reel" de la ligne courante
    var content = backoffice_row(element);
    // On insère les contenus dans le message
    $.each(content, function(i, td) {
        message = message.replace('%'+i,td.html());
    });
    return message;
};

function backoffice_row(element) {
    if(!$(element).data('backoffice_row')) {
        var eltTr = $(element).closest('tr');
        if($(element).closest('.bo-local') && $(element).closest('.bo-local').length > 0)
            eltTr = eltTr.closest('.bo-local').closest('tr');
        var numTd = eltTr.closest('table').find('tr:has(td):first > td').length,
            content = {}, fix = {},
            allTr = [],
            parse = true;
        // On récupére toutes les lignes tr à partir de notre élément jusqu'à une ligne sans rowspan
        while(parse && eltTr) {
            allTr.unshift(eltTr);
            if(eltTr.find('> td').length == numTd)
                parse = false;
            eltTr = eltTr.prev();
        }
        // On construit le tableau de contenu en prenant en compte les rowspan
        $.each(allTr, function(i, tr) {
            var l = 0;
            $('> td',tr).each(function(eq, td) {
                var i = (eq+l);
                while(typeof fix[i] != "undefined" && fix[i] > 0) {
                    fix[i]--; i++; l++;
                }
                if($(this).is('[rowspan]'))
                    fix[i] = $(this).attr('rowspan')-1;
                content[i+1] = $(this);
            });
        });
        $(element).data('backoffice_row', content);
    }
    return $(element).data('backoffice_row');
}