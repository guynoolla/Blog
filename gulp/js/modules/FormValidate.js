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

    this.emptyErrors = () => this.errors = {}

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

    this.success = () => {
      return (this.event.type == "submit" && !this.hasError())
    };

    this.responseMessage = (msg, success) => {
      for (let key in this.validValues) {
        this.validValues[key].val("").removeClass("alert-valid");
      }
      if (success) {
        this.form.find(".response").text(msg).addClass("response--show-okey")
      } else {
        this.form.find(".response").text(msg).addClass("response--show-fail")
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

  showErrors(fid, elem) {
    let errors = "";
    this.errors[fid].forEach(value => errors += `${value} `)
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
    if (this.event.type == "submit") {
      return true;
    }
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