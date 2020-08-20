import $ from 'jquery';
window.jQuery = $;
//window.$ = $;
//global.jQuery = $;
const bootstrap = require('bootstrap3');
import animateScrollTo from 'animated-scroll-to';
import Like from './modules/Like';


// Check scroll position and add/remove background to navbar
function checkScrollPosition() {
  if ($(window).scrollTop() > 50) {
    $(".fixed-header").addClass("scroll");
  } else {        
    $(".fixed-header").removeClass("scroll");
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

  $(".fixed-header").on("click", () => {
    scrollToEventHandler(document.querySelector('#contact'));
  })

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