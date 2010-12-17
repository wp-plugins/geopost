var MAP=null;var PREVIEW_MARKER=null;var ICON=0;var ICON_DIR=null;function iconURL(a){if(ICON_DIR==null){return null}if(a==null){a=0}a=a%7;return ICON_DIR+"/icon"+a+".png"}function shadowURL(){if(ICON_DIR==null){return null}return ICON_DIR+"/shadow.png"}function geopost_add_marker(h,d,g,e,f){var f=new google.maps.MarkerImage(iconURL(f));var c=new google.maps.Size(59,32);var i=new google.maps.Point(0,0);var a=new google.maps.Point(16,32);var k=new google.maps.MarkerImage(shadowURL(),c,i,a);var j=new google.maps.LatLng(d,g);h=h.replace("&#8211;","-");var b=new google.maps.Marker({map:MAP,position:j,icon:f,shadow:k,title:h});google.maps.event.addListener(b,"click",function(){if(e!=null){window.location=e}})}function geopost_set_preview_marker(a,e,g,c){if(PREVIEW_MARKER!=null){PREVIEW_MARKER.setMap(null)}if((e===null)||(g===null)){document.getElementById(a).style.height="0px";document.getElementById(a).style.borderWidth="0px";google.maps.event.trigger(MAP,"resize");document.getElementById("geopost_lat").value=null;document.getElementById("geopost_lng").value=null;document.getElementById("geopost_icon").value=null}else{if(document.getElementById(a).style.height=="0px"){document.getElementById(a).style.borderWidth="3px";document.getElementById(a).style.height="150px";google.maps.event.trigger(MAP,"resize")}var i=new google.maps.LatLng(e,g);ICON=c;var f=iconURL(c);var d=new google.maps.Size(59,32);var h=new google.maps.Point(0,0);var b=new google.maps.Point(16,32);var j=new google.maps.MarkerImage(shadowURL(),d,h,b);PREVIEW_MARKER=new google.maps.Marker({map:MAP,position:i,icon:f,shadow:j});google.maps.event.addListener(PREVIEW_MARKER,"click",function(){ICON=(ICON+1)%7;document.getElementById("geopost_icon").value=ICON;PREVIEW_MARKER.setIcon(iconURL(ICON))});MAP.setCenter(i);MAP.setZoom(5);document.getElementById("geopost_lat").value=i.lat();document.getElementById("geopost_lng").value=i.lng();document.getElementById("geopost_icon").value=c}}function geopost_geocode(b,a){geocoder=new google.maps.Geocoder();geocoder.geocode({address:a},function(e,d){if(d==google.maps.GeocoderStatus.OK){var f=e[0].geometry.location;var c=e[0].geometry.viewport;geopost_set_preview_marker(b,f.lat(),f.lng(),ICON);MAP.fitBounds(c)}})}function geopost_map(c,g,e,i,d,f){if(d===undefined){d=23}if(f===undefined){f=0}if(i===undefined){i=0}if((e===undefined)||(e==0)){e=google.maps.MapTypeId.SATELLITE}else{e=google.maps.MapTypeId.HYBRID}if(!(g===undefined)){ICON_DIR=g}var a=new google.maps.LatLng(d,f);var j={zoom:i,center:a,mapTypeId:e,mapTypeControl:false,navigationControl:true,streetViewControl:false,navigationControlOptions:{style:google.maps.NavigationControlStyle.SMALL},scaleControl:false};MAP=new google.maps.Map(document.getElementById(c),j);var h="Download Geopost";if(document.getElementById(c).offsetWidth<250){h="Geopost"}var b=document.createElement("DIV");b.style.fontSize="11px";b.style.marginBottom="2px";b.style.marginTop="2px";b.style.marginRight="2px";b.style.marginLeft="2px";b.innerHTML='<a href="http://www.rampantlogic.com/geopost/geopost.html"> <font color="white">'+h+"</font></a>";MAP.controls[google.maps.ControlPosition.TOP_RIGHT].push(b)};