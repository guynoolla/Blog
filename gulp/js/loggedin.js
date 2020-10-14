import $ from 'jquery';
import LikedPosts from './modules/LikedPosts';
import FormValidate from './modules/FormValidateRules';
import Cookies from 'js-cookie';
import { reject } from 'core-js/fn/promise';
import Breakpoint from 'bootstrap-breakpoints';

$(() => {

  new LikedPosts();

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

})

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
      main.removeClass("col-lg-9").addClass("collapse-mp-x");
      bar.removeClass("col-lg-3").addClass("sidebar--collapse")
         .find(".logo.logo--flex").css("justify-content", "center")
         .off("mouseenter mouseleave hover");
      arrowBtn.attr("style", "width:3rem !important;")
              .off("mouseenter mouseleave");
      admin.removeClass("page-admin--lg");
      admin.find("#adminSearchForm").addClass("px-5");
      
      return resolve(true);
      
    } else if (!collapse) {
      main.removeClass("collapse-mp-x").addClass("col-lg-9");
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