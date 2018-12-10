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

  $('#log_table').on('click', '.column-3 pre', function()
  {
    console.log($(this).css('max-height'));
    if ($(this).css('max-height') == '100px')
    {
      $(this).css('max-height', '100%');
    }
    else
    {
      $(this).css('max-height', '100px');
    }
  });
});