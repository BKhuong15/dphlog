/**
 * Created by DanielPHenry on 10/9/2018.
 */

$(document).ready(function()
{
  $('input.copy-button').click(function (e)
  {
    e.preventDefault();
    var $link = $('.short-link-hidden');
    $link.show();
    $link[0].select();
    document.execCommand('copy');
    $link.hide();
  });
});