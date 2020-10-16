import $ from 'jquery';
import FormValidate from './modules/FormValidateRules';
import { reject } from 'core-js/fn/promise';

$(() => {

  /*
   -- Delete Modal ------------------------------------------------------- */

  const admin = $(".adminContentJS");

  if ($("table a[data-delete]").length || $("a.btn.btn-danger").length) {
    appendModalToBody();
  
    admin.find("table a[data-delete], a.btn.btn-danger").on("click", e => {
      e.preventDefault();
    
      const link = $(e.target);
      const data = parseCsvDash(link.data("delete"));
      const urlToPost = link.attr("href").split("?")[0];
      let title = "";
      let body = "";
    
      if (data.table == "users") {
        title = "Delete User";
        body = `<p>Are you sure you want to delete the user
          <strong class="font-weight-bold">${data.username}</strong>?<br>
          It is unable to delete the user, which has existing posts.</p>`;
      
        } else if (data.table == "categories") {
        title = "Delete Category";
        body = `<p>Are you sure you want to delete the category
          <strong class="font-weight-bold">${data.name}</strong>?<br>
          It is unable to delete the category which has existing posts.</p>`;
      
      } else if (data.table == "posts") {
        title = "Delete Post";
        body = `<p>Are you sure you want to delete the post<br> <strong class="font-weight-bold">${data.title}</strong> ?<br>
        This post will be permanently deleted!</p>`;
      }
    
      currentModal("danger", title, body, data, urlToPost);
    });
  }

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
      appendModalToBody();

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

function currentModal(type, title, body, data=false, urlToPost=false) {
  const modal = $("#dashboardModal");

  modal.on("show.bs.modal", e => {
    modal.find(".modal-title").text(title);
    modal.find(".modal-body").html(body);
    modal.find(".modal-body").addClass(`alert-${type}`);
    
    if (data !== false && urlToPost !== false) {
      modal.find("form").attr("action", urlToPost);
      modal.find("form input[name='table']").val(data.table);
      modal.find("form button[name='delete']").val(data.id);
    
    } else {
      modal.find("form").addClass("d-none");
      modal.find("#modalCancelBtn").addClass("d-none");
      modal.find("#modalOkBtn").removeClass("d-none");
    }
  })

  modal.modal();
}

function appendModalToBody() {
  const modal = `<div class="modal fade" id="dashboardModal" tabindex="-1" role="dialog" aria-labelledby="dashboardModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="dashboardModalTitle"></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="padding:.7rem 1rem;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body alert text-center mb-0 py-4 border-0"></div>
        <div class="modal-footer">
          <button id="modalOkBtn" type="button" class="btn btn-primary my-3 d-none" data-dismiss="modal">Ok</button>
          <button id="modalCancelBtn" type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
          <form action="" method="post" class="my-3">
            <input type="hidden" name="table" value="">
            <button class="btn btn-danger btn-md delete" name="delete" value="">Delete</button>
          </form>
        </div>
      </div>
    </div>
  </div>`;

  $("body").append(modal);
}

function parseCsvDash(str) {
  const arr = str.split(",");
  const obj = {};
  arr.forEach(elem => {
    const prop = elem.split("-")[0];
    const value = elem.split("-")[1];
    return obj[prop] = value;
  })
  return obj;
}

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
