import { reject, resolve } from 'core-js/fn/promise';
import $ from 'jquery'
import FormValidate from './FormValidate'
var validator = require("email-validator");

class FormValidateRules extends FormValidate {

  async isUnique(value, fid, table) {
    return new Promise((resolve, reject) => {
      $.ajax({
        url: server.baseUrl + '/ajax.php',
        type: 'POST',
        data: {
          value: value,
          field: fid,
          table: table,
          target: 'is_already_exist'
        },
        success: res => {
          if (res == "true") {
            return resolve(value);
          
          } else if (res == "false") {
            if (fid == "username") {
              this.errors[fid].push("This username is not available.");
            } else if (fid == "email") {
              this.errors[fid].push("This email already exists.");
            }
            return resolve(false);
          
          } else {
            return reject(new Error(res));
          }
        },
        error: error => reject(error)
      })
    })
  }

  async message(fid) {
    if (!this.run(fid)) return false;

    const mess = this.form.find(`#${fid}`)
    this.errors[fid] = [];

    const check = () => new Promise(resolve => {
      const size = this.getFieldSize(fid)

      if (mess.val().length == 0) {
        this.errors[fid].push("Message cannot be blank.")
      } else if (mess.val().length < size.min) {
        this.errors[fid].push(`Message must contain at least ${size.min} characters.`)
      } else if (mess.val().length > size.max) {
        this.errors[fid].push(`Username cannot contain more than ${size.max}. characters.`)
      } else {
        return resolve(mess.val())
      }
      return resolve(false)
    })

    let value = await check();

    if (value == false) this.showErrors(fid, mess)
    else this.showValid(fid, mess)

    return value;
  }

  async email(fid) {
    if (!this.run(fid)) return false;

    const email = this.form.find(`#${fid}`)
    this.errors[fid] = [];

    const check = () => new Promise(resolve => {
      if (email.val().length == 0) {
        this.errors[fid].push("Email cannot be blank.")
      } else if (!validator.validate(email.val())) {
        this.errors[fid].push("Email is incorrect.")
      } else {
        return resolve(email.val())
      }
      return resolve(false)
    })

    let value = await check();

    if (value && typeof this.settings.uniqueVal[fid] != "undefined") {
      value = await this.isUnique(value, fid, 'users');
    }

    if (value == false) this.showErrors(fid, email);
    else this.showValid(fid, email);
    
    return value;
  }

  async username(fid) {
    if (!this.run(fid)) return false;

    const username = this.form.find(`#${fid}`)
    this.errors[fid] = [];

    const check = async () => new Promise(resolve => {
      const pattern = /^[a-z0-9]+$/i
      const size = this.getFieldSize(fid)

      if (username.val().length == 0) {
        this.errors[fid].push("Username cannot be blank.")
      } else if (username.val().length < size.min) {
        this.errors[fid].push(`Username must contain at least ${size.min} characters.`)
      } else if (username.val().length > size.max) {
        this.errors[fid].push(`Username cannot contain more than ${size.max}. characters.`)
      } else if (!pattern.test(username.val())) {
        this.errors[fid].push("Username must contain only letters and numbers.")
      } else {
        return resolve(username.val())
      }
      return resolve(false);
    });

    let value = await check();

    if (value && typeof this.settings.uniqueVal[fid] != "undefined") {
      value = await this.isUnique(value, fid, 'users');
    }

    if (value == false) this.showErrors(fid, username);
    else this.showValid(fid, username);
  
    return value;
  }

  async password(fid) {
    if (!this.run(fid)) return false;

    const pass = $(`#${fid}`)
    this.errors[fid] = []

    const check = () => new Promise(resolve => {
      const pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/
      const size = this.getFieldSize(fid)

      if (pass.val().length == 0) {
        this.errors[fid].push("Password cannot be blank.")
      } else if (pass.val().length < size.min) {
        this.errors[fid].push(`Password must contain at least ${size.min} characters.`)
      } else if (pass.val().length > size.max) {
        this.errors[fid].push(`Password cannot contain more than ${size.max} characters.`)
      } else if (!pattern.test(pass.val())) {
        this.errors[fid].push(`Password must be at least 8 characters long and contain at least 1 number 1 lowercase and 1 uppercase letter.`)
      } else {
        return resolve(pass.val())
      }
      return resolve(false)
    });

    let value = await check();

    if (value == false) this.showErrors(fid, pass)
    else this.showValid(fid, pass)

    return value;
  }

  async confirmPassword(fid) {
    if (!this.run(fid)) return false;

    const pass = $(`#${fid}`)
    this.errors[fid] = []

    const check = () => new Promise(resolve => {
      if (!(pass.val() == this.validValues.password.val())) {
        this.errors[fid].push("Confirm password must match password.")
      } else {
        return resolve(pass.val())
      }
      return resolve(false)
    })

    let value = await check();

    if (value == false) this.showErrors(fid, pass)
    else this.showValid(fid, pass)

    return value;
  }

