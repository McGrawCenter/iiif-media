jQuery(document).ready(function(){

	function displayObject(obj) {
	  var html = "<h3>"+obj.label+"</h3>";
	  jQuery.each(obj.images, function(i,v){
	    html += "<img src='"+v.thumb+"'/>";
	  });
	  return html;
	}


	function getManifestVersion(data) {
	  if(Array.isArray(data['@context'])) {
	     if(data['@context'].indexOf("http://iiif.io/api/presentation/3/context.json") ) {
	       return 3;
	     }
	     else {
	       return 2;
	     }
	  }
	  else {
	     if(data['@context'].includes("http://iiif.io/api/presentation/3/context.json") ) {
	       return 3;
	     }
	     else {
	       return 2;
	     }
	  }
	}


	jQuery("#manifesturl").on('input', function() { 
	
	  if(jQuery(this).val() != "") {
	
	    var url = jQuery(this).val();
	    

	    var m = new ManifestParser();
	    m.load(url);
	    
	    function doStuff() {
		if(m.images.length==0) {
		    setTimeout(doStuff, 500);
		    return;
		}
		jQuery("#manifestobj").empty();
	    	jQuery("#manifestpreview-content").empty();
	        jQuery("#manifestobj").val(JSON.stringify(m));
	        jQuery("#manifestpreview-content").append(displayObject(m));
	        jQuery("#manifestpreview").show();
	        
	    }

	    doStuff();

	  }
	});
});
