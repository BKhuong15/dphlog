/**
 * Created by DanielPHenry on 2/22/2019.
 */
$(document).ready(function()
{
  let $form = $('#unserialize');

  $form.find('input[name="submit"]').click(function(e)
  {
    e.preventDefault();

    let base64 = $form.find('.field.base64-check input').prop('checked');
    let unserialized = $form.find('.field.unserialize-check input').prop('checked');
    let raw_input = $form.find('.field.string-input textarea').val();

    let data = {
      base64: $form.find('.field.base64-check input').prop('checked'),
    }
    console.log(raw_input);
    console.log(base64);
    console.log(unserialized);

    $.post('/ajax/unserialize?operation=view', data, function(response)
    {
      if (response['status'])
      {
        // post output response['data']
      }
      else
      {
        // error handling
        console.log(response['data']);
      }
    }, 'json');

    let test_input = 'SGVsbG8gV29ybGQ=';

    let output = '';
    //
    // if (base64 && unserialized)
    // {
    //   output = atob(JSON.parse(raw_input));
    // }
    // else if (base64)
    // {
    //   output = atob(raw_input);
    // }
    // else if (unserialized)
    // {
    //   output = JSON.parse(raw_input);
    // }
    // else
    // {
    //   output = raw_input;
    // }

    $('.unserialize_output').html(output);
  });
});

