import { forEach } from 'core-js/fn/array';
import $ from 'jquery';
var validator = require("email-validator");

class FormValidate {

  constructor(formId) {
    this.form = $(`#${formId}`);
    this.errors = {};
    this.hasError = () => {
      const keys = Object.keys(this.errors);
      let has = false;
      keys.forEach(key => {
        if (this.errors[key].length > 0) has = true;
      })
      return has;
    };
    this.textSize = {
      min: 50,
      max: 1000
    };
    this.typingTimer;
    this.prevValue = {};
    this.changeEvent = {};
  }

  onElementChange(fid, elem) {
    if (typeof this.changeEvent[fid] == "undefined") {
      elem.on("keyup", () => {
        const type = elem[0].type;
        const value = elem.val().trim();

        if (value != this.prevValue[fid]) {
          clearTimeout(this.typingTimer);

          this.typingTimer = setTimeout(() => {
            console.log("Started");
            switch(type) {
              case "email":
                      this.email(fid, false);
              case "textarea":
                      this.text(fid, false);
            }
          }, 600)
        }
        this.prevValue[fid] = value;
      })
      this.changeEvent[fid] = true;
    }
  }

  showErrors(fid, elem) {
    elem.removeClass("alert-error").next().text("");
    let errorsStr = "";
    this.errors[fid].forEach(value => {
      errorsStr += `${value} `;
    });
    elem.addClass("alert-error").next().text(errorsStr);
    this.onElementChange(fid, elem);
  }

  showValid(elem) {
    elem.removeClass("alert-error");
    elem.addClass("alert-valid");
    elem.next().text("");
  }

  email(fid, errIndicate = true) {
    const email = this.form.find(`#${fid}`);
    this.errors[fid] = [];

    if (email.val().length == 0) {
      this.errors[fid].push("Email cannot be blank.");
    } else if (!validator.validate(email.val())) {
      this.errors[fid].push("Email is incorrect.");
    } else {
      this.showValid(email)
      return email.val()
    }

    if (errIndicate == true) this.showErrors(fid, email);
  }

  text(fid, errIndicate = true) {
    const text = this.form.find(`#${fid}`);
    this.errors[fid] = [];

    if (text.val().length == 0) {
      this.errors[fid].push("Message cannot be blank.");
    } else if (text.val().length < this.textSize.min) {
      this.errors[fid].push("Message is too short.");
    } else if (text.val().length > this.textSize.max) {
      this.errors[fid].push("Message is too long.");
    } else {
      this.showValid(text)
      return text.val()
    }

    if (errIndicate == true) this.showErrors(fid, text);
  }

}

export default FormValidate;