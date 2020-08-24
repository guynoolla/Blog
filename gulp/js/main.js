import $ from 'jquery';
window.jQuery = $;
//window.$ = $;
//global.jQuery = $;
const bootstrap = require('bootstrap3');
import animateScrollTo from 'animated-scroll-to';
import Like from './modules/Like';


window.addEventListener('DOMContentLoaded', () => {
  const home = document.getElementById("home");
  const secondary = document.getElementById("secondary");
  const homeStyle = window.getComputedStyle(home);
  secondary.style.height = homeStyle.height;
});

// Check scroll position and add/remove background to navbar
function checkScrollPosition() {
  if ($(window).scrollTop() > 50) {
    $(".fixed-header").addClass("scroll");
  } else {        
    $(".fixed-header").removeClass("scroll");
  }
  if ($(window).scrollTop() < 300) {
    $(".scroll-to-top").css("display", "none");
  } else {
    $(".scroll-to-top").css("display", "block");
  }
}

$(document).ready(function () { 

  const like = new Like();

  // nav bar
  $('.navbar-toggle').click(function(){
    $('.main-menu').toggleClass('show');
  });

  $('.main-menu a').click(function(){
    $('.main-menu').removeClass('show');
  });

  $(".scroll-to-top").on('click', () => {
    scrollToEventHandler(0);
  });

  $(".site-nav-js li").on("click", () => {
    scrollToEventHandler(document.querySelector('#contact'));
  })

  // Can also be used with $(document).ready()
  $(window).on('load', function() {
    const slider = $('.slider');

    if (typeof slider.slick == "function") {
      slider.slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: false,
        autoplaySpeed: 15000,
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
      });
    }
  });

});

$(window).on("scroll", function() {
  checkScrollPosition();    
});

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
