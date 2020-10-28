import $ from 'jquery';
import Dropzone from '../vendors/dropzone/dist/dropzone.js';

class UploadImage {

  constructor() {
    this.uploadBtn = $(".dropzoneBtnJS");
    this.dropzoneArea = $(".dropzone-area");
    this.active = false;

    this.dropzone();
    this.events();
  }

  dropzone() {
    Dropzone.autoDiscover = false;

    this.dropzone = new Dropzone(
      ".dropzone-area",
      { url: server.baseUrl + '/form_post.php' }
    );

    this.dropzone.on("sending", (file, xhr, formData) => {
      this.uploadBtn.closest("form").off("submit");
      const postId = this.getUrlParameter('id');
      formData.append("pid", postId);
    });

    this.dropzone.on("complete", complete => {
      this.responseHandler(complete);
      this.uploadBtn.closest("form").on("submit");
    });
  }

  events() {
    this.uploadBtn.on("click", this.uploadClickHandler.bind(this));

    this.dropzoneArea.find(".dropzone-area-hint").on("click", e => {
      $(e.target).closest(".dropzone-area").trigger("click");
    })
  }

  uploadClickHandler(e) {
    if (this.active == false) {
      this.dropzoneArea.animate({
        height: "170px" 
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

  responseHandler(data) {
    const res = JSON.parse(data.xhr.response);

    if (res[0] == "success") {
      this.insertAtCaret("body", res[1]);
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

  getUrlParameter(sParam) {
    let sPageURL = window.location.search.substring(1),
    sURLVariables = sPageURL.split('&'),
    sParameterName,
    i;

    for (i = 0; i < sURLVariables.length; i++) {
      sParameterName = sURLVariables[i].split('=');

      if (sParameterName[0] === sParam) {
        return sParameterName[1] === undefined
                ? true
                : decodeURIComponent(sParameterName[1]);
      }
    }
  }

}

export default UploadImage;