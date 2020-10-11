import $ from 'jquery';
import FormValidate from './modules/FormValidateRules';
import LikedPosts from './modules/LikedPosts';
import urlParser from 'js-video-url-parser/lib/base';
import 'js-video-url-parser/lib/provider/vimeo';
import 'js-video-url-parser/lib/provider/youtube';

$(() => {

  new LikedPosts();

  const admin = $(".adminContentJS");

  /*
   -- Delete Modal ------------------------------------------------------- */
  
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
          You can not delete the user which has posts,<br>
          unless you delete those posts first!</p>`;
      
        } else if (data.table == "categories") {
        title = "Delete Category";
        body = `<p>Are you sure you want to delete the category
          <strong class="font-weight-bold">${data.name}</strong>?<br>
          If there are posts under this category you can not delete it,<br>
           unless you delete those posts first!</p>`;
      
      } else if (data.table == "posts") {
        title = "Delete Post";
        body = `<p>Are you sure you want to delete the post <strong class="font-weight-bold">${data.title}</strong>?<br>
        This post will be permanently deleted!</p>`;
      }
    
      currentModal("warning", title, body, data, urlToPost);
    });
  }
  
  /*
   -- User Edit Form ------------------------------------------------------- */

  if ($("#userEditForm").length) {
    
    const validate = new FormValidate($("#userEditForm"));
    validate.settings.fieldSize["username"] = { min: 4, max: 20 };
    validate.settings.fieldSize["password"] = { min: 8, max: 20 };
    validate.settings.fieldSize["about_text"] = { min: 30, max: 255 };
    validate.settings.uniqueVal["username"] = true;
    validate.settings.uniqueVal["email"] = true;
    validate.settings.validateOnSubmit = false;

    validate.form.on("submit change", async e => {
      e.preventDefault();
  
      const username = await validate.username("username");
      const email = await validate.email("email");
      const password = await validate.password("password");
      const confirmPassword = await validate.confirmPassword("confirm_password");
      const aboutText = await validate.aboutText("about_text");
  
      if (e.type == "submit" && !validate.hasError()) {
        validate.form.off("submit");
        validate.form.trigger("submit");
      }
      return false;
    })

    $("#about_image").on("change", e => {
      const input = e.target;
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = (e) => {
          $('#aboutImage').attr('src', e.target.result).removeClass("d-none");
        }
        reader.readAsDataURL(input.files[0]);
      }
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
   -- Edit Post Form ------------------------------------------------------------ */

  if ($("#postEditForm").length) {
    const postForm = $("#postEditForm");

    editPostFormElementsBehavior(postForm);

    const onVideoChange = (e) => new Promise((resolve, reject) => {
      if (e.type == "change" && e.target.id == "video") {
        const targetUrl = $(e.target).val();
        if (targetUrl.trim() == "") {
          $(".preview.preview-video").attr("data-value", "false").html("");
          return;
        }
        const video = urlParser.parse(targetUrl);
        let videoUrl = "";
        let iframe = "";
  
        if (video.provider == 'youtube') {
          videoUrl = `//www.youtube.com/embed/${video.id}`;
          iframe = `<iframe id="previewVideo" class="embed-responsive-item"
                      src="${videoUrl}" frameborder="0" allowfullscreen>
                    </iframe>`;
        } else if (video.provider == 'vimeo') {
          videoUrl = `//player.vimeo.com/video/${video.id}`;
          iframe = `<iframe id="previewVideo" class="embed-responsive-item"
                      src="${videoUrl}" frameborder="0" webkitallowfullscreen
                      mozallowfullscreen' allowfullscreen>
                    </iframe>`;
        }
  
        iframe = `<div class="embed-responsive embed-responsive-16by9">${iframe}</div>`;
        
        if (videoUrl) {
          postForm.find(".preview.preview-video").html(iframe);
          postForm.find(".preview.preview-video").attr("data-value", "true");
          postForm.find(".preview.preview-video").removeClass("d-none d-block");
          postForm.find(".preview.preview-video").hide().fadeIn();
          return resolve(true)
        }

        alert("Sorry, only youtube and vimeo videos are allowed");
      }
      return resolve(false)
    })

    const onImageChange = (e) => new Promise((resolve, reject) => {
      if (e.type == "change" && e.target.id == 'image') {
        console.log("Image change captured!");
        const input = e.target;
        if (input.files && input.files[0]) {
          const reader = new FileReader();
          reader.onload = (e) => {
            postForm.find("#previewImage").attr("src", e.target.result);
          }
          reader.readAsDataURL(input.files[0]);
          postForm.find(".preview.preview-image").removeClass("d-none d-block");
          postForm.find(".preview.preview-image").hide().fadeIn();
          postForm.find(".preview.preview-image").attr("data-value", "true");
          return resolve(true);
        }
      }
      return resolve(false);
    })

    const validate = new FormValidate(postForm);
    validate.settings.fieldSize["title"] = { min: 6, max: 200 };
    validate.settings.fieldSize["meta_desc"] = { min: 20, max: 160 };
    validate.settings.fieldSize["body"] = { min: 500, max: 65000 };
    validate.settings.validateOnSubmit = true;

    validate.form.on("submit change", async e => {
      e.preventDefault();

      const imageChange = await onImageChange(e);
      const videoChange = await onVideoChange(e);

      const title = await validate.title("title");
      const meta_desc = await validate.metaDesc("meta_desc");
      const body = await validate.body("body");
      const image = await validate.image("image");
      const video = await validate.video("video");
      const category = await validate.category("category");

      if (e.type == "submit" && validate.validatedLen() == 5) {
        validate.form.off("submit");
        validate.form.trigger("submit");
      } else {
        validate.errorsSummary();
      }

      return false;
    })

  }

  /*
   -- XHR Post, User, Category Search -------------------------------------------*/

  if ($(".loadContentJS").length) appendSpinnerToMainContent();

  const pagData = {
    pathname: window.location.pathname,
    type: "search",
    value: "",
    access: $(".loadContentJS").data("access"),
    total: 0,
    page: 1,
    params(type, value, access, total) {
      pagData.type = type;
      pagData.value = value;
      pagData.access = access;
      pagData.total = total;
    },
    pageNum(page=false) {
      if (page) pagData.page = page;
      else return pagData.page;
    },
    script() {
      const pathitems = pagData.pathname.split("/");
      const path = `${pathitems[1]}/${pathitems[2]}`;
      switch (path) {
        case 'staff/posts':
              return "xhr_search_post.php";
        case 'staff/users':
        case 'staff/categories':
              return "xhr_search.php";
        default: 
              return false;
      }
    }
  };

  $("#adminSearchForm").on("submit", e => {
    e.preventDefault();

    const form = $(e.target);
    const type = form.attr("data-type");
    const value = form.find("#s").val();
    const access = $(".loadContentJS").data("access");

    if (value == pagData.value) {
      console.log("The same value ajax sf forbidden!");
      return;
    }

    loading(1);
    $.ajax({
      url: server.baseUrl + '/staff/' + pagData.script(),
      type: "POST",
      data: {
        target: access + '_by_' + type,
        data: `type=${type}&value=${value}&access=${access}&pathname=${pagData.pathname}`,
        uid: server.userId
      },
      success: res => {

        loading(0);
        const data = JSON.parse(res);

        loadPostBox(data);
        pagData.params(type, value, access, data[2].total_count);
      },
      error: err => console.log(err)
    })

    return false;
  }) // <-- Admin Search Form

  $(".loadContentJS").on("click", ".click-load", e => {
    e.preventDefault();

    const link = $(e.target);
    const type = link.attr("data-type");
    const value = link.attr("data-value");
    const access = link.attr("data-access");

    if (value == pagData.value) {
      console.log("The same value ajax cl forbidden!");
      return;
    }

    if (type && value && access) {
      loading(1);
      $.ajax({
        url: server.baseUrl + '/staff/' + pagData.script(),
        type: 'POST',
        data: {
          target: access + '_by_' + type,
          data: `type=${type}&value=${value}&access=${access}&pathname=${pagData.pathname}`,
          uid: server.userId
        },
        success: res => {

          loading(0);
          const data = JSON.parse(res);

          loadPostBox(data);
          pagData.params(type, value, access, data[2].total_count);

          console.log('pagData.total', pagData.total);
        },
        error: err => console.log(err)
      })
    }

    return false;
  }) // <-- Click-Load Link

  $(".loadContentJS").on("click", ".page-link", e => {
    e.preventDefault();

    const page = getPageNum($(e.target));
    if (!page) return false;

    loading(1);
    $.ajax({
      url: server.baseUrl + '/staff/' + pagData.script(),
      type: 'POST',
      data: {
        target: pagData.access + '_by_' + pagData.type,
        data: `type=${pagData.type}&value=${pagData.value}&access=${pagData.access}&page=${page}&pathname=${pagData.pathname}`,
        uid: server.userId
      },
      success: res => {
        //let timer = setTimeout(() => {
          loading(0);
          const data = JSON.parse(res);

          loadPostBox(data);
          pagData.pageNum(page);
          $('#item-' + page).addClass("active");
          
          //clearTimeout(timer);
        //}, 1200)
      },
      error: err => console.log(err)
    })
  })

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
          $.when(requestServer()).then(() => {
            formAlert("right", "Your site settings reloaded.");
            jsonBorder("border-success");
          });
          return;
        }

        let data = jsonForm.find("#json").val();
        if (data == loadedData) {
          formAlert("right", "Your site settings data is okay!");
          jsonBorder("border-success");
          return;
        }

        if (isJson(data)) {
          data = JSON.parse(data);
          requestServer(data);
        } else {
          formAlert("error", "Please correct JSON data in form!");
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

function editPostFormElementsBehavior(form) {
  form.find(".form-check-input").on("change", (e) => {
    
    if (e.target.value == "image") {
      form.find("#image").prop("disabled", false);
      form.find("#video").prop("disabled", true);
      form.find(".media-preview").attr("data-format", "image");
    
    } else if (e.target.value == "video") {
      form.find("#image").prop("disabled", true);
      form.find("#video").prop("disabled", false);
      form.find(".media-preview").attr("data-format", "video");
    }
    
    if (form.find(".media-preview").attr("data-format") == "image") {
      form.find(".preview").removeClass("d-none d-block");
      form.find(".preview-video").hide();
      
      if (form.find(".preview-image").attr("data-value") != "false") {
        form.find(".preview-image").fadeIn();
      } else {
        form.find(".preview-image").fadeOut();
      }
    
    } else if (form.find(".media-preview").attr("data-format") == "video") {
      form.find(".preview").removeClass("d-none d-block");
      form.find(".preview-image").hide();
      
      if (form.find(".preview-video").attr("data-value") != "false") {
        form.find(".preview-video").fadeIn();
      } else {
        form.find(".preview-video").fadeOut();
      }
    }
  })
}

function loading(wait=0) {
  if (wait == 1) {
    $(".loadContentJS").hide();
    $(".loading").removeClass("d-none");
  } else if (wait == 0) {
    $(".loading").addClass("d-none");
    $(".loadContentJS").show();
  }
}

function getPageNum(target) {
  let page = false;

  if (target.closest("li").hasClass("active")) {
    console.log('this link is active ' + target.text());
    return false;
  }

  if (target.hasClass("page-link")) {
    if (target.parent().hasClass("active")) {
      return;
    }
  } else if (target.is("span")) {
    target = target.closest(".page-link");
  }

  let url = target.attr("href");

  if (url) {
    const params = url.split("?")[1];
    const paramsArr = params.split("&");
    
    paramsArr.forEach(value => {
      if (value.split("=")[0] == 'page') {
        page = value.split("=")[1];
        $(".pagination li.page-item").removeClass("active");
        $(`#item-${page}`).addClass("active");
        return false;
      }
    });
  }

  return page;
}

function loadPostBox(data) {
  const loadBox = $(".loadContentJS");

  if (data[0] == 'success') {
    loadBox.html("");
    loadBox.html(data[1]);
    loadBox.append(data[2].html);
  
  } else if (data[0] == 'failed') {
    console.log("failed");
  }
}

function currentModal(type, title, body, data=false, urlToPost=false) {
  const modal = $("#dashboardModal");

  modal.on("show.bs.modal", e => {
    modal.find(".modal-title").text(title);
    modal.find(".modal-body").html(body);
    modal.find(".modal-body").addClass(`alert-${type}`);
    modal.find(".modal-title").addClass(`text-${type}`);
    
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
          <h3 class="modal-title alert-heading" id="dashboardModalTitle"></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="padding:.7rem 1rem;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body alert text-center mb-0 py-4"></div>
        <div class="modal-footer">
          <button id="modalOkBtn" type="button" class="btn btn-primary my-3 d-none" data-dismiss="modal">Ok</button>
          <button id="modalCancelBtn" type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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

function appendSpinnerToMainContent() {
  const spinner = `<div class="loading d-none">
    <div class="spinner-border" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>`;
  $(".main .main-content").append(spinner);
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
    $(".form-alert").html("");  
    return;
  }

  if (!title) {
    title = (type == "right") ? "correct format" : "wrong format";
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

  $("#json").addClass(currClass);
}