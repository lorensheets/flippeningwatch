/* navigation indicator */

// find out which page visitor is on

if (window.location.href.includes("charts")) {

  $('.navigation-bar a:nth-child(2)').css({
    'border': '2px solid #dddddd',
    'padding': '5px 5px 3px 5px',
    'border-radius': '10px',
    'background-color': '#f5f6f7',
    'font-weight': '800'
  });

} else {

  $('.navigation-bar a:first-child').css({
    'border': '2px solid #dddddd',
    'padding': '5px 5px 3px 5px',
    'border-radius': '10px',
    'background-color': '#f5f6f7',
    'font-weight': '800'
  });

}
