import $ from 'jquery';
var validator = require("email-validator");

class FormValidate {

  constructor(formId) {
    this.form = $(`#${formId}`);
    this.errors = {
      email: [],
      message: []
    };
    this.hasError = () => {
      return this.errors.email.length > 0 || 
             this.errors.message.length > 0;
    };
  }

  showErrors(id) {
    let errors = "";
    this.errors[id].forEach(value => {
      errors += `${value} `;
    });
    $(`#${id}`).addClass("alert-error")
      .next().text(errors);
  }

  email(id) {
    const email = this.form.find(`#${id}`);

    if (email.val().length == 0) {
      this.errors.email.push("Email cannot be blank.");
    } else if (!validator.validate(email.val())) {
      this.errors.email.push("Email is incorrect.");
    } else {
      return email.val();
    }

    this.showErrors(id);
  }

  length(id, min = 10, max = 1000) {
    const text = this.form.find(`#${id}`);

    if (text.val().length == 0) {
      this.errors.message.push("Message cannot be blank.");
    } else if (text.val().length < min) {
      this.errors.message.push("Message is too short.");
    } else if (text.val().length > max) {
      this.errors.message.push("Message is too long.");
    } else {
      return text.val();
    }

    this.showErrors(id);
  }

}

export default FormValidate;