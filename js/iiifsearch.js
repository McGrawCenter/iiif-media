

jQuery(document).ready(function(){


	/***********************************
	* inital population of image gallery 
	***********************************/
	
	var d = {'action':'manifests'}
	
	console.log(iiifvars);
	
		jQuery(".iiif-media").empty();

		jQuery.getJSON( iiifvars.ajax_url, d, function( data ) { // get list of manifests
		  jQuery.each( data, function( key, url ) { // each manifest
		      var da = { 'action':'manifest','url':url }
		      jQuery.get( iiifvars.ajax_url, da, function(data){

			   var html = "<li>";
			   html += '<h3>'+data.title+'</h3>';
			   html += "<ul class='tile-grid'>";
			   jQuery.each(data.canvases, function(i,v){
			       html += '<li class="tile">';
			       html += '<img src="'+v.id+'/full/,150/0/default.jpg" class="iiifimage" rel="'+v.id+'" data-title="'+v.title+'" draggable="false" alt="" style="margin:0 auto;max-width:150px;">';
			       html += '<div class="meta">'+v.title+'</div></li>';
			   });
			 
			   html += "</li>";
		    	   jQuery(".iiif-media").append( html );


		      }); // end get ajax for single manifest
		  }); // end each manifest
		});




	jQuery('#iiif-insert').click(function(e){
	
		var img = jQuery('#attachment-details-iiif').val();
		var title = jQuery('#attachment-details-title').val();
		var caption = jQuery('#attachment-details-caption').val();
		var size = jQuery('#attachment-details-size').val();
		
		console.log(img,title,caption,size);
	
		html = '<figure class="wp-caption">';
		html += '<img src="'+img+'/full/'+size+',0/0/default.jpg">';
		html += '<figcaption class="wp-caption-text"><strong>'+title+'</strong><br />'+caption+'</figcaption>';
		html += '</figure>';
        	parent.wp.media.editor.insert(html);
	});





	



	/***********************************
	* User clicks on an image
	***********************************/

	  jQuery( document ).on( "click", ".tile", function() {
	  
	  	//var sidebar = jQuery('.attachment-details');
	  	//sidebar.empty();
		       		
	  	
	  	var html = "<div class='attachment-details'>\
	  		       <div style='width:90%;max-height:280px;text-align:center;padding:12px;'>\
				<img id='iiifmedia-selected-img' src='' data-iiif='' style='width:auto;height:auto;max-height:250px;'/>\
			       </div>\
	            <span class='setting' data-setting='title'>\
		 	<label for='attachment-details-title' class='name'>Title</label>\
			<textarea id='attachment-details-title'></textarea>\
		    </span>\
	           <span class='setting' data-setting='caption'>\
		 	<label for='attachment-details-caption' class='name'>Caption</label>\
			<textarea id='attachment-details-caption'></textarea>\
		    </span>\
		    <span class='setting' data-setting='size'>\
		 	<label for='attachment-details-size' class='name'>Size</label>\
			<select id='attachment-details-size'>\
			  <option value='240'>Small</option>\
			  <option value='300'>Medium</option>\
			  <option value='800'>Large</option>\
			  <option value='1200'>Larger</option>\
			</select>\
		    </span>\
	       </div>";
	       
	       
	       //sidebar.html(html);
	       
	      // add img to sidebar
	      var iiif = jQuery(this).find('img').attr('rel');
	      jQuery('#attachment-details-iiif').val(iiif);
	      //jQuery('#iiifmedia-selected-img').attr('src', iiif+'/full/200,/0/default.jpg');
	      //jQuery('#iiifmedia-selected-img').attr('data-iiif', iiif);
	      
	      // add title to sidebar
	      var title = jQuery(this).find('img').attr('data-title');
	      jQuery('#attachment-details-title').val(title);	       
	  
	  	/*
	        var imgwidth = jQuery('input[name=width]:checked').val();
        	var id = jQuery(this).attr('rel');
		var title = jQuery(this).parent().find('.iiifimageblock-title').text();
		
		console.log(title);
		var title = "<strong>Title</strong>: " + title;
		var imgurl = id+"/full/"+imgwidth+",/0/default.jpg";
		html = '<figure class="wp-caption">';
		html += '<img src="'+imgurl+'">';
		html += '<figcaption class="wp-caption-text">'+title+'<br /></figcaption>';
		html += '</figure>';
        	parent.wp.media.editor.insert(html);

		*/
	  });
	





});
