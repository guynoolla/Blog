import { forEach } from 'core-js/fn/array';
import $ from 'jquery';
var validator = require("email-validator");

class FormValidate {

  constructor(formId) {
    this.form = $(`#${formId}`);
    this.errors = {};
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
    this.textSize = {
      min: 50,
      max: 1000
    };
    this.typingTimer;
    this.prevValue = {};
    this.keyupEvent = {};

    this.event();
  }

  event() {
    $(document).on("click", (e) => {
      if (!($(e.target).hasClass("form-control"))) {
        $("form").find(".form-control").removeClass("alert-error").next().text("");
        $("form").find(".form-control").removeClass("alert-valid");
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
          case "email":
                  this.email(fid);
                  break;
          case "textarea":
                  this.text(fid);
                  break;
        }
      }, 600)
    }
    this.prevValue[fid] = value;
  }

  onElementKeyup(fid, elem) {
    if (typeof this.keyupEvent[fid] == "undefined") {
      elem.on("keyup", () => {
        this.checkField(fid, elem);
      })
      this.keyupEvent[fid] = true;
    }
  }

  showErrors(fid, elem) {
    elem.removeClass("alert-error").next().text("");
    elem.removeClass("alert-valid");

    let errorsStr = "";
    this.errors[fid].forEach(value => {
      errorsStr += `${value} `;
    });
    elem.addClass("alert-error").next().text(errorsStr);
    this.onElementKeyup(fid, elem);
  }

  showValid(fid, elem) {
    this.errors[fid] = [];
    elem.removeClass("alert-error");
    elem.addClass("alert-valid");
    elem.next().text("");
  }

  email(fid) {
    const email = this.form.find(`#${fid}`);
    this.errors[fid] = [];

    if (email.val().length == 0) {
      this.errors[fid].push("Email cannot be blank.");
    } else if (!validator.validate(email.val())) {
      this.errors[fid].push("Email is incorrect.");
    } else {
      this.showValid(fid, email)
      return email.val()
    }

    this.showErrors(fid, email);
  }

  text(fid) {
    const text = this.form.find(`#${fid}`);
    this.errors[fid] = [];

    if (text.val().length == 0) {
      this.errors[fid].push("Message cannot be blank.");
    } else if (text.val().length < this.textSize.min) {
      this.errors[fid].push("Message is too short.");
    } else if (text.val().length > this.textSize.max) {
      this.errors[fid].push("Message is too long.");
    } else {
      this.showValid(fid, text)
      return text.val()
    }

    this.showErrors(fid, text);
  }

}

export default FormValidate;