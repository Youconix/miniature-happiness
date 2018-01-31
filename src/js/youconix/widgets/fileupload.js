class FileUploader{
  constructor(){
    this.response;
    this.id;
    this.frameID;
    this.formID;
    this.done = false;
    this.event;
    this.url;

    this.filename;
    this.fileSize;

    this.timer;
  }
  /**
   * Inits the uploader class
   * 
   * @param int	  id	The unique id
   * @param string  url	The url to call
   * @param string  formID	The form ID
   */
  init(id, url, formID) {
    this.id = id;
    this.url = url;
    this.formID = formID;
  }
  /**
   * Displays the preview from the given image in the given target image
   * This preview need FileReader support
   *
   * @param	String	target	The image id
   * @param  array	extensions	The extensions with the mimetype as value, default all pictures
   * @param	int	maxWidth		The maximun width, default 600
   * @param	int	maxHeight		The maximun height, default 600
   * @return int		The status code
   * 		-2	No file
   *		-1	Not a image
   *		0		FileReader not supported
   *		1	Image displayed
   */
  getPreview(target, extensions = null, maxWidth = 600, maxHeight = 600) {
    if (extensions === null){
      extensions = {"jpg": "image/jpg", "jpeg": "image/jpeg", "png": "image/png", "gif": "image/gif",
	"tiff": "image/tiff", "bmp": "image/bmp", "svg": ["image/svg", "image/svg+xml"], "webp": "image/webp"};
    }
    
    if (document.getElementById(this.id).files.length === 0) {
      return -2;
    }

    let file = document.getElementById(this.id).files[0];

    try {
      let reader = new FileReader();

      reader.onload = function (oFREvent) {
	let image = new Image();
	image.onload = function (evt) {
	  let width = this.width;
	  let height = this.height;

	  let ratio = 1;

	  if (width > height) {
	    if (width > maxWidth) {
	      ratio = maxWidth / width;
	    } else if (height > maxHeight) {
	      ratio = maxHeight / height;
	    }
	  } else if (height > width) {
	    if (height > maxHeight) {
	      ratio = maxHeight / height;
	    } else if (width > maxWidth) {
	      ratio = maxWidth / width;
	    }
	  }

	  let newWidth = Math.round(width * ratio);
	  let newHeight = Math.round(height * ratio);

	  $("#" + target).css("width", newWidth);
	  $("#" + target).css("height", newHeight);
	};

	image.src = oFREvent.target.result;
	document.getElementById(target).src = oFREvent.target.result;
      };

      if (!this.checkExtensions(file.name, extensions) || !this.checkMimetypes(file.type, extensions)) {
	return -1;
      }

      reader.readAsDataURL(file);
      return 1;
    } catch (error1) {
      if (this.checkExtensions(file.name, extensions)) {
	return 0;
      }

      return -1;
    }
  }
  /**
   * Uploads the given file
   * For HTML 5 upload is FileReader support needed
   * 
   * @param  array	extensions	The extensions with the mimetype as value
   * @params	int	maxSize		The maximum size, optional
   * @return int		The status code
   *		-2		File is empty
   *		-1		Invalid mime type		
   *		0			File to big
   *		1			Uploading
   */
  upload(extensions, maxSize = -1) {
    this.frameID = "uploader" + this.id;

    try {
      let reader = new FileReader();

      if (document.getElementById(this.id).files.length === 0) {
	return -2;
      }

      reader.onload = (oFREvent) => {
	this.uploadDocument();

	return true;
      };

      let file = document.getElementById(this.id).files[0];
      this.filename = file.name;
      this.fileSize = file.size;

      if (!this.checkExtensions(file.name,file.type, extensions) ) {
	return -1;
      }

      if (maxSize !== -1 && maxSize < file.size) {
	return 0;
      }

      reader.readAsDataURL(file);

      return 1;
    } catch (error2) {
      return this.uploadDocumentOld(extensions);
    }
  }
  /**
   * Checks the accepted mimetypes
   *
   * @param string	filetype	The file type	
   * @param	array extensions The accepted extensions
   * @return boolean	True if the mimetype is accepted
   */
  checkMimetypes(filetype, extensions) {
    for (let id in extensions) {
      if ((extensions[id] === filetype) || (filetype === '' && this.specialMimeTypes(extensions[id]))) {
	return true;
      }
    }

    return false;
  }
  specialMimeTypes(mimeType) {
    if (mimeType === 'text/csv' || mimeType === 'text/comma-separated-values') {
      return true;
    }
	
    return false;
  }
  /**
   * Checks the accepted extensions
   *
   * @param string	filename	The file name			 
   * @param string	filetype	The file type	
   * @param array extensions The accepted extensions
   * @return boolean	True if the extension is accepted
   */
  checkExtensions (filename,filetype, extensions) {
    let extension;

    extension = filename.toLowerCase().split("\.");
    extension = extension[extension.length - 1];

    for (let id in extensions) {
      if (id === extension) {
	return this.checkMimetypes(filetype,extensions[id]);
      }
    }
  }
  /**
   * Old way of file uploading. 
   * No FileReader support
   * 
   * @params	array extensions The accepted extensions
   * @return int		The status code
   -2		File is empty
   -1		Invalid extension
   1			Uploading
   */
  uploadDocumentOld(extensions) {
    let filename = $("#" + this.id).val();

    if (filename === "") {
      return -2;
    }

    if (!this.checkExtensions(filename, extensions)) {
      return -1;
    }

    this.uploadDocument();

    this.filename = filename;
    this.fileSize = -1;

    return 1;
  }
  getID() {
    return this.frameID;
  }
  /**
   * Uploads the loaded file
   */
  uploadDocument() {
    let iframe = $('<iframe name="' + this.frameID + '" id="' + this.frameID + '" style="display:none"></iframe>');
    $("body").append(iframe);

    this.sendForm();
  }
  sendForm() {
    window.clearInterval(this.event);

    let form = $("#" + this.formID);
    form.attr("target", this.frameID);
    form.attr("method", "post");
    form.attr("enctype", "multipart/form-data");
    form.attr("encoding", "multipart/form-data");
    form.attr("action", this.url);
    form.submit();

    this.timer = setTimeout(() => {
      this.check();
    }, 1000);
  }
  check() {
    let el = $("#" + this.frameID).contents().find("body").html();

    if (((typeof el) === "undefined") || el.length < 1) {
      this.timer = setTimeout(() => {
	this.check();
      }, 1000);
      return;
    }

    this.complete();
  }
  getResponse() {
    return this.response;
  }
  complete() {
    $("#" + this.frameID).unbind("load");
    this.response = $('#'+this.frameID).contents().text();
    $("#" + this.frameID).remove();
    this.done = true;
  }
  isDone() {
    return this.done;
  }
  getSize() {
    return this.fileSize;
  }
  getFileName() {
    return this.filename;
  }
}
