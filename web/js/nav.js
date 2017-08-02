/* navigation indicator and scrollTop button */

// find out which page visitor is on
$(document).ready(function() {


  if (window.location.href.includes("charts")) {
    $('.navigation-bar a:nth-child(2)').css({
      'border': 'var(--link-border)',
      'padding': '5px 5px 3px 5px',
      'border-radius': '10px',
      'background-color': 'var(--link-bg)',
      'font-weight': '800'
    });
  } else {
    $('.navigation-bar a:first-child').css({
      'border': 'var(--link-border)',
      'padding': '5px 5px 3px 5px',
      'border-radius': '10px',
      'background-color': 'var(--link-bg)',
      'font-weight': '800'
    });
  }

  var scrollPos;
  var scrollTopVisible = false;

  window.onscroll = scroll;

  function scroll() {
    scrollPos = window.pageYOffset;

    if (scrollPos > 200 && scrollTopVisible == false) {
      $('#scrollTop').css('display','block');
      $('#scrollTop').animate({
        'opacity': '0.7'
      }, 300);
      scrollTopVisible = true;
    } else if (scrollPos < 200 & scrollTopVisible == true) {
      $('#scrollTop').animate({
        'opacity': '0'
      }, 300);
      $('#scrollTop').css('display','none');
      scrollTopVisible = false;
    }
  }

  $('#scrollTop').on('click', function() {
    $('html,body').animate({scrollTop:0},500,'easeInOutCubic');
  });

});
