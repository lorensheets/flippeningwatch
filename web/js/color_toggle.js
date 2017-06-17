var mode;

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function checkCookie() {
    mode = getCookie("mode");
    if (mode == "") {
      document.cookie = "mode=day; expires=100";
    } else if (mode == "night") {
      $('#toggle-color').attr('src', 'images/moon.svg');
      document.body.style.setProperty('--main-bg-color', '#131517');
      document.body.style.setProperty('--main-text-color', '#eee');
      document.body.style.setProperty('--table-bg', '#07090a');
      document.body.style.setProperty('--ticker-color', '#ccc');
      document.body.style.setProperty('--link-color', '#eee');
      document.body.style.setProperty('--link-bg', '#222');
      document.body.style.setProperty('--link-border', '2px solid #444');
      document.body.style.setProperty('--light-grey', '#aaa');
      document.body.style.setProperty('--border', '1px solid #444');
      document.body.style.setProperty('--dark-border', '1px solid #333');
      document.body.style.setProperty('--pct', '#333');
      $('.invert').css({
        'filter' : 'invert(100%)',
        '-webkit-filter' : 'invert(100%)',
        '-moz-filter' : 'invert(100%)',
        '-ms-filter' : 'invert(100%)',
        '-o-filter' : 'invert(100%)'
      });
    }
}

checkCookie();

var scroll = function() {
  var scrollTop = window.pageYOffset;
  if (scrollTop > 100) {
    $('#toggle-color').fadeOut();
  } else {
    $('#toggle-color').fadeIn();
  }
}
window.onscroll = scroll;


$('#toggle-color').click(function() {
if (mode == "day") {
    document.cookie = "mode=night; expires=100";
    $(this).attr('src', 'images/moon.svg');

    document.body.style.setProperty('--main-bg-color', '#131517');
    document.body.style.setProperty('--main-text-color', '#eee');
    document.body.style.setProperty('--table-bg', '#07090a');
    document.body.style.setProperty('--ticker-color', '#ccc');
    document.body.style.setProperty('--link-color', '#eee');
    document.body.style.setProperty('--link-bg', '#222');
    document.body.style.setProperty('--link-border', '2px solid #444');
    document.body.style.setProperty('--light-grey', '#aaa');
    document.body.style.setProperty('--border', '1px solid #444');
    document.body.style.setProperty('--dark-border', '1px solid #333');
    document.body.style.setProperty('--pct', '#333');

    $('.invert').css({
      'filter' : 'invert(100%)',
      '-webkit-filter' : 'invert(100%)',
      '-moz-filter' : 'invert(100%)',
      '-ms-filter' : 'invert(100%)',
      '-o-filter' : 'invert(100%)'
    });

    mode = "night";
  } else {
    document.cookie = "mode=day; expires=100";
    $(this).attr('src', 'images/sun.png');

    document.body.style.setProperty('--main-bg-color', '#fff');
    document.body.style.setProperty('--main-text-color', '#222');
    document.body.style.setProperty('--table-bg', '#f6f6f6');
    document.body.style.setProperty('--ticker-color', '#444');
    document.body.style.setProperty('--link-color', '#444');
    document.body.style.setProperty('--link-bg', '#f5f6f7');
    document.body.style.setProperty('--link-border', '2px solid #ddd');
    document.body.style.setProperty('--light-grey', '#888');
    document.body.style.setProperty('--border', '1px solid #ddd');
    document.body.style.setProperty('--dark-border', '1px solid #ccc');
    document.body.style.setProperty('--pct', '#eee');

    $('.invert').css({
      'filter' : 'invert(0%)',
      '-webkit-filter' : 'invert(0%)',
      '-moz-filter' : 'invert(0%)',
      '-ms-filter' : 'invert(0%)',
      '-o-filter' : 'invert(0%)'
    });

    mode = "day";
  }
});
