import $ from 'jquery';
import Dropzone from '../vendors/dropzone/dist/dropzone.js';

class UploadImage {

  constructor() {
    this.uploadBtn = $(".dropzoneBtnJS");
    this.dropzoneArea = $(".dropzone-area");
    this.active = false;
    this.formTextarea = $("#body");
    this.maxFileSize = server.maxFileSize/1024/1000;
    this.dropzone();
    this.events();
  }

  dropzone() {
    Dropzone.autoDiscover = false;

    this.dropzone = new Dropzone(
      ".dropzone-area",
      { url: server.baseUrl + '/form_post.php' }
    );

    this.dropzone.options.maxFiles = this.postImgMaxNum;
    this.dropzone.options.maxFilesize = this.maxFileSize;
    this.dropzone.options.acceptedFiles = '.gif, .jpg, .jpeg, .png';
    this.dropzone.options.addRemoveLinks = true;
    this.dropzone.options.dictRemoveFile = 'Delete';

    this.dropzone.on("sending", (file, xhr, formData) => {
      formData.append("dropzone", "upload");
      this.uploadBtn.closest("form").off("submit");
    });

    this.dropzone.on("error", error => {
      console.log("Error ->", error)
    })

    this.dropzone.on("complete", data => {
      if (typeof data.xhr != "undefined") {
        const res = JSON.parse(data.xhr.response);
        if (res[0] == "success") {
          this.successHandler(res[1]);
        }
      } else {
        this.dropzone.on("thumbnail", () => {
          this.dataDzRemove("")
        })
      }

      this.uploadBtn.closest("form").on("submit");
    });
  }

  events() {
    this.uploadBtn.on("click", this.uploadClickHandler.bind(this));

    this.dropzoneArea.find(".dropzone-area-hint").on("click", e => {
      $(e.target).closest(".dropzone-area").trigger("click");
    })
  }

  remove(e) {
    const image = $(e.target).attr("data-dz-remove");
    this.requestServer('remove', image);
  }

  requestServer(action, image) {
    $.ajax({
      url: server.baseUrl + '/form_post.php',
      type: "POST",
      data: {
        image: image,
        dropzone: action
      },
      success: res => {
        const data = JSON.parse(res);
        console.log("DATA ui", data);
        if (data[0] == 'success') {
          if (action == "remove") {
            this.formContentRemove(image);
          }
        }
      }
    })
  }

  formContentRemove(image) {
    let postBody = this.formTextarea.val();
    postBody = postBody.replace(image, "", postBody);
    this.formTextarea.val(postBody);
    console.log("Inside function!");
  }

  uploadClickHandler(e) {
    if (this.active == false) {
      this.dropzoneArea.animate({
        height: "180px" 
      }, 300)
      let timer = setTimeout(() => {
        this.dropzoneArea.addClass("dropzone-area--open dropzone");
        this.active = true;
        clearTimeout(timer);
      }, 300)
    } else {
      this.dropzoneArea.animate({
        height: "0" 
      }, 300)
      this.dropzoneArea.removeClass("dropzone-area--open dropzone");
      this.active = false;
    }
  }

  successHandler(data) {
    this.dataDzRemove(data);
    this.insertAtCaret("body", data);
  }

  dataDzRemove(image) {
    if (image != "") {
      this.dropzoneArea.find('a[data-dz-remove=""]').attr('data-dz-remove', image);
      this.dropzoneArea.find(".dz-remove").on("click", this.remove.bind(this));
    } else {
      this.dropzoneArea.find('a[data-dz-remove=""]').text("");
    }
  }

  insertAtCaret(areaId, text) {
    var txtarea = document.getElementById(areaId);
    if (!txtarea) {
      return;
    }
  
    var scrollPos = txtarea.scrollTop;
    var strPos = 0;
    var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
      "ff" : (document.selection ? "ie" : false));
    if (br == "ie") {
      txtarea.focus();
      var range = document.selection.createRange();
      range.moveStart('character', -txtarea.value.length);
      strPos = range.text.length;
    } else if (br == "ff") {
      strPos = txtarea.selectionStart;
    }
  
    var front = (txtarea.value).substring(0, strPos);
    var back = (txtarea.value).substring(strPos, txtarea.value.length);
    txtarea.value = front + text + back;
    strPos = strPos + text.length;
    if (br == "ie") {
      txtarea.focus();
      var ieRange = document.selection.createRange();
      ieRange.moveStart('character', -txtarea.value.length);
      ieRange.moveStart('character', strPos);
      ieRange.moveEnd('character', 0);
      ieRange.select();
    } else if (br == "ff") {
      txtarea.selectionStart = strPos;
      txtarea.selectionEnd = strPos;
      txtarea.focus();
    }
  
    txtarea.scrollTop = scrollPos;
  }

}

export default UploadImage;