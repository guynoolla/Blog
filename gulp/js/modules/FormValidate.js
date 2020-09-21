import { forEach } from 'core-js/fn/array';
import $ from 'jquery';
var validator = require("email-validator");

class FormValidate {

  constructor(formId) {
    this.form = $(`#${formId}`);
    
    this.settings = {
      textSize: {}
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
      if (key == 27 || key.toLowerCase() == "escape") {
        this.form.find(".response.response--shade")
          .removeClass("response--show-okey")
          .removeClass("response--show-fail")
      }
    })
  }

  checkField(fid, elem) {
    const type = elem[0].type;
    const value = elem.val().trim();

    if (value != this.prevValue[fid]) {
      clearTimeout(this.typingTimer);

      this.typingTimer = setTimeout(() => {
        switch (type) {
          case "email":     this.email(fid);
                            break;
          case "textarea":  this.text(fid);
                            break;
        }
      }, 600)
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

  getTextSize(fid) {
    const size = { min: 4, max: 100 }
    size.min = this.settings.textSize[fid].min || size.min;
    size.max = this.settings.textSize[fid].max || size.max;
    return size;
  }

  goOn(fid) {
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

  email(fid) {
    return new Promise(resolve => {
      if (!this.goOn(fid)) return resolve(false)
  
      const email = this.form.find(`#${fid}`)
      this.errors[fid] = [];
  
      if (email.val().length == 0) {
        this.errors[fid].push("Email cannot be blank.")
      } else if (!validator.validate(email.val())) {
        this.errors[fid].push("Email is incorrect.")
      } else {
        this.showValid(fid, email)
        return resolve(email.val())
      }
  
      this.showErrors(fid, email)
      return resolve(false)
    })
  }

  text(fid) {
    return new Promise(resolve => {
      if (!this.goOn(fid)) return resolve(false);

      const text = this.form.find(`#${fid}`)
      this.errors[fid] = [];

      const size = this.getTextSize(fid)

      if (text.val().length == 0) {
        this.errors[fid].push("Message cannot be blank.")
      } else if (text.val().length < size.min) {
        this.errors[fid].push("Message is too short.")
      } else if (text.val().length > size.max) {
        this.errors[fid].push("Message is too long.")
      } else {
        this.showValid(fid, text)
        return resolve(text.val())
      }

      this.showErrors(fid, text)
      return resolve(false)
    })
  }

  // password(fid) {
  //   this.errors[fid] = [];
  //   pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/;
  //   const pass = this.form.find(`#${fid}`);
  //   const size = this.getTextSize(fid);

  //   if (pass.val().length == 0) {
  //     this.errors[fid].push("Password cannot be blank.");
  //   } else if (pass.val().length < size.min) {
  //     this.errors[fid].push(`Password must contain at least ${size.min} characters.`);
  //   } else if (pass.val().length > size.max) {
  //     this.errors[fid].push(`Password cannot contain more than ${size.max} characters.`);
  //   } else if (!pattern.test(pass.val())) {
  //     this.errors[fid].push(`Password must be at least 8 characters long and contain at least 1 number 1 lowercase and 1 uppercase letter.`);
  //   } else {
  //     this.showValid(fid, pass);
  //     return pass.val();
  //   }

  //   this.showErrors(fid, pass);
  // }

}

export default FormValidate;