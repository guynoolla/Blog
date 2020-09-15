import $ from 'jquery';
var validator = require("email-validator");

class FormValidate {

  constructor(formId) {
    this.form = $(`#${formId}`);
    this.errors = [];
  }

  email(id) {
    const email = this.form.find(`#${id}`);
    if (email.val().length == 0) {
      this.errors.push("Email cannot be blank.");
      return false;
    }
    if (validator.validate(email.val())) {
      return email.val();
    } else {
      this.errors.push("Email is incorrect.");
    }
    return false;
  }

  length(id, min = 10, max = 1000) {
    const text = this.form.find(`#${id}`);
    if (text.val().length == 0) {
      this.errors.push("Message cannot be blank.");
      return false;
    }
    if (text.val().length < min) {
      this.errors.push("Message is too short.");
    } else if (text.val().length > max) {
      this.errors.push("Message is too long.");
    } else {
      return text.val();
    }
    return false;
  }

}

export default FormValidate;