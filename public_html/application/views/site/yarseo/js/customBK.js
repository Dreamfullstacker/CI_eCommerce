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
   TWITTER FEED
============================================================== */
function handleTweets(tweets) {
    var x = tweets.length,
    n = 0,
    element = document.getElementById('twitter-feed'),
    html = '<div class="slides">';
    while (n < x) {
        html += '<div>' + tweets[n] + '</div>';
        n++;
    }
    html += '</div>';
    element.innerHTML = html;
        
    /* Twits attached to owl-carousel */
    $("#twitter-feed .slides").owlCarousel({
        slideSpeed : 300,
        paginationSpeed : 400,
        autoPlay: true,
        pagination: false,
        transitionStyle : "fade",
        singleItem: true
    });
}

if( $('#twitter-feed').length ) {   

    var config_feed = {
      "profile": {"screenName": 'envato'},
      "domId": 'twitter-feed',
      "maxTweets": 5,
      "enableLinks": true,
      "showUser": false,
      "showTime": true,
      "dateFunction": '',
      "showRetweet": false,
      "customCallback": handleTweets,
      "showInteraction": false
    };

    twitterFetcher.fetch(config_feed);
}


/* ===========================================================
   TWITTER WIDGET FOR TESTIMONIALS
============================================================== */
window.twttr = (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0],
        t = window.twttr || {};
    if (d.getElementById(id)) return t;
    js = d.createElement(s);
    js.id = id;
    js.src = "https://platform.twitter.com/widgets.js";
    fjs.parentNode.insertBefore(js, fjs);
    t._e = [];
    t.ready = function(f) {
        t._e.push(f);
    };
    return t;
}(document, "script", "twitter-wjs"));


/* ===========================================================
   COUNTDOWN TIMER
============================================================== */
if( $('.countdown').length ) {
    $(".countdown").jCounter({
        date: "16 december 2016 9:00:00", // Deadline date
        timezone: "Europe/London",
        format: "dd:hh:mm:ss",
        twoDigits: 'on',
        serverDateSource: "php/dateandtime.php",
        fallback: function() {console.log("Count finished!")}
    });
}


/* ===========================================================
   MAILCHIMP
============================================================== */
if( $('#mailchimpForm').length ) {
    $("#mailchimpForm").formchimp(); 
}


/* ===========================================================
   GOOGLE MAPS
============================================================== */
/* active mouse scroll when the user clicks into the map*/
if( $('.map-container').length ) {
    $('.map-container').on('click', function () {
        $('.map-iframe').css("pointer-events", "auto");
    });

    $( ".map-container" ).on('mouseleave', function() {
      $('.map-iframe').css("pointer-events", "none");
    });
}


/* ==========================================
   FUNCTION FOR EMAIL ADDRESS VALIDATION
============================================= */
function isValidEmail(emailAddress) {
    var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
}


/* ==========================================
   FUNCTION FOR PHONE NUMBER VALIDATION
============================================= */
function isValidPhoneNumber(phoneNumber) {
    return phoneNumber.match(/[0-9-()+]{3,20}/);
}


/* ==========================================
   CONTACT FORM
============================================= */
$("#contactForm").on('submit', function(e) {
    
    e.preventDefault();
    var data = {
        name: $("#cfName").val(),
        email: $("#cfEmail").val(),
        subject: $("#cfSubject").val(),
        message: $("#cfMessage").val()
    };

    if ( isValidEmail(data['email']) && (data['message'].length > 1) && (data['name'].length > 1) && (data['subject'].length > 1) ) {
        $.ajax({
            type: "POST",
            url: "php/contact.php",
            data: data,
            success: function() {
                $('.success.cf').delay(500).fadeIn(1000);
                $('.failed.cf').fadeOut(500);
            }
        });
    } else {
        $('.failed.cf').delay(500).fadeIn(1000);
        $('.success.cf').fadeOut(500);
    }

    return false;
});


/* ==========================================
   CALLBACK FORM
============================================= */
$("#callbackForm").on('submit', function(e) {
    e.preventDefault();
    var data = {
        name: $("#cbName").val(),
        email: $("#cbEmail").val(),
        phone: $("#cbPhone").val()
    };

    if ( isValidEmail(data['email']) && (data['name'].length > 1) && isValidPhoneNumber(data['phone']) ) {
        $.ajax({
            type: "POST",
            url: "php/callback.php",
            data: data,
            success: function() {
                $('.success.cb').delay(500).fadeIn(1000);
                $('.failed.cb').fadeOut(500);
            }
        });
    } else {
        $('.failed.cb').delay(500).fadeIn(1000);
        $('.success.cb').fadeOut(500);
    }

    return false;
});