  async captcha(fid) {
    if (!this.run(fid)) return false;

    const captcha = $(`#${fid}`)
    this.errors[fid] = []

    const check = () => new Promise((resolve, reject) => {
      if (captcha.val().trim().length == 0) {
        this.errors[fid].push("Please enter captcha code.");
      } else if (captcha.val().trim().length > 5) {
        this.errors[fid].push("Please complete captcha code.");
      } else {
        return resolve(captcha.val());
      }
      return resolve(false);
    })

    const checkCode = () => new Promise((resolve, reject) => {
      $.ajax({
        url: server.baseUrl + '/ajax.php',
        type: 'POST',
        data: {
          captcha: value,
          target: 'validate_captcha'
        },
        success: res => {
          const data = JSON.parse(res);

          if (data[0] == "true") {
            this.captchaValid(fid, captcha);
            return resolve(true);
          
          } else if (data[0] == "false") {
            this.errors[fid].push("Captcha validation failed, try again.");
            this.captchaError(fid, captcha, data[1]);
            return resolve(false);
          
          } else {
            return new Error(res)
          }
        },
        error: error => reject(error)
      })
    })

    let value = await check();
    if (value) value = await checkCode();

    return value;
  }

  async aboutText(fid) {
    if (!this.run(fid)) return false;

    const about = this.form.find(`#${fid}`)
    this.errors[fid] = [];

    const check = () => new Promise(resolve => {
      const size = this.getFieldSize(fid)

      if (about.val().length != 0) {
        if (about.val().length < size.min) {
          this.errors[fid].push(`About text must contain at least ${size.min} characters.`)
        } else if (about.val().length > size.max) {
          this.errors[fid].push(`About text cannot contain more than ${size.max}. characters.`)
        } else {
          return resolve(about.val())
        }
        return resolve(false)

      } else {
        this.showErrors(fid, about);
      }
    })

    let value = await check();

    if (value == false) this.showErrors(fid, about)
    else this.showValid(fid, about)

    return value;
  }

  async title(fid) {
    if (!this.run(fid)) return false;
    
    return this.fieldLength(fid);
  }

  async metaDesc(fid) {
    if (!this.run(fid)) return false;
    
    return this.fieldLength(fid);
  }

  async body(fid) {
    if (!this.run(fid)) return false;

    return this.fieldLength(fid);
  }

  async image(fid) {
    if (!this.run(fid)) return false;
    
    const elem = $(`#${fid}`);
    this.errors[fid] = [];

    const value = await this.checkFormatInputs(fid);

    if (value == false) {
      this.errors[fid].push("Image is not uploaded.");
      this.showErrors(fid, elem);
      return false;
    } else if (value == true) {
      this.showValid(fid, elem);
      return true;
    } else if (value == -1) {
      return true;
    }
  }

  async video(fid) {
    if (!this.run(fid)) return false;

    const elem = $(`#${fid}`);
    this.errors[fid] = [];

    const value = await this.checkFormatInputs(fid);

    if (value == false) {
      this.errors[fid].push("Video URL is not set.");
      this.showErrors(fid, elem);
      return false;
    } else if (value == true) {
      this.showValid(fid, elem);
      return true;
    } else if (value == -1) {
      return true;
    }
  }

  async category(fid) {
    if (!this.run(fid)) return false;

    const elem = this.form.find(`#${fid}`)
    this.errors[fid] = [];

    const check = () => new Promise(resolve => {
      const selected = $("#category").children("option:selected").val();
      
      if (selected != 0) return resolve(true);
      else return resolve(false);
    })

    const value = await check();
    
    if (value == false) {
      this.errors[fid].push("The post category is not selected.");
      this.showErrors(fid, elem);
      return false;
    } else {
      this.showValid(fid, elem);
      return true;
    }
  }

  async checkFormatInputs(fid) {
    if (!this.run(fid)) return false;

    const elem = this.form.find(`#${fid}`)
    this.errors[fid] = [];

    const check = () => new Promise(resolve => {
      const checked = $('input[name="post[format]"]:checked').val()
      if (checked == fid) {
        if ($(`.preview.preview-${fid}`).attr("data-value") == "true") {
          console.log("value is true");
          return resolve(true);
        } else {
          console.log("value is false");
          return resolve(false);
        }
      }
      return resolve(-1);
    })

    return await check();
  }

  async name(fid) {
    if (!this.run(fid)) return false;
    
    return this.fieldLength(fid);    
  }

  async description(fid) {
    if (!this.run(fid)) return false;
    
    if (this.form.find(`#${fid}`).val().trim() != 0) {
      return this.fieldLength(fid);
    } else {
      return true;
    }
  }

  async fieldLength(fid) {
    const text = this.form.find(`#${fid}`)
    this.errors[fid] = [];

    const check = () => new Promise(resolve => {
      const size = this.getFieldSize(fid)
      const field = fid == 'body' ? 'content' : fid;

      if (text.val().length == 0) {
        this.errors[fid].push(`The ${field} cannot be blank.`)
      } else if (text.val().length < size.min) {
        this.errors[fid].push(`The ${field} must contain at least ${size.min} characters.`)
      } else if (text.val().length > size.max) {
        this.errors[fid].push(`The ${field} cannot contain more than ${size.max}. characters.`)
      } else {
        return resolve(text.val())
      }
      return resolve(false)
    })

    const value = await check();

    if (value == false) this.showErrors(fid, text)
    else this.showValid(fid, text)

    return value;
  }

}

export default FormValidateRules