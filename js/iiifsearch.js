

jQuery(document).ready(function(){


	/***********************************
	* initial population of image gallery 
	***********************************/
	
	var d = {'action':'manifests'}
	jQuery("#iiifsearchresults").empty();
	
	jQuery.getJSON( vars.ajax_url, d, function( data ) { // get list of manifests

	  jQuery.each( data, function( index, value ) { // each manifest

	    	  var html = "<div>";
	  	  html += '<ul style="display:flex">';
	    
	         jQuery.each(value.images, function(i,v){
	            html += '<li class="iiifimageblock">';
html += "<img src='"+v.thumb+"' class='iiifimage' rel='"+v.url+"' draggable='false' alt='' style='margin:0 auto;max-width:150px;'>";
html += "<div class='iiifimageblock-title'>"+v.label+"</div></li>";
	            html += "</li>";
	         });
	         html += "</ul>";
		 html += '<strong>'+value.label+'</strong>';
	         html += "</div>";
	    	 jQuery("#iiifsearchresults").append( html );
	       
	    

	  });


	});
	



	/***********************************
	* User clicks on an image
	***********************************/

	  jQuery( document ).on( "click", ".iiifimage", function() {
	  
	        var imgwidth = jQuery('input[name=width]:checked').val();
        	var url = jQuery(this).attr('rel');
		var title = jQuery(this).parent().find('.iiifimageblock-title').text();

		var imgurl = url+"/full/"+imgwidth+",/0/default.jpg";
		
		html = '<figure class="wp-caption">';
		html += '<img src="'+imgurl+'">';
		html += '<figcaption style="width:'+imgwidth+'px">'+title+'</figcaption>';
		html += '</figure>';
        	parent.wp.media.editor.insert(html);


	  });
	


});
