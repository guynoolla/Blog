import $ from 'jquery';
import FormValidate from './modules/FormValidateRules';
import { reject } from 'core-js/fn/promise';

$(() => {

  /*
   -- Category Edit Form --------------------------------------------------------*/ 

  if ($("#categoryEditForm").length) {
    
    const validate = new FormValidate($("#categoryEditForm"));
    validate.settings.fieldSize["name"] = { min: 0, max: 50 };
    validate.settings.fieldSize["description"] = { min: 0, max: 255 };
    validate.settings.validateOnSubmit = false;

    validate.form.on("submit change", async e => {
      e.preventDefault();

      const name = await validate.name("name");
      const description = await validate.description("description");

      if (e.type == "submit" && !validate.hasError()) {
        validate.form.off("submit");
        validate.form.trigger("submit");
      }

      return false;
    })
  }

  /*
   -- Edit Json in Form --------------------------------------------- */

    if ($("#jsonEditForm").length) {

      const jsonForm = $("#jsonEditForm");
      let loadedData = "";
      $(window).on("load", requestServer());

      jsonForm.on("click", "button", e => {
        e.preventDefault();

        if (e.target.name == "reload") {
          if (jsonForm.find("#json").val() !== loadedData) {
            $.when(requestServer()).then(() => {
              formAlert();
              jsonBorder("border-success");
            });
          } else {
            jsonBorder("border-success");
            console.log('The same data submission atempt!');
          }
          return;
        }

        let data = jsonForm.find("#json").val();
        if (data == loadedData) {
          jsonBorder("border-success");
          return;
        }

        if (isJson(data)) {
          data = JSON.parse(data);
          requestServer(data);
        } else {
          formAlert("error", "Please correct JSON format or reload data!");
          jsonBorder("border-danger");
          return;
        }

        return false;
      })

      function requestServer(json=false) {
        let data = {};

        if (json == false) {
          data = { target: "user_site_data", json: "false" }
        } else {
          data = { target: "user_site_data", json: JSON.stringify(json) }
        }

        $.ajax({
          url: server.baseUrl + '/ajax.php',
          type: "post",
          data: data
        })
        .done(res => {
          const data = JSON.parse(res);

          if (data[0] == "okey" || data[0] == "done") {
            loadedData = data[1];
            jsonForm.find("#json").val(data[1]);
            if (data[0] == "done") {
              formAlert("right", "Your site settings data is updated!");
              jsonBorder("border-success");
            }
          } else if (data[0] == "error") {
            formAlert("error", data[1], "Error");
            jsonBorder("border-danger");
          }
        })
        .fail(res => console.log('Err', res))
      }
    
    }

}) // <-- jQuery

/*
 * Functions ---------------------------------------------------------*/

function isJson(str) {
  try {
    return JSON.parse(str);
  } catch (e) {
    console.log("Error ->", e);
    return false;
  }
}

function formAlert(type="", errors, title=false) {
  $(".form-alert").removeClass("form-alert--error form-alert--right");
  
  if (type == "") {
    $(".form-alert").slideUp().html("");  
    return;
  }

  if (!title) {
    title = (type == "right") ? "correct" : "wrong";
  }
  
  let arr = [];
  if (typeof errors == "string") arr.push(errors)
  else arr = errors;

  let list = "";
  arr.forEach(value => list += `<li>${value}</li>`);

  $(".form-alert")
    .slideUp()
    .addClass(`form-alert--${type}`)
    .html( `<ul><h4>${title}</h4>${list}</ul>`)
    .slideDown();
}

function jsonBorder(currClass="") {
  $("#json").removeClass("border-success border-danger");
  
  if (currClass == "") return;

  let timer = setTimeout(() => {
    $("#json").addClass(currClass);
    clearTimeout(timer);
  }, 1000)
}
