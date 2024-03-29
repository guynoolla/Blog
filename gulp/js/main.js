import $ from 'jquery';
require('core-js');
require('bootstrap');
import animatedScrollTo from 'animated-scroll-to';
import { preventExtensions } from 'core-js/fn/object';
import Breakpoint from 'bootstrap-breakpoints';
import Like from './modules/Like';
import FormValidate from './modules/FormValidateRules';
import { find, forEach } from 'core-js/fn/array';

window.jQuery = $;

$(() => {

  Breakpoint.init();

  slickCarousel(server.slider, Breakpoint.current());
  navbarSearchBehavior();
  homePagePaginationBehavior();

  const like = new Like();

  const er1 = $(".embed-responsive");
  const er2 = $(".embed-responsive");

  deviceMediaEmbedResponsive(er1, er2, server.postFontSize);

/*
  -- Change classes on window resize (test) --------------------------------*/

$(window).on('change:breakpoint', function (e, current, previous) {
  console.log('previous breakpoint was', previous);
  console.log('current breakpoint is', current);

  cleanEmbedResponsive(er1, er2);
  mediaEmbedResponsive(er1, er2);

  const fontSize = server.postFontSize;

  if (current == "xs") {
    $(".postContentJS").attr("style", `font-size:${fontSize.xs} !important`);
  } else if (current == "sm") {
    $(".postContentJS").attr("style", `font-size:${fontSize.sm} !important`);
  } else if (current == "md") {
    $(".postContentJS").attr("style", `font-size:${fontSize.md} !important`);
  } else if (current == "lg") {
    $(".postContentJS").attr("style", `font-size:${fontSize.lg} !important`);
  } else if (current == "xl") {
    $(".postContentJS").attr("style", `font-size:${fontSize.xl} !important`);
  }
});

/*
  -- Adapt content height for device screen -------------------------------- */

  let bodyHeight = $(document.body).height();
  const windowHeight = $(window).height();
  
  if (windowHeight > bodyHeight) {
    $("footer").attr("style", "position:fixed; bottom:0");
    $(".firstTopPaddingJS").attr("style", "padding-top: 0;");
    $(".main").attr("style", `height:100vh`);
    
  } else {
    const logoHeight = $("section.logo").height();
    const footerHeight = $("footer").height();

    if ($("section.logo.topBannerHideJS").length) {
      if (windowHeight < (bodyHeight - logoHeight)) {
        $("section.logo.topBannerHideJS")
          .attr("style", "display: none !important;");

        if (windowHeight > (bodyHeight - logoHeight - footerHeight)) {
          $("footer").attr("style", "position:fixed !important; bottom:0");
        }
      } else {
        $(".firstTopPaddingJS").attr("style", "padding-top: 0 !important;");
      }
    }
  }

  /*
    -- To top Button -------------------------------------------------*/

  const halfWidth = $(".containerJS").width() / 2;
  $("#scrollToTopJS").css({
    right: "50%",
    marginRight: -(halfWidth) + 'px'
  })

  /*
   -- Require modules according the user type ---------------------------*/

  if (server.isLoggedIn) require("./loggedin.js");
  if (server.isAuthor) require("./author.js");
  if (server.isAdmin) require("./admin.js");

  /*
   -- Nav Search Form ---------------------------------------------------*/

  $("#navSearchForm, #asideSearchForm").on("submit", e => {
    const form = $(e.target)
    const term = form.find("input[name='s']").val()
    if (term.trim() == "") {
      e.preventDefault();
      return false;
    }
  })

  /*
    -- Contact Form --------------------------------------------------- */

  if ($("#contactForm").length) {
    const validate = new FormValidate($("#contactForm"));
    validate.settings.fieldSize["message"] = { min: 20, max: 1000 };
    validate.settings.validateOnSubmit = true;

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
  } // <-- Contact Form

  /*
    -- Register Form --------------------------------------------------- */

  if ($("#registerForm").length) {
    const validate = new FormValidate($("#registerForm"));
    validate.settings.fieldSize["username"] = { min: 4, max: 20 };
    validate.settings.fieldSize["password"] = { min: 8, max: 20 };
    validate.settings.uniqueVal["username"] = true;
    validate.settings.uniqueVal["email"] = true;
    validate.settings.validateOnSubmit = true;

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
  } // <-- Register Form

  /*
   -- Scroll ---------------------------------------------------------- */

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

  /*
   -- Navbar toggler animation ----------------------------------------- */

  $(".navbar .navbar-toggler")
    .on("mouseenter", e => {
      e.preventDefault();
      $(e.target).addClass("pulse");
      setTimeout(() => {
        $(e.target).removeClass("pulse");
      }, 400)
    })
  
}); // $ jquery

/*
 * Functions ---------------------------------------------------------*/

