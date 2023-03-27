/**
 * Created by DanielPHenry on 10/9/2018.
 */

$(document).ready(function()
{
  //$('#log_table').on('click', 'tr', function()
  //{
  //  console.log('hello');
  //  //$('this').css('height', 'auto');
  //});

  $('#log_table').on('click', 'input.expand', function()
  {
    var $content = $(this).parents('tr').find('td.column-4 pre');
    if ($content.css('max-height') == '100px')
    {
      $content.css('max-height', '100%');
    }
    else
    {
      $content.css('max-height', '100px');
    }
  });
});