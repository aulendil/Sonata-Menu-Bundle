'use strict'
jQuery(document).ready(function($){var list=null;var results=[];var items=jQuery('#items');jQuery('#nestable').nestable({callback:function(l,e){list=l.length?l:jQuery(l.target);results=JSON.stringify(list.nestable('serialize'));items.val(results)}})})