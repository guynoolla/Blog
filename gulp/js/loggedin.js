import $ from 'jquery';
import LikedPosts from './modules/LikedPosts';
import FormValidate from './modules/FormValidateRules';
import urlParser from 'js-video-url-parser/lib/base';
import 'js-video-url-parser/lib/provider/vimeo';
import 'js-video-url-parser/lib/provider/youtube';
import { reject } from 'core-js/fn/promise';
import Breakpoint from 'bootstrap-breakpoints';
import PostStatus from './modules/PostStatus';
import Cookies from 'js-cookie';

$(() => {

  new LikedPosts();
  const postStatus = new PostStatus();

  /*
   -- Toggle Admin Sidebar -------------------------------------------- */

   Breakpoint.init();

   adminPanelToggle("cookie", Breakpoint.current());
 
   $(".page-admin .sidebar .double-arrow-btn").on("click", e => {
     e.preventDefault();
     adminPanelToggle("click", Breakpoint.current());
   })
 
   $(window).on('change:breakpoint', function (e, current, previous) {
     window.location.reload();
   });

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
      path(filename = true) {
        const pathitems = pagData.pathname.split("/");
        const last = pathitems.length - 1;
        if (!filename) {
          return `${pathitems[last-2]}/${pathitems[last-1]}`;
        } else {
          return `${pathitems[last-2]}/${pathitems[last-1]}/${pathitems[last]}`;        
        }
      },
      script() {
        switch (pagData.path(false)) {
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
       type: "GET",
       data: {
         target: access + '_by_' + type,
         data: `type=${type}&value=${value}&access=${access}&pathname=${pagData.path()}`,
         uid: server.userId
       },
       success: res => {
 
         loading(0);
         const data = JSON.parse(res);
 
         loadPostBox(data);
         pagData.params(type, value, access, data[2].total_count);

         postStatus.actionsClickHandler();
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
          type: 'GET',
          data: {
            target: access + '_by_' + type,
            data: `type=${type}&value=${value}&access=${access}&pathname=${pagData.path()}`,
            uid: server.userId
          },
          success: res => {

            loading(0);
            const data = JSON.parse(res);

            loadPostBox(data);
            pagData.params(type, value, access, data[2].total_count);

            postStatus.actionsClickHandler();
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
       type: 'GET',
       data: {
         target: pagData.access + '_by_' + pagData.type,
         data: `type=${pagData.type}&value=${pagData.value}&access=${pagData.access}&page=${page}&pathname=${pagData.path()}`,
         uid: server.userId
       },
       success: res => {
         //let timer = setTimeout(() => {
           loading(0);
           const data = JSON.parse(res);
 
           loadPostBox(data);
           pagData.pageNum(page);
           $('#item-' + page).addClass("active");

           postStatus.actionsClickHandler();
           
           //clearTimeout(timer);
         //}, 1200)
       },
       error: err => console.log(err)
     })
   })

})

/*
 -- Functions ---------------------------------------------------- */

function appendSpinnerToMainContent() {
  const spinner = `<div class="loading d-none">
    <div class="spinner-border" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>`;
  $(".main .main-content").append(spinner);
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

function loading(wait=0) {
  if (wait == 1) {
    $(".loadContentJS").hide();
    $(".loading").removeClass("d-none");
  } else if (wait == 0) {
    $(".loading").addClass("d-none");
    $(".loadContentJS").show();
  }
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

async function adminPanelToggle(type, current) {
  
  if (current == "sm" || current == "xs") {
    return false;
  }

  let collapse = false;

  if (type == "click") {
    collapse = ($(".page-admin").hasClass("page-admin--lg"));
  } else if (type == "cookie") {
    collapse = (typeof Cookies.get("lk_table_wide") !== "undefined");
  }

  const admin = $(".page-admin");
  const bar = admin.find(".sidebar");
  const main = admin.find(".main");
  const arrowBtn = bar.find(".nav-item.logo .double-arrow-btn");

  const toggle = () => new Promise(resolve => {
    if (collapse) {
      main.removeClass("col-lg-9")
          .find(".main-content").addClass("collapse-mp-x");
      bar.removeClass("col-lg-3").addClass("sidebar--collapse")
         .find(".logo.logo--flex").css("justify-content", "center")
         .off("mouseenter mouseleave hover");
      arrowBtn.attr("style", "width:3rem !important;")
              .off("mouseenter mouseleave");
      admin.removeClass("page-admin--lg");
      admin.find("#adminSearchForm").addClass("px-5");
      
      return resolve(true);
      
    } else if (!collapse) {
      main.addClass("col-lg-9")
          .find(".main-content").removeClass("collapse-mp-x");
      bar.removeClass("sidebar--collapse").addClass("col-lg-3")
         .find(".nav-item.logo .nav-link").css("display", "block")
         .on("mouseenter mouseleave hover");
      arrowBtn.attr("style", "width: 2.7rem !important;")
              .on("mouseenter mouseleave");
      admin.addClass("page-admin--lg");
      admin.find("#adminSearchForm").removeClass("px-5");

      return resolve(true);
    }
  });

  admin.css("opacity", "0");
  const done = await toggle();
  admin.animate({ opacity: 1 }, 600);
  
  if (collapse) {
    bar.find(".double-arrow-btn").css("transform", "rotate(-180deg)");
    Cookies.set("lk_table_wide", "1");
  } else {
    Cookies.remove("lk_table_wide");
  }

}