/* ==========================================
   TICKET SELECTION
============================================= */
var $ticketSelected = $('.ticket-selection .item-price');

$ticketSelected.on('click', function(event) {
    $ticketSelected.removeClass('active');
    $(this).addClass('active');

    $('#ticketForm input[name="ticket"]').val($(this).find('h4').text() + ' Ticket - Cost: ' + $(this).find('.amount').text());
});


/* ==========================================
   TICKET FORM
============================================= */
$("#ticketForm").on('submit', function(e) {
    e.preventDefault();
    var data = {
        name: $("#tfName").val(),
        email: $("#tfEmail").val(),
        phone: $("#tfPhone").val(),
        ticket: $("#tfTicket").val()
    };

    if ( isValidEmail(data['email']) && (data['name'].length > 1) && (data['ticket'].length > 1) && isValidPhoneNumber(data['phone']) ) {
        $.ajax({
            type: "POST",
            url: "php/ticket.php",
            data: data,
            success: function() {
                $('.success.tf').delay(500).fadeIn(1000);
                $('.failed.tf').fadeOut(500);
            }
        });
    } else {
        $('.failed.tf').delay(500).fadeIn(1000);
        $('.success.tf').fadeOut(500);
    }

    return false;
});


/* ==========================================
   QUOTE FORM
============================================= */
$("#quoteForm").on('submit', function(e) {
    e.preventDefault();
    var data = {
        name: $("#qName").val(),
        email: $("#qEmail").val(),
        phone: $("#qPhone").val(),
        message: $("#qMessage").val()
    };

    if ( isValidEmail(data['email']) && (data['name'].length > 1) && (data['message'].length > 1) && isValidPhoneNumber(data['phone']) ) {
        $.ajax({
            type: "POST",
            url: "php/quote.php",
            data: data,
            success: function() {
                $('.success.qf').delay(500).fadeIn(1000);
                $('.failed.qf').fadeOut(500);
            }
        });
    } else {
        $('.failed.qf').delay(500).fadeIn(1000);
        $('.success.qf').fadeOut(500);
    }

    return false;
});


/* ==========================================
   DATEPICKER
============================================= */
if( $("#dfDate").length ) {
    $('#dfDate').pickadate({
        min: new Date()
    });
}


/* ==========================================
   APPOINTMENT WITH DATEPICKER FORM
============================================= */
$("#dateForm").on('submit', function(e) {
    e.preventDefault();
    var data = {
        name: $("#dfName").val(),
        email: $("#dfEmail").val(),
        phone: $("#dfPhone").val(),
        date: $("#dfDate").val(),
        message: $("#dfMessage").val()
    };

    if ( isValidEmail(data['email']) && (data['name'].length > 1) && (data['date'].length > 1) && (data['message'].length > 1) && isValidPhoneNumber(data['phone']) ) {
        $.ajax({
            type: "POST",
            url: "php/appointment.php",
            data: data,
            success: function() {
                $('.success.df').delay(500).fadeIn(1000);
                $('.failed.df').fadeOut(500);
            }
        });
    } else {
        $('.failed.df').delay(500).fadeIn(1000);
        $('.success.df').fadeOut(500);
    }

    return false;
});


/* ==========================================
   SUBSCRIBE FORM / ONLY EMAIL
============================================= */
$("#subscribeForm").on('submit', function(e) {
    e.preventDefault();
    var data = {
        email: $("#sfEmail").val()
    };
        
    if ( isValidEmail(data['email']) ) {
        $.ajax({
            type: "POST",
            url: "php/subscribe.php",
            data: data,
            success: function() {
                $('.success.sf').delay(500).fadeIn(1000);
                $('.failed.sf').fadeOut(500);
            }
        });
    } else {
        $('.failed.sf').delay(500).fadeIn(1000);
        $('.success.sf').fadeOut(500);
    }

    return false;
});


/* ==========================================
   SUBSCRIBE FORM 2 / EMAIL + NAME
============================================= */
$("#subscribeForm2").on('submit', function(e) {
    e.preventDefault();
    var data = {
        name: $("#sf2Name").val(),
        email: $("#sf2Email").val()
    };
        
    if ( isValidEmail(data['email']) && (data['name'].length > 1) ) {
        $.ajax({
            type: "POST",
            url: "php/subscribe2.php",
            data: data,
            success: function() {
                $('.success.sf2').delay(500).fadeIn(1000);
                $('.failed.sf2').fadeOut(500);
            }
        });
    } else {
        $('.failed.sf2').delay(500).fadeIn(1000);
        $('.success.sf2').fadeOut(500);
    }

    return false;
});


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