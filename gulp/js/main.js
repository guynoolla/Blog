import $ from 'jquery';
require('core-js');
require('bootstrap');
import animatedScrollTo from 'animated-scroll-to';
import { preventExtensions } from 'core-js/fn/object';
import like from './modules/Like';


window.jQuery = $;

navbarSearchBehavior();
homePagePaginationBehavior();

const Like = new like();

$(document).ready(() => {

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

function homePagePaginationBehavior() {
  $(window).on("load", () => {
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
  })
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