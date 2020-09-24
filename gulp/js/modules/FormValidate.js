import { forEach } from 'core-js/fn/array';
import $ from 'jquery';

class FormValidate {

  constructor(formId) {
    this.form = $(`#${formId}`);
    
    this.settings = {
      fieldSize: {},
      uniqueVal: {}
    };

    this.errors = {};
    this.prevValue = {};
    this.validValues = {};
    this.elements = [];
    this.typingTimer;
    this.event;

    this.getValidated = () => {
      const keys = Object.keys(this.validValues);
      let values = {};
      keys.forEach(key => {
        values[key] = this.validValues[key].val()
      })
      return values;
    }

    this.hasError = (fid = false) => {
      if (fid == false) {
        let has = false;
        const keys = Object.keys(this.errors);
        keys.forEach(key => {
          if (this.errors[key].length > 0) has = true;
        })
        return has;
      } else {
        if (typeof this.errors[fid] !== "undefined") {
          return this.errors[fid].length > 0;
        }
      }
    };

    this.validatedLen = () => {
      if (!this.hasError()) {
        return Object.keys(this.validValues).length;
      }
      return false;
    }

    this.responseMessage = (data, success) => {
      for (let key in this.validValues) {
        this.validValues[key].val("").removeClass("alert-valid");
      }
      if (success) {
        this.form.find(".response").text(data['alert']).addClass("response--show-okey")
        this.captchaReset(data["image_src"]);
      } else {
        this.form.find(".response").text(data['alert']).addClass("response--show-fail");
        this.captchaReset(data["image_src"]);
      }
    }

    this.events()
  }

  events() {
    this.form.on("submit change keyup", event => this.event = event)
    this.form.find(".response.response--shade").on("click", e => {
        $(e.target)
          .removeClass("response--show-okey")
          .removeClass("response--show-fail")
    })
    $(document).on("keyup", e => {
      let key;
      if (e.key !== undefined) key = e.key
      else if (e.keyIdentifier !== undefined) key = e.keyIdentifier;
      else if (e.keyCode !== undefined) key = e.keyCode;
      if (key !== undefined) {
        if (key == 27 || key.toLowerCase() == "escape") {
          this.form.find(".response.response--shade")
            .removeClass("response--show-okey")
            .removeClass("response--show-fail")
        }
      }
    })
  }

  checkField(fid, elem) {
    const value = elem.val().trim();

    if (value != this.prevValue[fid]) {
      clearTimeout(this.typingTimer);
      this.typingTimer = setTimeout(() => {
        const func = this.fieldNameToCamelCase(fid);
        this[func](fid);
      }, 800)
    }
    this.prevValue[fid] = value;
  }

  onElementKeyup(fid, elem) {
    elem.on("keyup", () => this.checkField(fid, elem))
  }

  getErrors(fid) {
    let errors = "";
    this.errors[fid].forEach(value => errors += `${value} `)
    return errors;
  }

  captchaReset(imageSrc) {
    this.form.find("#captcha").val("");
    this.form.find(".form-group-captcha img").attr("src", imageSrc);
    this.form.find(".form-group-captcha .captcha-success").remove();    
  }

  captchaError(fid, elem, imageSrc) {
    console.log("error here!");
    elem.val("");
    const error = this.getErrors(fid)
    $(".form-group-captcha").next().text(error);
    $(".form-group-captcha img").attr("src", imageSrc);
    $(".form-group-captcha .captcha-success").remove();
  }

  captchaValid(fid, elem) {
    console.log("valid here!")
    this.validValues[fid] = elem;
    $(".form-group-captcha").next().text("");
    $(".form-group-captcha")
      .append('<span class="captcha-success">&#10003;</span>');
  }

  showErrors(fid, elem) {
    const errors = this.getErrors(fid);
    elem.removeClass("alert-error").next().text("");
    elem.addClass("alert-error").next().text(errors);
    elem.removeClass("alert-valid");
    this.onElementKeyup(fid, elem);
  }

  showValid(fid, elem) {
    elem.removeClass("alert-error");
    elem.addClass("alert-valid");
    elem.next().text("");
    this.validValues[fid] = elem;
    this.onElementKeyup(fid, elem);
  }

  getFieldSize(fid) {
    const size = { min: 4, max: 100 }
    size.min = this.settings.fieldSize[fid].min || size.min;
    size.max = this.settings.fieldSize[fid].max || size.max;
    return size;
  }

  run(fid) {
    if (this.event.type == "keyup") {
      if (typeof this.validValues[fid] != "undefined") {
        return true
      }
    }
    if (this.hasError(fid)) {
      return true
    }
    if (this.event.type == "change") {
      if (this.event.target.id == fid) {
        return true
      } else {
        return false
      }
    }
  }

  fieldNameToCamelCase(str) {
    if (str.indexOf("_") > -1) {
      let arr = str.split('_')
      arr = arr.map((value, idx) => {
        if (idx > 0) {
          return value.charAt(0).toUpperCase() + value.slice(1);
        } else {
          return value;
        }
      })
      str = arr.join("")
    }
    return str;
  }

}

export default FormValidate;