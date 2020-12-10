//https://wordpress.stackexchange.com/questions/185271/how-to-add-new-tab-to-media-upload-manager-with-custom-set-of-images/313729#313729

var l10n = wp.media.view.l10n;
wp.media.view.MediaFrame.Select.prototype.browseRouter = function( routerView ) {
    routerView.set({
        upload: {
            text:     l10n.uploadFilesTitle,
            priority: 20
        },
        browse: {
            text:     l10n.mediaLibraryTitle,
            priority: 40
        },
        coursecollection: {
            text:     "Course Collection",
            priority: 60
        },
        /*
        iiif: {
            text:     "Manifest",
            priority: 60
        }
        */
    });
};



jQuery(document).ready(function($){


    if ( wp.media ) {

        wp.media.view.Modal.prototype.on( "open", function() {
        
            var activebutton = jQuery(wp.media).find('.media-router button.active');
            if(activebutton.attr('id') == 'menu-item-coursecollection' ) { doCourseCollectionContent(); }

	    jQuery(wp.media).on('click', '#menu-item-coursecollection', function(e){
               doCourseCollectionContent();
            });


        });

    }
    
    
    jQuery(document).on('click','.addmanifest', function(){
        var url = jQuery('#iiif-manifest').val();
        var d = { 'action':'manifest','url':url }
    	jQuery.get(iiifvars.ajax_url, d, function(data){
    	
    	  jQuery(".iiif-media").empty();
    	
    	
	  var html = "<li>";
  	  html += '<h3>'+data.title+'</h3>';
  	  html += "<ul class='tile-grid'>";
    
	 jQuery.each(data.canvases, function(i,v){
	    html += '<li class="tile">';
	    html += '<img src="'+v.id+'/full/,150/0/default.jpg" class="iiifimage" rel="'+v.id+'" draggable="false" alt="" style="margin:0 auto;max-width:150px;">';
	    html += '<div class="meta">'+v.title+'</div></li>';
	 });
	 
	 html += "</li>";
    	 jQuery(".iiif-media").append( html );
    	});

    });
    

    
    /****************************
    * click a tile
    ****************************/
    jQuery(document).on('click','.tile',function(){
    
      jQuery(".tile").removeClass('selected details');
      jQuery(this).addClass('selected details');
      
      // add img to sidebar
      var iiif = jQuery(this).find('img').attr('rel');
      jQuery('#iiifmedia-selected-img').attr('src',iiif+'/full/200,/0/default.jpg');
      jQuery('#iiifmedia-selected-img').attr('data-iiif', iiif);
      
      // add title to sidebar
      var title = jQuery(this).find('img').attr('data-title');
      jQuery('#attachment-details-title').val(title);

      jQuery('.media-button-insert').prop('disabled',false);
      jQuery('.media-button-select').prop('disabled',false);
      var html = "gotcha";
      //parent.wp.media.editor.insert(html);

    });


    jQuery('.media-button-select').click(function(e){
     alert('featured');
     //e.preventDefault();
    });
    
    jQuery('.media-button-insert').click(function(e){
     alert('insert');
     //e.preventDefault();
    });
   
    
    
    
}); // end document ready







    /****************************
    * populate the gallery
    ****************************/
	function doCourseCollectionContent() {
	
	  var wait_icon = iiifvars.plugin_url+"/images/loading.gif";

	  var setup = "<div class='attachments-browser'>\
	      <div class='media-toolbar'>\
	      	<p><label for='iiif-manifest'>Add Manifest</label> <input type='text' id='iiif-manifest' name='iiif-manifest'/> <button class='addmanifest'>Go</button></p>\
	      </div>\
	      <ul class='iiif-media'></ul>\
	      <div class='media-sidebar active'>\
	       <div style='width:90%;max-height:280px;text-align:center;padding:12px;'>\
	        <img id='iiifmedia-selected-img' src='' data-iiif='' style='width:auto;height:auto;max-height:250px;'/>\
	       </div>\
	       <div class='attachment-details'>\
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
	       </div>\
	      </div>\
	      </div>\
	    </div>";


	    jQuery('.media-frame-content').html(setup);
	    
	    jQuery(".iiif-media").empty();
	    
	    var d = {'action':'manifests'}
   
	    
	    jQuery.getJSON( iiifvars.ajax_url, d, function( data ) { // get list of manifests


		jQuery.each( data, function( key, url ) { // each manifest

		    var da = { 'action':'manifest','url':url }
		    jQuery.get(iiifvars.ajax_url, da, function(data){
		    	
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
		       
		    });
		  });
	    });
	    
    /****************************
    * end populate the gallery
    ****************************/    
   

}





