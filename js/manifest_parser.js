class ManifestParser {

  constructor() {
	this.label = "";
	this.thumbnail = "";
	this.url = "";
	this.images = [];
  }


  
  
  load(manifest_uri) {

    this.url = manifest_uri;
    fetch(manifest_uri)
          .then(response => {
                  if (!response.ok) {
                      throw new Error(response.statusText);
                  }
                  return response.json();
          })
          .then(data => {
            if ("@type" in data) {
                if (data["@type"] != 'sc:Manifest') {
                    console.log( 'Manifest Format Error', 'The JSON for this Manifest doesnt look like a Manifest. It should have either a @type of sc:Manifest but has a type of: ' + data["@type"]);
                } else {
                    this.parsev2(data);
                }
            } else if ("type" in data) {
                if (data["type"] != 'Manifest') {
                    console.log( 'Manifest Format Error', 'The JSON for this Manifest doesnt look like a Manifest. It should have either a type of Manifest but has a type of: ' + data["type"]);
                } else {
                    this.parsev3(data);
                }
            } else {
                console.log( 'Manifest Format Error', 'The JSON for this Manifest doesnt look like a Manifest. It should have either a @type or type value of Manifest');
            }
          })
          .catch(error => {
                console.log( 'Manifest retrieval error', 'I was unable to get the Manifest you supplied due to: ' + error);
      }); // end fetch
      
  }
  
  
  
  /****
  * 
  *************************************/
  parsev2 (manifest) {
      if(manifest.label && Array.isArray(manifest.label)) { 
         this.label = manifest.label[0];
       }
      else if(manifest.label && typeof manifest.label === "string") { 
         this.label = manifest.label;
      }
      
      // thumbnail
      if(manifest.thumbnail && typeof manifest.thumbnail === 'object') { 
         this.thumbnail = manifest.thumbnail['@id'];
       }
      else if(manifest.thumbnail && typeof manifest.thumbnail === "string") { this.thumbnail = this.thumbnail; }      
      else {
        var firstcanvas = manifest.sequences[0].canvases[0];
        this.thumbnail = this.getCanvasThumbnail(firstcanvas,'',150);
      }      
      
      if(manifest.sequences) {
        var sequences = manifest.sequences;
        for (const sequence of sequences) {
          if ('canvases' in sequence) {
            for (const canvas of sequence.canvases) {
              var label = canvas.label;
              var thumb = this.getCanvasThumbnail(canvas,'',150);
              var url = canvas.images[0].resource.service["@id"];
              var o = {}
              o.label = label;
              o.thumb = thumb;
              o.url = url;
              this.images.push(o);
            }
          }
        }
      }   
  }


  /****
  * 
  *************************************/
  parsev3 (manifest) {
      if(manifest.label && typeof manifest.label === "object") { 
         this.label = Object.values(manifest.label)[0][0];
       }    
      else if(manifest.label && Array.isArray(manifest.label)) { 

         this.label = manifest.label[0][0];
       }
      else if(manifest.label && typeof manifest.label === "string") { 
         this.label = manifest.label;
      }
      else { 
      
      }
      
      // thumbnail
      if(manifest.thumbnail && typeof manifest.thumbnail === 'object') { 
         this.thumbnail = manifest.thumbnail[0].id;
       }      
      else if(manifest.thumbnail && typeof manifest.thumbnail === 'array') { 
         this.thumbnail = manifest.thumbnail['id'];
       }
      else if(manifest.thumbnail && typeof manifest.thumbnail === "string") { this.thumbnail = this.thumbnail; }      
      else {
        var firstcanvas = manifest.items[0];
        this.thumbnail = this.getCanvasThumbnail(firstcanvas, '',150);
      }      
      
      if(manifest.items) {
        var items = manifest.items;
        for (const item of items) {
            var label = Object.values(item.label)[0][0];
            var thumb = this.getCanvasThumbnail(item, '',150);
            var url = item.id;
            var o = {}
            o.label = label;
            o.thumb = thumb;
            o.url = url;
            this.images.push(o);
        }
      }      
  }
  
  /****
  * 
  *************************************/
  getCanvasThumbnail (canvas, width="", height="") {
     var service = "";
     if(canvas.thumbnail) {
         if (typeof canvas.thumbnail === "string") { service = canvas.thumbnail; }
         
         else if (Array.isArray(canvas.thumbnail)) {
	     service = canvas.thumbnail[0].service[0]['@id'];
         }    
         else if (typeof canvas.thumbnail === "object") {
	     service = canvas.thumbnail.service['@id'];
         }
      
     }
     return service+"/full/"+width+","+height+"/0/default.jpg";
   }
  


}
