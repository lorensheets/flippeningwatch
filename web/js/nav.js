/* navigation indicator */

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
});
