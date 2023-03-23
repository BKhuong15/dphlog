<?php
/**
 * @return HTMLTemplate
 */
function timePage()
{
  // Template.
  $template = new HTMLTemplate();
  $template->setTitle('Epoch Converter');
  $template->addCssFilePath('/libraries/time/time.css');
  $template->addJsFilePath('/libraries/time/time.js');
  $template->setMenu(menu());

  // Header.
  $header = htmlWrap('h1', 'Epoch Converter');

  $output = '';

  // Form.
  $output .= htmlWrap('div', timeForm(), array('class' => array('form-section')));

  // Top clock.
  $top_clock = htmlWrap('div', '01/01/1970', array('class' => array('current-date')));
  $top_clock .= htmlWrap('div', '1234567890', array('class' => array('unix-time')));
  $top_clock .= htmlWrap('div', '', array('id' => array('clock-state')));
  $output .= htmlWrap('div', $top_clock, array('class' => array('date-and-epoch')));

  // Reset/Now button.
  $now_button = new FieldSubmit('now-button', 'Now');
  $output .= htmlWrap('div', $now_button, array('class' => array('right-form')));

  // Wrap Clock, Form, and Now button.
  $output = htmlWrap('div', $output, array('class' => array('top-section')));

  // Clocks.
  $clocks = '';
  $timezones = getTimeTimezoneList();
  $user_hash_map = sortUsers();

  // For each timezone, add a list of users in each timezone under each clock.
  foreach ($timezones as $key => $value)
  {
    $users_in_zone = $user_hash_map[$key];
    $clocks .= timeClock($key, $users_in_zone);
  }

  $clocks = htmlWrap('div', $clocks, array('class' => array('clock-section')));
  $output .= htmlWrap('div', $clocks, array('class' => array('clock-container')));

  /*****************************
   * Return final html template
   *****************************/
//  $template->setBody($header . $output);

  $template->setBody(calculator());
  return $template;
}

function calculator()
{
  $output = 'hello world';

  /*****************************
   *
   *   Price Comparison Table
   *
   *****************************/
  // Table columns.
  $table_headers = '';
  $table_headers .= htmlWrap('th', 'Values');
  $table_headers .= htmlWrap('th', 'QEMR');
  $table_headers .= htmlWrap('th', 'WEB PT');
  $table_headers = htmlWrap('tr', $table_headers, array('class' => array('table-headers')));

  // Conversion inputs.
  $provider_input = '<input type="number" id="provider-input" class = "conversion-inputs" min=1 value=1>';
  $part_time_provider_input = '<input type="number" id="pt-provider-input" class = "conversion-inputs" min=1 value=1>';
  $reminder_calls_input = '<input type="number" id="reminder-calls-input" class = "conversion-inputs" min=0 value=0>';

  // Table rows.
  $provider_info = '';
  $provider_info .= htmlWrap('td', 'Full Time Providers' . $provider_input);
  $provider_info .= htmlWrap('td', '$69', array('id' => array('qemr-provider-value')));
  $provider_info .= htmlWrap('td', '$180', array('id' => array('other-provider-value')));
  $provider_info = htmlWrap('tr', $provider_info, array('class' => array('provider-info')));

  $part_time_providers = '';
  $part_time_providers .= htmlWrap('td', 'Part Time Providers' . $part_time_provider_input);
  $part_time_providers .= htmlWrap('td', '$35', array('id' => array('qemr-pt-provider-value')));
  $part_time_providers .= htmlWrap('td', '$180', array('id' => array('other-pt-provider-value')));
  $part_time_providers = htmlWrap('tr', $part_time_providers, array('class' => array('pt-provider-info')));

  $support_persons = '';
  $support_persons .= htmlWrap('td', 'Support Personnel');
  $support_persons .= htmlWrap('td', '$0', array('id' => array('qemr-support-value')));
  $support_persons .= htmlWrap('td', 'TBD', array('id' => array('other-support-value')));
  $support_persons = htmlWrap('tr', $support_persons, array('class' => array('support-info')));

  $connect = '';
  $connect .= htmlWrap('td', 'Connect');
  $connect .= htmlWrap('td', '$0', array('id' => array('qemr-connect-value')));
  $connect .= htmlWrap('td', '$399', array('id' => array('other-connect-value')));
  $connect = htmlWrap('tr', $connect, array('class' => array('connect-info')));

  $reminder_calls = '';
  $reminder_calls .= htmlWrap('td', 'Reminder Calls' . $reminder_calls_input);
  $reminder_calls .= htmlWrap('td', '$0.10', array('id' => array('qemr-calls-value')));
  $reminder_calls .= htmlWrap('td', 'TBD', array('id' => array('other-calls-value')));
  $reminder_calls = htmlWrap('tr', $reminder_calls, array('class' => array('calls-info')));

  $total_prices = '';
  $total_prices .= htmlWrap('td', 'Total');
  $total_prices .= htmlWrap('td', '$0', array('id' => array('qemr-total-value')));
  $total_prices .= htmlWrap('td', '$0', array('id' => array('other-total-value')));
  $total_prices = htmlWrap('tr', $total_prices, array('class' => array('total-info')));

  // Combine items.
  $full_table = $table_headers . $provider_info . $part_time_providers . $support_persons . $connect . $reminder_calls . $total_prices;
  $wrapped_full_table = htmlWrap('table', $full_table, array('class' => array('price-comparison-table')));

  /***********************
   *
   * Styling
   *
   ***********************/
    $styling = '
    table 
    {
      font-family: arial, sans-serif;
      border-collapse: collapse;
      width: 30%;
    }

    td, th 
    {
      border: 1px solid #dddddd;
      text-align: left;
      padding: 8px;
    }
    
    tr:nth-child(even) 
    {
      background-color: #dddddd;
    }
    
    .conversion-inputs
    {
      float: right;
      width: 20%;
    }
    ';

    $wrapped_style = htmlWrap('style', $styling);

  /***********************
   *
   *  Javascript
   *
   ***********************/

    $javacript =
      '
//      $(document).ready(function()
//      {
//        let $full_time_provider = $(".provider-info");
//        let $pt_provider = $(".pt-provider-info");
//        let $reminder_calls = $(".calls-info");
//    
//        let full_time_provider_value = $full_time_provider.find("#provider-input input").val();
//        
//        console.log(full_time_provider_value);
//        
//        $full_time_provider.find(".qemr-provider-value").val(full_time_provider_value * 69)
//        
//        
//      });

        provider-input.oninput = function() {
          qemr-provider-value.innerHTML = provider-input.value;
        };



      ';

    $js_2 =
      '
$(document).ready(function() {
  let $full_time_provider = $(".provider-info");
  let $pt_provider = $(".pt-provider-info");
  let $reminder_calls = $(".calls-info");

  let $full_time_provider_input = $full_time_provider.find("#provider-input input");
  console.log("$full_time_provider_input:", $full_time_provider_input); // Debugging statement

  let full_time_provider_value = parseFloat($full_time_provider_input.val());

  let $qemr_provider_value = $full_time_provider.find(".qemr-provider-value");

  $full_time_provider_input.change(function() {
    full_time_provider_value = parseFloat($full_time_provider_input.val());
    $qemr_provider_value.text(full_time_provider_value * 69);
  });

  $qemr_provider_value.text(full_time_provider_value * 69);
});

      
      ';





      $wrapped_javascript = htmlWrap('script', $js_2);
    





  return $wrapped_style . $wrapped_full_table;// . $wrapped_javascript;
}