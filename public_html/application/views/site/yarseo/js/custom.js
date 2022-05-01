$(document).ready(function() {



"use strict";





/* =================================

   LOADER                     

=================================== */

// makes sure the whole site is loaded

$(window).on('load', function() {



    // will first fade out the loading animation

    $(".loader-inner").fadeOut();

    // will fade out the whole DIV that covers the website.

    $(".loader").fadeOut("slow");



});





/* =================================

   NAVBAR COLLAPSE ON SCROLL

=================================== */

$(window).on('scroll', function(){

    var b = $(window).scrollTop();

    if( b > 60 ){

        $(".navbar").addClass("top-nav-collapse");

    } else {

        $(".navbar").removeClass("top-nav-collapse");

    }

});





/* =================================

   NAVBAR WITH TOP BAR

=================================== */

$('.nav-2').affix({

      offset: {

        top: $('.top-bar').height()

      }

});





/* ===========================================================

   PAGE SCROLLING FEATURE

============================================================== */

$('a.smooth-scroll').on('click', function(event) {

    var $anchor = $(this);

    $('html, body').stop().animate({

        scrollTop: $($anchor.attr('href')).offset().top + 20

    }, 1500, 'easeInOutExpo');

    event.preventDefault();

});





/* ===========================================================

   DYNAMIC PRICING TABLE

============================================================== */

$('.package-toggle').each(function () {

    $(this).change(function() {

        var curr_class = '.' + $(this).attr('id');

        var price = $(this).attr('data-price');

        var price_box = $('.pricing-table li.price span');



        $(curr_class).toggleClass('active');



        if (price) {

            if ($(curr_class).hasClass('active')) {

              price_box.html(parseInt(price_box.html(), 10) + parseInt(price, 10));

            }

            else {

              price_box.html(parseInt(price_box.html(), 10) - parseInt(price, 10));

            }

        }

    });

});





/* ===========================================================

   BACK TO TOP BUTTON

============================================================== */

var offset = 300,

//browser window scroll (in pixels) after which the "back to top" link opacity is reduced

offset_opacity = 1200,

//duration of the top scrolling animation (in ms)

scroll_top_duration = 700,

//grab the "back to top" link

$back_to_top = $('.top');



//hide or show the "back to top" link

$(window).on('scroll', function() {

    ( $(this).scrollTop() > offset ) ? $back_to_top.addClass('is-visible') : $back_to_top.removeClass('is-visible fade-out');

    if( $(this).scrollTop() > offset_opacity ) {

        $back_to_top.addClass('fade-out');

    }

});



//smooth scroll to top

$back_to_top.on('click', function(event){

    event.preventDefault();

    $('body,html').animate({

        scrollTop: 0

        }, scroll_top_duration

    );

});





/* ===========================================================

    WOW ANIMATIONS                   

============================================================== */

new WOW().init();





/* ===========================================================

   HIDE MOBILE MENU AFTER CLICKING 

============================================================== */

$('.navbar-nav>li>a:not(#dLabel)').on('click', function(){

    $('#navbar-collapse').removeClass("in").addClass("collapse"); 

});





/* ===========================================================

   VIDEO BACKGROUND

============================================================== */

$('.video-play').vide("images/video/video", {

    posterType: "jpg"

});





/* ===========================================================

   MAGNIFIC POPUP

============================================================== */

$('.mp-singleimg').magnificPopup({

    type: 'image'

});



$('.mp-gallery').magnificPopup({

    type: 'image',

    gallery:{enabled:true},

});



$('.mp-iframe').magnificPopup({

    type: 'iframe'

});





/* ===========================================================

   FUNFACTS COUNTER

============================================================== */

if( $('.counter').length ) {

    var o = $('.counter'),

    cc = 1;



    $(window).on('scroll', function() {

        var elemPos = o.offset().top,

        elemPosBottom = o.offset().top + o.height(),

        winHeight = $(window).height(),

        scrollToElem = elemPos - winHeight,

        winScrollTop = $(this).scrollTop();



        if (winScrollTop > scrollToElem) {

            if(elemPosBottom > winScrollTop){

                if (cc < 2){

                    cc = cc + 2;

                    o.countTo();                    

                }

            }

        }

    });

}





/* ===========================================================

   FEATURES TAB

============================================================== */

$('.features-tab .tab-title').on('click', function(e) {

    if (!$(this).hasClass('current')) {

        $('.tab-title').removeClass('out');

        $('.tab-title.current').addClass('out');

        $('.features-tab .tab-title').removeClass('current');

        $(this).addClass('current');

    }

    e.preventDefault();

});





/* ===========================================================

   FEATURES TAB - SCROLLING TO THE TAB-TITLE ON MOBILE DEVICES

==============================================================  */

var mQ = window.matchMedia('(max-width: 767px)');

mQ.addListener(tabScrolling);

  

function tabScrolling(mQ) {    

    if (mQ.matches) {

        $('.features-tab .tab-title').on('click', function(event) {

            var $anchor = $(this);

            $('html, body').stop().animate({

                scrollTop: $anchor.offset().top - 90

            }, 500, 'easeInOutExpo');

            event.preventDefault();

        });

    }    

}

  

tabScrolling(mQ);






/* ===========================================================

   BOOTSTRAP FIX FOR IE10 in Windows 8 and Windows Phone 8  

============================================================== */

if (navigator.userAgent.match(/IEMobile\/10\.0/)) {

    var msViewportStyle = document.createElement('style');

    msViewportStyle.appendChild(

        document.createTextNode(

            '@-ms-viewport{width:auto!important}'

            )

        );

    document.querySelector('head').appendChild(msViewportStyle);

}







}); // End $(document).ready Function