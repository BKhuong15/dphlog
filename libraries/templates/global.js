/**
 * Created by DanielPHenry on 9/4/2018.
 */
$ = jQuery;

dnd = {};
$(document).ready(function()
{
  var $menu = $('#menu');
  $menu.find('a').hover(
    function()
    {
      var $this = $(this);
      var $next = $this.next();
      if ($next.prop('tagName') == 'UL')
      {
        $next.css('display', 'block');
        $next.css('left', $this.position().left + 'px');
      }
    },
    function()
    {
      var $this = $(this);
      var $next = $this.next();
      if ($next.prop('tagName') == 'UL')
      {
        menuHide($next);
      }
    });

  $menu.find('ul').hover(
    function()
    {
      clearInterval(dnd.menu_interval);
      dnd.menu_interval = false;
    },
    function()
    {
      menuHide($(this));
    });

  dnd.menu_interval = false;
  function menuHide($target)
  {
    if (!dnd.menu_interval)
    {
      dnd.menu_interval = setTimeout(function()
      {
        $target.css('display', 'none');
        dnd.menu_interval = false;
      }, 400);
    }
  }

  $('.field.date input').once().each(function()
  {
    $(this).datepicker(
      {
        'changeMonth' : $(this).attr('change_month') === "1",
        'changeYear' : $(this).attr('change_year') === "1",
        'yearRange' : $(this).attr('min_year') + ':' + $(this).attr('max_year'),
        'onChangeMonthYear' : dateChangeMonthYear,
        showOtherMonths: true,
        selectOtherMonths: true
      }).mask("99/99/9999", {placeholder:" "});
  });

  $('.field.time input').once().each(function()
  {
    function restoreFocus()
    {
      $(this).focus();
    }
    $(this).timepicker(
      {
        'step': $(this).attr('step'),
        'onSelect': restoreFocus,
        'timeFormat' : "h:i a",
      }).mask("b9:t9 pm", {placeholder:" "});
  });

});


$.fn.once = function(processed_class)
{
  if (typeof processed_class == 'undefined')
  {
    processed_class = 'processed';
  }
  return this.not('.' + processed_class).addClass(processed_class);
};

function dateChangeMonthYear(year, month)
{
  let date = $(this).val();
  let date_pieces = date.split('/');
  date_pieces[0] = addZero(month);
  date_pieces[1] = date_pieces[1] ? date_pieces[1] : "01";
  date_pieces[2] = addZero(year);
  date = date_pieces.join('/');
  $(this).val(date);
  return false;
}

function addZero(i)
{
  if (i < 10)
  {
    i = '0' + i;
  }

  return i;
}

function modalShow($content)
{
  var $body = $('body');
  var $cover = $('#cover');
  var $modal = $('#modal');
  if (!$cover.length)
  {
    $body.append($('<div id="cover"></div>'));
    //$cover = $('#cover');
  }
  if (!$modal.length)
  {
    $body.append($('<div id="modal"><div id="close-modal">close</div></div>'));
    $modal = $('#modal');
  }
  $modal.find('#close-modal').click(function()
  {
    modalHide();
  });
  $modal.append($content);
  return $modal;
}

function modalLoad()
{
  var $modal = modalShow('');
  $modal.addClass('loading');
}

function modalHide()
{
  $('#cover').remove();
  $('#modal').remove();
}

function getUrlParameter(param, url)
{
  var query;
  if (!url)
  {
    query = window.location.search.substring(1);
  }
  else
  {
    query = url.substring(url.indexOf('?') + 1);
  }
  var args = query.split('&');
  for (var i = 0; i < args.length; i++)
  {
    var arg = args[i].split('=');
    if (arg[0] == param)
    {
      return arg[1];
    }
  }
  return false;
}

/**
 * @param string: string of class/id
 * @returns {string}: machine-readable string
 * Turns strings into machine-readable strings, ie timezone string -> readable class.
 */
function machineName(string)
{
  string = string.replace(/\s/g, '_');
  string = string.toLowerCase();
  string = string.replace(/[^A-Za-z0-9_-]/, '_');
  return string;
}