import $ from 'jquery';
require('core-js');
require('bootstrap');
import animatedScrollTo from 'animated-scroll-to';
import { preventExtensions } from 'core-js/fn/object';
import Breakpoint from 'bootstrap-breakpoints';
import Like from './modules/Like';
import Posts from './modules/Posts';
import FieldValidate from './modules/FieldValidate';
import { forEach } from 'core-js/fn/array';

window.jQuery = $;

$(() => {

  Breakpoint.init();

  slickCarousel();
  navbarSearchBehavior();
  homePagePaginationBehavior();
  editPostFormElementsBehavior();

  const like = new Like();
  const posts = new Posts();

  var rtime;
  var timeout = false;
  var delta = 200;
  var er1 = $(".embed-responsive");
  var er2 = $(".embed-responsive");

  deviceMediaEmbedResponsive(er1, er2);

  $(window).on("resize", function() {
    rtime = new Date();
    if (timeout === false) {
      timeout = true;
      setTimeout(resizeend, delta);
    }
  });
  
  function resizeend() {
    if (new Date() - rtime < delta) {
      setTimeout(resizeend, delta);
    } else {
      timeout = false;
      if (er1.length && er2.length) {
        cleanEmbedResponsive(er1, er2);
        mediaEmbedResponsive(er1, er2);
      } else {
        // code...
      }
    }         
  }

  $("#navSearchForm, #asideSearchForm").on("submit", e => {
    const form = $(e.target)
    const term = form.find("input[name='s']").val()
    if (term.trim() == "") {
      e.preventDefault();
      return false;
    }
  })

  if ($("#contactForm").length) {
    const validate = new FieldValidate("contactForm");
    validate.settings.fieldSize["message"] = { min: 20, max: 1000 };

    validate.form.on("submit change", async e => {
      e.preventDefault();

      const email = await validate.email("email");
      const message = await validate.message("message");
      const captcha = await validate.captcha("captcha");

      if (e.type == "submit" && validate.validatedLen() == 3) {
        
        $(validate.form).find(".spinner-grow").removeClass("d-none");
        const { email, message, captcha } = validate.getValidated();
  
        $.ajax({
          url: server.baseUrl + '/ajax.php',
          type: 'POST',
          data: { 
            email: email,
            message: message,
            captcha: captcha,
            target: 'contact_form'
          },
          success: res => {
            let timer = setTimeout(() => {

              $(validate.form).find(".spinner-grow").addClass("d-none");
              const data = JSON.parse(res);

              if (data[0] == "success") {
                validate.responseMessage(data[1], true)
              } else if (data[0] == "failed") {
                validate.responseMessage(data[1], false)
              }

              clearTimeout(timer);
            }, 2000);
          },
          error: error => console.log("Error -> ", error)
        })
      }

      return false;
    });
  }

  if ($("#registerForm").length) {
    const validate = new FieldValidate("registerForm");
    validate.settings.fieldSize["username"] = { min: 4, max: 20 };
    validate.settings.fieldSize["password"] = { min: 8, max: 20 };
    validate.settings.uniqueVal["username"] = true;
    validate.settings.uniqueVal["email"] = true;

    validate.form.on("submit change", async e => {
      e.preventDefault();

      const username = await validate.username("username");
      const email = await validate.email("email");
      const password = await validate.password("password");
      const confirmPassword = await validate.confirmPassword("confirm_password");
      const captcha = await validate.captcha("captcha");

      if (e.type == "submit" && validate.validatedLen() == 5) {
        validate.form.off("submit");
        validate.form.trigger("submit");
      }

      return false;
    })
  }

  $(window).on("scroll", function() {
    checkScrollPosition();    
  });

  $("#scrollToTopJS").on('click', () => {
    scrollToEventHandler(0);
  });

  $(".siteNavJS li").on("click", (e) => {
    if (e.target.href.indexOf('#') > -1) {
      const splitted = e.target.href.split('#');
      if (splitted.length == 2) {
        const target = '#' + splitted[1];
        scrollToEventHandler(document.querySelector(target));
      }
    }
  })

});

/**
 * Functions
 */

function deviceMediaEmbedResponsive(embedResp1, embedResp2) {
  if (Breakpoint.is("md")) {
    cleanEmbedResponsive(false, embedResp2);
    mediaEmbedResponsive(false, embedResp2);
  } else {
    // Leave default embed-responsive-16by9;
  }
}

