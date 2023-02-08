/**
 * Created by DanielPHenry on 2/22/2019.
 */

$(document).ready(function()
{
  //$('#log_table').on('click', 'tr', function()
  //{
  //  console.log('hello');
  //  //$('this').css('height', 'auto');
  //});

  var $form = $('#json_formatter');
  $form.find('input[name="submit"]').click(function(e)
  {
    e.preventDefault();
    var snippet = $form.find('.old textarea').val();
    snippet = JSON.parse(snippet);
    snippet = JSON.stringify(snippet, null, 2);
    snippet = syntaxHighlight(snippet);

    $('.json_formatter_output').html(snippet);
  });

  function syntaxHighlight(json)
  {
    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match)
    {
      var cls = 'number';
      if (/^"/.test(match))
      {
        if (/:$/.test(match))
        {
          cls = 'key';
        }
        else
        {
          cls = 'string';
        }
      }
      else if (/true|false/.test(match))
      {
        cls = 'boolean';
      }
      else if (/null/.test(match))
      {
        cls = 'null';
      }
      return '<span class="' + cls + '">' + match + '</span>';
    });
  }
});
