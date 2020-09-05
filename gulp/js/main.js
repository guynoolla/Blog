import $ from 'jquery';
require('core-js');
require('bootstrap');
import animatedScrollTo from 'animated-scroll-to';
import { preventExtensions } from 'core-js/fn/object';
import Breakpoint from 'bootstrap-breakpoints';
import like from './modules/Like';


window.jQuery = $;

$(document).ready(() => {

  const Like = new like();

  Breakpoint.init();

  deviceWidthResponsiveEmbed();
  navbarSearchBehavior();
  homePagePaginationBehavior();
  editPostFormElementsBehavior();

  var rtime;
  var timeout = false;
  var delta = 200;

  $(window).resize(function() {
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
      var er1 = $("main .lg-one-article-row .post .post-format .embed-responsive");
      var er2 = $("main .lg-two-articles-row .post .post-format .embed-responsive");
      if (er1.length && er2.length) {
        resetEmbedResponsive(er1, er2);
        embedResponsiveOnResize(er1, er2);
      }
    }               
  }

  const slider = $('.slider');

  if (typeof slider.slick == "function") {
    slider.slick({
      slidesToShow: 3,
      slidesToScroll: 1,
      autoplay: true,
      autoplaySpeed: 2000,
      nextArrow: $('.next'),
      prevArrow: $('.prev'),
      responsive: [{
          breakpoint: 1201, //1024,
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
        }
      ]
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

function deviceWidthResponsiveEmbed() {
  var oneColEmbedResponsive = $("main .lg-one-article-row .post .post-format .embed-responsive");
  var twoColEmbedResponsive = $("main .lg-two-articles-row .post .post-format .embed-responsive");

  if (oneColEmbedResponsive.length) {
    if (Breakpoint.is("xs")) { // 576px
      console.log('Breakpoint', 'xs')
      // Leave as is
    }
    if (Breakpoint.is("sm")) { // 768px
      console.log("Breakpoint", "sm")
      twoColEmbedResponsive.removeClass("embed-responsive-16by9");
      twoColEmbedResponsive.addClass("embed-responsive-4by3");
    }
    if (Breakpoint.is("md")) { // 992px
      console.log("Breakpoint", "md")
      // Leave as is
    }
    if (Breakpoint.is("lg")) { // 1200px
      console.log("Breakpoint", "lg")
      // Leave as is
    }
  }
}

function embedResponsiveOnResize(embedResp1, embedResp2) {
  if (Breakpoint.is("xs")) { // 576px
    console.log("Breakpoint", "xs");
    embedResp1.addClass('embed-responsive-16by9');
    embedResp2.addClass('embed-responsive-16by9');
  }
  if (Breakpoint.is("sm")) { // 768px
    console.log("Breakpoint", "sm");
    embedResp1.addClass('embed-responsive-16by9');
    embedResp2.addClass("embed-responsive-4by3");
  }
  if (Breakpoint.is("md")) { // 992px
    console.log("Breakpoint", "md");
    embedResp1.addClass('embed-responsive-16by9');
    embedResp2.addClass('embed-responsive-16by9');
  }
  if (Breakpoint.is("lg")) { // 1200px
    console.log("Breakpoint", "lg");
    embedResp1.addClass('embed-responsive-16by9');
    embedResp2.addClass('embed-responsive-16by9');
  }  
}

function resetEmbedResponsive(embedResp1, embedResp2) {
  embedResp1.removeClass('embed-responsive-4by3');
  embedResp1.removeClass('embed-responsive-16by9');
  embedResp1.removeClass('embed-responsive-21by9');
  embedResp2.removeClass('embed-responsive-4by3');
  embedResp2.removeClass('embed-responsive-16by9');
  embedResp2.removeClass('embed-responsive-21by9');
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