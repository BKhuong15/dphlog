/**
 * Created by DanielPHenry on 2/22/2019.
 */
$(document).ready(function()
{
  let $form = $('#unserialize');

  $form.find('.submit input').click(function(e)
  {
    e.preventDefault();

    // Data from user input.
    let data = {
      operation: 'view',
      base_64: $form.find('.field.base64-check input').prop('checked'),
      serialized: $form.find('.field.unserialize-check input').prop('checked'),
      raw_input: $form.find('.field.string-input textarea').val(),
    }

    // Send post request to this link with the data
    $.post('/ajax/unserialize?operation=view', data, function(response)
    {
      $('.unserialize_output').text(response.data);
    }, 'json', function(response, status){ console.log('Error.')});
  });
});

