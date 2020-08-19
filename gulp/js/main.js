import $ from 'jquery';
window.jQuery = $;
//window.$ = $;
//global.jQuery = $;
const bootstrap = require('bootstrap3');
const singlePageNav = require('single-page-nav');
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

  // Single page nav
  $('.fixed-header').singlePageNav({
      offset: 59,
      filter: ':not(.external)',
      updateHash: true        
  });

  checkScrollPosition();

  // nav bar
  $('.navbar-toggle').click(function(){
      $('.main-menu').toggleClass('show');
  });

  $('.main-menu a').click(function(){
      $('.main-menu').removeClass('show');
  });

});

$(window).on("scroll", function() {
    checkScrollPosition();    
});