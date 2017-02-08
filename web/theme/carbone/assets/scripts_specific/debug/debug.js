function debug_toggle(id) {
	div_tab=document.getElementsByTagName("div"); 
	
	if(id=='debug_bar' || id=='debug') {
		var d = document.getElementById(id);
		d.style.display = d.style.display == 'none' ? '' : 'none';

		if(id=='debug_bar' && d.style.display=='none')
			document.cookie = "carbone_cookie_"+id+"=none; path=/";
		else
			document.cookie = "carbone_cookie_"+id+"=yes; path=/";

		document.cookie = "carbone_cookie_debug_info=none; path=/";

		for(var i = 0; i < div_tab.length; i++) { 
			if(div_tab[i].id){ 
				suffixe=div_tab[i].id.substring(0,11); 
				if(suffixe=="debug_info_") { 
					div_tab[i].style.display="none";
				} 
			} 
		} 
	}
	else if(id.substr(0,11)=='debug_menu_')  {
		var d = document.getElementById(id);

		for(var i = 0; i < div_tab.length; i++) { 
			if(div_tab[i].id){ 
				suffixe=div_tab[i].id.substring(0,11); 
				if(suffixe=="debug_menu_") { 
					div_tab[i].style.display="none";
				} 
			} 
		} 

		if (d) {d.style.display='block';}
	}
	else if(id.substr(0,11)=='debug_info_')  {
		var d = document.getElementById(id);

		if (d) {
			if(d.style.display == 'none') {
				d.style.display='';
				document.cookie = "carbone_cookie_debug_info="+id+"; path=/";
			}	
			else {
				d.style.display='none';
				document.cookie = "carbone_cookie_debug_info=none; path=/";
			}
		}

		for(var i = 0; i < div_tab.length; i++) { 
			if(div_tab[i].id){ 
				suffixe=div_tab[i].id.substring(0,11); 
				if(suffixe=="debug_info_" && div_tab[i].id!=id) { 
					div_tab[i].style.display="none";
				} 
			} 
		} 
	}
}

function create_request_object() {
	var ro;
	var browser = navigator.appName;

	if(browser == "Microsoft Internet Explorer"){
		ro = new ActiveXObject("Microsoft.XMLHTTP");
	}
	else{
		ro = new XMLHttpRequest();
	}
	return ro;
}

var http = create_request_object();

function send_request(action, url) {
	http.open('get', url+'lib_debug_ajax.php?debug_ajax_action='+action+'&url='+url);
	http.onreadystatechange = handle_response;
	http.send(null);
}

function handle_response() {
	if(http.readyState == 4){
		var response = http.responseText;
		var update = new Array();

		if(response.indexOf('|' != -1)) {
			update = response.split('|');
			foo=document.getElementById(update[0]).innerHTML.replace('<PRE>','<pre>');
			foo=foo.split('<pre');
			document.getElementById(update[0]).innerHTML = foo[0]+'<pre>'+update[1]+'</pre>';

			var d = document.getElementById(update[0]);

			if(d.style.display != 'none') {
				setTimeout("send_request('"+update[2]+"','"+update[3]+"')",2000);
			}
			else {
				var timer=setTimeout("send_request('"+update[2]+"','"+update[3]+"')",2000);
				clearTimeout(timer);
			}

		}
	}
}

function Browser() {

  var ua, s, i;

  this.isIE    = false;
  this.isNS    = false;
  this.version = null;

  ua = navigator.userAgent;

  s = "MSIE";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isIE = true;
    this.version = parseFloat(ua.substr(i + s.length));
    return;
  }

  s = "Netscape6/";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isNS = true;
    this.version = parseFloat(ua.substr(i + s.length));
    return;
  }

  // Treat any other "Gecko" browser as NS 6.1.

  s = "Gecko";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isNS = true;
    this.version = 6.1;
    return;
  }
}

var browser = new Browser();

// Global object to hold drag information.

var dragObj = new Object();
dragObj.zIndex = 0;

function dragStart(event, id) {

  var event = window.event || event;
  var targ = event.target || event.srcElement;

  if(targ.tagName !== 'DIV') {
    return false;
  }

  var el;
  var x, y;

  // If an element id was given, find it. Otherwise use the element being
  // clicked on.

  if (id)
    dragObj.elNode = document.getElementById(id);
  else {
    if (browser.isIE)
      dragObj.elNode = window.event.srcElement;
    if (browser.isNS)
      dragObj.elNode = event.target;

    // If this is a text node, use its parent element.

    if (dragObj.elNode.nodeType == 3)
      dragObj.elNode = dragObj.elNode.parentNode;
  }

  // Get cursor position with respect to the page.

  if (browser.isIE) {
    x = window.event.clientX + document.documentElement.scrollLeft
      + document.body.scrollLeft;
    y = window.event.clientY + document.documentElement.scrollTop
      + document.body.scrollTop;
  }
  if (browser.isNS) {
    x = event.clientX + window.scrollX;
    y = event.clientY + window.scrollY;
  }

  // Save starting positions of cursor and element.

  dragObj.cursorStartX = x;
  dragObj.cursorStartY = y;
  dragObj.elStartLeft  = parseInt(dragObj.elNode.style.left, 10);
  dragObj.elStartTop   = parseInt(dragObj.elNode.style.top,  10);

  if (isNaN(dragObj.elStartLeft)) dragObj.elStartLeft = 0;
  if (isNaN(dragObj.elStartTop))  dragObj.elStartTop  = 0;

  // Update element's z-index.

  dragObj.elNode.style.zIndex = ++dragObj.zIndex;

  // Capture mousemove and mouseup events on the page.

  if (browser.isIE) {
    document.attachEvent("onmousemove", dragGo);
    document.attachEvent("onmouseup",   dragStop);
    window.event.cancelBubble = true;
    window.event.returnValue = false;
  }
  if (browser.isNS) {
    document.addEventListener("mousemove", dragGo,   true);
    document.addEventListener("mouseup",   dragStop, true);
    event.preventDefault();
  }
}

function dragGo(event) {

  var x, y;

  // Get cursor position with respect to the page.

  if (browser.isIE) {
    x = window.event.clientX + document.documentElement.scrollLeft
      + document.body.scrollLeft;
    y = window.event.clientY + document.documentElement.scrollTop
      + document.body.scrollTop;
  }
  if (browser.isNS) {
    x = event.clientX + window.scrollX;
    y = event.clientY + window.scrollY;
  }

  // Move drag element by the same amount the cursor has moved.

  dragObj.elNode.style.left = (dragObj.elStartLeft + x - dragObj.cursorStartX) + "px";
  dragObj.elNode.style.top  = (dragObj.elStartTop  + y - dragObj.cursorStartY) + "px";

  if (browser.isIE) {
    window.event.cancelBubble = true;
    window.event.returnValue = false;
  }
  if (browser.isNS)
    event.preventDefault();
}

function dragStop(event) {

  // Stop capturing mousemove and mouseup events.

  if (browser.isIE) {
    document.detachEvent("onmousemove", dragGo);
    document.detachEvent("onmouseup",   dragStop);
  }
  if (browser.isNS) {
    document.removeEventListener("mousemove", dragGo,   true);
    document.removeEventListener("mouseup",   dragStop, true);
  }

  document.cookie = "carbone_cookie_debug_info_left="+dragObj.elNode.style.left+"; path=/";
  document.cookie = "carbone_cookie_debug_info_top="+dragObj.elNode.style.top+"; path=/";
}