function mediaEmbedResponsive(embedResp1=false, embedResp2=false) {
  if (Breakpoint.is("xs")) {
    //console.log("Breakpoint", "xs");
    if (embedResp1) embedResp1.addClass('embed-responsive-16by9');
    if (embedResp2) embedResp2.addClass('embed-responsive-16by9');
  }
  if (Breakpoint.is("sm")) {
    //console.log("Breakpoint", "sm");
    if (embedResp1) embedResp1.addClass('embed-responsive-16by9');
    if (embedResp2) embedResp2.addClass("embed-responsive-16by9");
  }
  if (Breakpoint.is("md")) {
    //console.log("Breakpoint", "md");
    if (embedResp1) embedResp1.addClass('embed-responsive-16by9');
    if (embedResp2) embedResp2.addClass('embed-responsive-4by3');
  }
  if (Breakpoint.is("lg")) {
    //console.log("Breakpoint", "lg");
    if (embedResp1) embedResp1.addClass('embed-responsive-16by9');
    if (embedResp2) embedResp2.addClass('embed-responsive-16by9');
  }  
}

function cleanEmbedResponsive(embedResp1=false, embedResp2=false) {
  if (embedResp1) {
    embedResp1.removeClass('embed-responsive-4by3');
    embedResp1.removeClass('embed-responsive-16by9');
    embedResp1.removeClass('embed-responsive-21by9');
  }
  if (embedResp2) {
    embedResp2.removeClass('embed-responsive-4by3');
    embedResp2.removeClass('embed-responsive-16by9');
    embedResp2.removeClass('embed-responsive-21by9');
  }
}

function editPostFormElementsBehavior() {
  const Form = $("#editPostForm");
  
  Form.find(".form-check-input").on("change", (e) => {
    if (e.target.value == "image") {
      Form.find("#image").prop("disabled", false);
      Form.find("#video").prop("disabled", true);
    } else if (e.target.value == "video") {
      Form.find("#image").prop("disabled", true);
      Form.find("#video").prop("disabled", false);        
    }
  })
}

function homePagePaginationBehavior() {
  const url = window.location.href;

  if (url.indexOf("?") > -1) {
    const urlParts = url.split("?");
    if (urlParts[1].indexOf("&") > -1) {
      // here...
    } else {
      if (urlParts[1].indexOf("=") > -1) {
        const assignment = urlParts[1].split('=')
        if (assignment[0] === 'page') {
          if ($("#homeMain").length) {
            scrollToEventHandler(document.querySelector("#homeMain"));
          }
        }
      }
    }
  }
}

function checkScrollPosition() {
  if ($(window).scrollTop() > 50) {
    $("nav#hideByScroll").addClass("scroll");
  } else {        
    $("nav#hideByScroll").removeClass("scroll");
  }
  if ($(window).scrollTop() < 500) {
    $(".scroll-to-top").css("display", "none");
  } else {
    $(".scroll-to-top").css("display", "block");
  }
}

function scrollToEventHandler(position) {

  animateScrollTo(position).then(hasScrolledToPosition => {
    // scroll animation is finished
   
    // "hasScrolledToPosition" indicates if page/element
    // was scrolled to a desired position
    // or if animation got interrupted
    if (hasScrolledToPosition) {
      // page is scrolled to a desired position
    } else {
      // scroll animation was interrupted by user
      // or by another call of "animateScrollTo"
    }
  });
}

function navbarSearchBehavior() {
  $(window).on("load", () => {
    $(".search-field-lk").addClass("push-in-field");
    $(".search-field-lk").removeClass("hide");

    $(".search-field-lk").on("mouseover", (e) => {
      $(".search-field-lk").removeClass("push-in-field");
    });

    $(document).on("click scroll", (e) => {
      if (!($(e.target).hasClass("search-field-lk"))) {
        $(".search-field-lk").addClass("push-in-field");
      }
    })
  })
}

function slickCarousel() {
  $(window).on("load", () => {
    const carousel = $(".carousel")
    const content = carousel.find(".carousel-content");
    const slider = carousel.find(".slider");

    if (typeof slider.slick == "function") {
      let timer = setInterval(() => {
        let imgLen = slider.find("img").length;

        if (imgLen => 2) {
          slider.slick({
            slidesToShow: 2,
            slidesToScroll: 1,
            autoplay: false,
            autoplaySpeed: 2000,
            nextArrow: $('.next'),
            prevArrow: $('.prev'),
            responsive: [{
              breakpoint: 1201, // 1024,
              settings: {
                slidesToShow: 2,
                slidesToScroll: 1,
                infinite: true,
                dots: false
              }
            },
            {
              breakpoint: 991, // 880,
              settings: {
                slidesToShow: 2,
                slidesToScroll: 1,
                infinite: true,
                dots: false
              }
            },
            {
              breakpoint: 701,
              settings: {
                slidesToShow: 1,
                slidesToScroll: 1
              }
            }]
          })
          clearTimeout(timer)
          content.addClass("carousel-content--fade-in");
          carousel.find(".carousel-spinner").addClass("hide");
        }
      }, 250)
    }
  })
}