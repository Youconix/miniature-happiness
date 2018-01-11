function FileUploader() {
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
};
/**
 * Inits the uploader class
 * 
 * @param int	  id	The unique id
 * @param string  url	The url to call
 * @param string  formID	The form ID
 */
FileUploader.prototype.init = function (id, url, formID) {
  this.id = id;
  this.url = url;
  this.formID = formID;
};

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
FileUploader.prototype.getPreview = function (target, extensions, maxWidth, maxHeight) {
  extensions = extensions || {"jpg": "image/jpg", "jpeg": "image/jpeg", "png": "image/png", "gif": "image/gif",
    "tiff": "image/tiff", "bmp": "image/bmp", "svg": ["image/svg", "image/svg+xml"], "webp": "image/webp"};
  maxWidth = maxWidth || 600;
  maxHeight = maxHeight || 600;

  if (document.getElementById(this.id).files.length === 0) {
    return -2;
  }

  var file = document.getElementById(this.id).files[0];

  try {
    var reader = new FileReader();

    var _this = this;
    reader.onload = function (oFREvent) {
      var image = new Image();
      image.onload = function (evt) {
	var width = this.width;
	var height = this.height;

	var ratio = 1;

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

	var newWidth = Math.round(width * ratio);
	var newHeight = Math.round(height * ratio);

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
};

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
FileUploader.prototype.upload = function (extensions, maxSize) {
  maxSize = maxSize || -1;

  this.frameID = "uploader" + this.id;

  try {
    var reader = new FileReader();

    var _this = this;
    if (document.getElementById(_this.id).files.length === 0) {
      return -2;
    }

    reader.onload = function (oFREvent) {
      _this.uploadDocument();

      return true;
    };

    var file = document.getElementById(_this.id).files[0];
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
};

/**
 * Checks the accepted mimetypes
 *
 * @param string	filetype	The file type	
 * @param	array extensions The accepted extensions
 * @return boolean	True if the mimetype is accepted
 */
FileUploader.prototype.checkMimetypes = function (filetype, extensions) {
  var id;
    
  for (id in extensions) {
    if ((extensions[id] === filetype) || (filetype === '' && this.specialMimeTypes(extensions[id]))) {
      return true;
    }
  }
  
  return false;
};

FileUploader.prototype.specialMimeTypes = function(mimeType) {
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
 * @param	array extensions The accepted extensions
 * @return boolean	True if the extension is accepted
 */
FileUploader.prototype.checkExtensions = function (filename,filetype, extensions) {
  var id,extension;
  
  extension = filename.toLowerCase().split("\.");
  extension = extension[extension.length - 1];
  
  for (id in extensions) {
    if (id === extension) {
      return this.checkMimetypes(filetype,extensions[id]);
    }
  }
};

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
FileUploader.prototype.uploadDocumentOld = function (extensions) {
  var filename = $("#" + this.id).val();

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
};

FileUploader.prototype.getID = function () {
  return this.frameID;
};

/**
 * Uploads the loaded file
 */
FileUploader.prototype.uploadDocument = function () {
  var _this = this;

  var iframe = $('<iframe name="' + this.frameID + '" id="' + this.frameID + '" style="display:none"></iframe>');
  $("body").append(iframe);

  _this.sendForm();
};

FileUploader.prototype.sendForm = function () {
  window.clearInterval(this.event);

  _this = this;
  var form = $("#" + _this.formID);
  form.attr("target", this.frameID);
  form.attr("method", "post");
  form.attr("enctype", "multipart/form-data");
  form.attr("encoding", "multipart/form-data");
  form.attr("action", _this.url);
  form.submit();

  var _this = this;
  this.timer = setTimeout(function () {
    _this.check();
  }, 1000);
};

FileUploader.prototype.check = function () {
  var el = $("#" + this.frameID).contents().find("body").html();

  if (((typeof el) === "undefined") || el.length < 1) {
    var _this = this;
    this.timer = setTimeout(function () {
      _this.check();
    }, 1000);
    return;
  }

  this.complete();
};

FileUploader.prototype.getResponse = function () {
  return this.response;
};

FileUploader.prototype.complete = function () {
  $("#" + this.frameID).unbind("load");
  this.response = $('#'+this.frameID).contents().text();
  $("#" + this.frameID).remove();
  this.done = true;
};

FileUploader.prototype.isDone = function () {
  return this.done;
};

FileUploader.prototype.getSize = function () {
  return this.fileSize;
};

FileUploader.prototype.getFileName = function () {
  return this.filename;
};
