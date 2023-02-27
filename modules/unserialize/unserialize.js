/**
 * Created by DanielPHenry on 2/22/2019.
 */
$(document).ready(function()
{
  let $form = $('#unserialize');

  $form.find('input[name="submit"]').click(function(e)
  {
    e.preventDefault();

    console.log('button pressed');
    let data = {
      operation: 'view',
      base_64: $form.find('.field.base64-check input').prop('checked'),
      serialized: $form.find('.field.unserialize-check input').prop('checked'),
      raw_input: $form.find('.field.string-input textarea').val(),
    }

    console.log(data);
    // Send post request to this link with the data
    $.post('/ajax/unserialize?operation=view', data, function(response)
    {
      if (response.status == true)
      {
        // post output response['data']
        console.log('Reponse status success')
        console.log(response['data']);
      }
      else
      {
        // error handling
        console.log('Error processing data: ' + response.message);
        //console.log(response['data']);
      }

      // Update html output with converted text.
      $('.unserialize_output').html(response.data);

    }, 'json', function(response, status){ console.log('Error.')});

    let test_input = 'SGVsbG8gV29ybGQ=';




  });
});