function deviceMediaEmbedResponsive(embedResp1, embedResp2, fontSize) {
  if (Breakpoint.is("xs")) {
    $(".postContentJS").attr("style", `font-size:${fontSize.xs} !important`);
    // Leave default embed-responsive-16by9;
    
  } else if (Breakpoint.is("sm")) {
    $(".postContentJS").attr("style", `font-size:${fontSize.sm} !important`);    
    // Leave default embed-responsive-16by9;

  } else if (Breakpoint.is("md")) {
    $(".postContentJS").attr("style", `font-size:${fontSize.md} !important`);
    cleanEmbedResponsive(false, embedResp2);
    mediaEmbedResponsive(false, embedResp2);
  
  } else if (Breakpoint.is("lg")) {
    $(".postContentJS").attr("style", `font-size:${fontSize.lg} !important`);
    // Leave default embed-responsive-16by9;
  
  } else if (Breakpoint.is("xl")) {
    $(".postContentJS").attr("style", `font-size:${fontSize.xl} !important`);    
    // Leave default embed-responsive-16by9;
  }
}

function mediaEmbedResponsive(embedResp1=false, embedResp2=false) {
  if (Breakpoint.is("xs")) {
    if (embedResp1) embedResp1.addClass('embed-responsive-16by9');
    if (embedResp2) embedResp2.addClass('embed-responsive-16by9');
  }
  if (Breakpoint.is("sm")) {
    if (embedResp1) embedResp1.addClass('embed-responsive-16by9');
    if (embedResp2) embedResp2.addClass("embed-responsive-16by9");
  }
  if (Breakpoint.is("md")) {
    if (embedResp1) embedResp1.addClass('embed-responsive-16by9');
    if (embedResp2) embedResp2.addClass('embed-responsive-4by3');
  }
  if (Breakpoint.is("lg")) {
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

function slickCarousel(settings, current) {

  const carousel = $(".carousel")
  const content = carousel.find(".carousel-content");
  const slider = carousel.find(".slider");

  if (typeof slider.slick == "function") {
    carousel.find(".carousel-spinner").removeClass("d-none");

    if (settings.centerModeChevronsNone && (current != 'xs')) {
      console.log("Sure!!");
      $(".carousel .slider-nav .slider-btn svg")
         .attr("style", "display: none !important;");
      $(".carousel .slider .slider-post-text").on("mouseenter", (e) => {
        $(".carousel .slider .slick-slide").removeClass("cursor-point");
        if ($(e.target).closest(".slick-slide").hasClass("slick-current")) {
          return false;
        } else {
          $(e.target).closest(".slick-slide").addClass("cursor-point");
        }
      })
    }

    slider.slick({
      autoplay: settings.autoplay,
      autoplaySpeed: settings.autoplaySpeed,
      speed: settings.speed,
      dots: settings.dots,
      infinite: settings.infinite,
      slidesToShow: settings.slidesToShow,
      slidesToScroll: settings.slidesToScroll,
      centerPadding: settings.centerPadding,
      centerMode: settings.centerMode,
      nextArrow: $('.next'),
      prevArrow: $('.prev'),
      cssEase: "linear",
      focusOnSelect: settings.focusOnSelect,
      responsive: [
        {
          breakpoint: 2000,
          settings: {
            dots: settings.less2000w.dots,
            infinite: settings.less2000w.infinite,
            slidesToShow: settings.less2000w.slidesToShow,
            slidesToScroll: settings.less2000w.slidesToScroll,
            centerPadding: settings.less2000w.centerPadding,
            centerMode: settings.less2000w.centerMode,
          }
        },
        {
          breakpoint: 1600,
          settings: {
            dots: settings.less1600w.dots,
            infinite: settings.less1600w.infinite,
            slidesToShow: settings.less1600w.slidesToShow,
            slidesToScroll: settings.less1600w.slidesToScroll,
            centerPadding: settings.less1600w.centerPadding,
            centerMode: settings.less1600w.centerMode,
          }
        },
        {
          breakpoint: 1200,
          settings: {
            dots: settings.less1200w.dots,
            infinite: settings.less1200w.infinite,
            slidesToShow: settings.less1200w.slidesToShow,
            slidesToScroll: settings.less1200w.slidesToScroll,
            centerPadding: settings.less1200w.centerPadding,
            centerMode: settings.less1200w.centerMode
          }
        },
        {
          breakpoint: 992,
          settings: {
            autoplay: settings.less992w.autoplay,
            dots: settings.less992w.dots,
            infinite: settings.less992w.infinite,
            slidesToShow: 1,
            slidesToScroll: 1,
            centerMode: settings.less992w.centerMode,
            centerPadding: settings.less992w.centerPadding,
          }
        },
        {
          breakpoint: 768,
          settings: {
            autoplay: settings.less768w.autoplay,
            dots: settings.less768w.dots,
            infinite: settings.less768w.infinite,
            slidesToShow: 1,
            slidesToScroll: 1
          }
        }
      ]
    })

    let imgLen = slider.find("img").length;
    let started = false;

    let interval = setInterval(() => {
      if (imgLen >= 3) {
        started = true;
        carousel.find(".carousel-spinner").removeClass("d-flex").addClass("d-none");
        content.addClass("carousel-content--fade-in");
        clearTimeout(interval);
      }
    }, 250)

    let timeout = setTimeout(() => {
      if (!started) {
        content.css("display", "none");
        carousel.find(".carousel-spinner").removeClass("d-flex").addClass("d-none");
      }
      clearTimeout(timeout);
    }, 5000)
  
  } else {
    content.css("display", "none");
    carousel.find(".carousel-spinner").removeClass("d-flex").addClass("d-none");
  }

}