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
  $support_input = '<input type="number" id="support-input" class = "conversion-inputs" min=0 value=0>';
  $connect_input = '<input type="checkbox" id="connect-input" class = "conversion-inputs">';
  $reminder_calls_input = '<input type="number" id="reminder-calls-input" class = "conversion-inputs" min=0 value=0>';
  $refresh_button = '<button type="button" id="refresh">Refresh</button>';

  // Table rows.
  $provider_info = '';
  $provider_info .= htmlWrap('td', 'Full Time Providers' . $provider_input);
  $provider_info .= htmlWrap('td', '$0', array('id' => array('qemr-provider-value')));
  $provider_info .= htmlWrap('td', '$0', array('id' => array('other-provider-value')));
  $provider_info = htmlWrap('tr', $provider_info, array('class' => array('provider-info')));

  $part_time_providers = '';
  $part_time_providers .= htmlWrap('td', 'Part Time Providers' . $part_time_provider_input);
  $part_time_providers .= htmlWrap('td', '$0', array('id' => array('qemr-pt-provider-value')));
  $part_time_providers .= htmlWrap('td', '$0', array('id' => array('other-pt-provider-value')));
  $part_time_providers = htmlWrap('tr', $part_time_providers, array('class' => array('pt-provider-info')));

  $support_persons = '';
  $support_persons .= htmlWrap('td', 'Support Personnel' . $support_input);
  $support_persons .= htmlWrap('td', '$0', array('id' => array('qemr-support-value')));
  $support_persons .= htmlWrap('td', '$0', array('id' => array('other-support-value')));
  $support_persons = htmlWrap('tr', $support_persons, array('class' => array('support-info')));

  $connect = '';
  $connect .= htmlWrap('td', 'Connect' . $connect_input);
  $connect .= htmlWrap('td', '$0', array('id' => array('qemr-connect-value')));
  $connect .= htmlWrap('td', '$0', array('id' => array('other-connect-value')));
  $connect = htmlWrap('tr', $connect, array('class' => array('connect-info')));

  $reminder_calls = '';
  $reminder_calls .= htmlWrap('td', 'Reminder Calls' . $reminder_calls_input);
  $reminder_calls .= htmlWrap('td', '$0', array('id' => array('qemr-calls-value')));
  $reminder_calls .= htmlWrap('td', '$0', array('id' => array('other-calls-value')));
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
      table {
        font-family: Arial, sans-serif;
        border-collapse: collapse;
        width: 500px;
        margin: 0 auto;
        background-color: #fff;
        box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.2);
      }
      
      th {
        font-weight: bold;
        background-color: #1e88e5;
        color: #fff;
        padding: 12px 8px;
        text-align: left;
      }
      
      td {
        border: 1px solid #ddd;
        padding: 12px 8px;
      }
      
      tr:nth-child(even) {
        background-color: #f2f2f2;
      }
      
      .conversion-inputs {
        float: right;
        width: 20%;
      }
      
      #qemr-provider-value,
      #qemr-pt-provider-value,
      #qemr-support-value,
      #qemr-connect-value,
      #qemr-calls-value,
      #qemr-total-value {
        font-weight: bold;
      }
      
      #other-provider-value,
      #other-pt-provider-value,
      #other-support-value,
      #other-connect-value,
      #other-calls-value,
      #other-total-value {
        font-weight: bold;
      }
      
      .price-comparison-table {
        margin-bottom: 40px;
      }
      
      #other-total-value{
        color: red;
      }

      #qemr-total-value{
        color: green;
      }
    
    ';
  $wrapped_style = htmlWrap('style', $styling);
  /***********************
   *
   *  Javascript
   *
   ***********************/

  $javascript =
    '
    $(document).ready(function()
{
  // Pricing variables.
  let qemr_full_time_price = 69;
  let other_full_time_price = 180;

  let qemr_pt_provider_price = 35;
  let other_pt_provider_price = 180;

  let qemr_personnel_price = 0;
  let other_personnel_price = 100; // TBD

  let qemr_connect_price = 0;
  let other_connect_price = 0;

  let qemr_calls_price = 0.10;
  let other_calls_price = 10; // TBD

  // Input IDs.
  let $full_time_provider_input = $("#provider-input");
  let $qemr_provider_value = $("#qemr-provider-value");
  let $other_provider_value = $("#other-provider-value");

  let $part_time_provider_input = $("#pt-provider-input");
  let $qemr_pt_provider_value = $("#qemr-pt-provider-value");
  let $other_pt_provider_value = $("#other-pt-provider-value");

  let $support_input = $("#support-input");
  let $qemr_support_value = $("#qemr-support-value");
  let $other_support_value = $("#other-support-value");


  let $connect_input = $("#connect-input");
  let $qemr_connect_value = $("#qemr-connect-value");
  let $other_connect_value = $("#other-connect-value");

  let $reminder_calls_input = $("#reminder-calls-input");
  let $qemr_reminder_calls_value = $("#qemr-calls-value");
  let $other_reminder_calls_value = $("#other-calls-value");

  let $qemr_totals_cell = $("#qemr-total-value");
  let $other_total_cell = $("#other-total-value")

  // Prices * input values.
  let qemr_ft_amount = 0;
  let qemr_pt_amount = 0;
  let qemr_support_amount = 0;
  let qemr_connect_amount = 0;
  let qemr_calls_amount = 0;

  let other_ft_amount = 0;
  let other_pt_amount = 0;
  let other_support_amount = 0;
  let other_connect_amount = 0
  let other_calls_amount = 0;

  // Full time provider cells.
  qemr_ft_amount = calculatePrices($full_time_provider_input, qemr_full_time_price, $qemr_provider_value);
  other_ft_amount = calculatePrices($full_time_provider_input, other_full_time_price, $other_provider_value);

  $full_time_provider_input.change(function()
  {
    qemr_ft_amount = calculatePrices($full_time_provider_input, qemr_full_time_price, $qemr_provider_value);
    other_ft_amount = calculatePrices($full_time_provider_input, other_full_time_price, $other_provider_value);
    $qemr_totals_cell.trigger("refresh");
    $other_total_cell.trigger("refresh");
  });

  // Part time provicer cells.
  qemr_pt_amount = calculatePrices($part_time_provider_input, qemr_pt_provider_price, $qemr_pt_provider_value);
  other_pt_amount = calculatePrices($part_time_provider_input, other_pt_provider_price, $other_pt_provider_value);

  $part_time_provider_input.change(function()
  {
    qemr_pt_amount = calculatePrices($part_time_provider_input, qemr_pt_provider_price, $qemr_pt_provider_value);
    other_pt_amount = calculatePrices($part_time_provider_input, other_pt_provider_price, $other_pt_provider_value);
    $qemr_totals_cell.trigger("refresh");
    $other_total_cell.trigger("refresh");
  });


  // Support Personnel cells.
  qemr_support_amount = calculatePrices($support_input, qemr_personnel_price, $qemr_support_value);
  other_support_amount = calculatePrices($support_input, other_personnel_price, $other_support_value);

  $support_input.change(function()
  {
    qemr_support_amount = calculatePrices($support_input, qemr_personnel_price, $qemr_support_value);
    other_support_amount = calculatePrices($support_input, other_personnel_price, $other_support_value);
    $qemr_totals_cell.trigger("refresh");
    $other_total_cell.trigger("refresh");
  });

  // Connect cells.
  $qemr_connect_value.text("$" + qemr_connect_price.toFixed(2));
  $other_connect_value.text("$" + other_connect_price.toFixed(2));

  $connect_input.click(function()
  {
    if ($connect_input.is(":checked")){
      qemr_connect_price = 0;
      other_connect_price = 399;
    }
    else
    {
      qemr_connect_price = 0;
      other_connect_price = 0;
    }

    $qemr_connect_value.text("$" + qemr_connect_price.toFixed(2));
    $other_connect_value.text("$" + other_connect_price.toFixed(2));
    qemr_connect_amount = qemr_connect_price;
    other_connect_amount = other_connect_price;

    $qemr_totals_cell.trigger("refresh");
    $other_total_cell.trigger("refresh");

  });

  // Reminder calls cells.
  qemr_calls_amount = calculatePrices($reminder_calls_input, qemr_calls_price, $qemr_reminder_calls_value);
  other_calls_amount = calculatePrices($reminder_calls_input, other_calls_price, $other_reminder_calls_value);

  $reminder_calls_input.change(function()
  {
    qemr_calls_amount = calculatePrices($reminder_calls_input, qemr_calls_price, $qemr_reminder_calls_value);
    other_calls_amount = calculatePrices($reminder_calls_input, other_calls_price, $other_reminder_calls_value);
    $qemr_totals_cell.trigger("refresh");
    $other_total_cell.trigger("refresh");
  });

  // Total cells.

  $qemr_totals_cell.on("refresh", function()
  {
    let total = (qemr_ft_amount + qemr_pt_amount + qemr_calls_amount + qemr_support_amount + qemr_connect_amount);
    $qemr_totals_cell.text("$" + total.toFixed(2));
  });

  $qemr_totals_cell.trigger("refresh");

  $other_total_cell.on("refresh", function()
  {
    let other_total = other_ft_amount + other_pt_amount + other_calls_amount + other_support_amount + other_connect_amount;
    $other_total_cell.text("$" + other_total.toFixed(2));
  });

  $other_total_cell.trigger("refresh");


});

function calculatePrices(input_cell, price, output_cell)
{
  // Get user input.
  let cell_value = parseFloat(input_cell.val());
  // Calculate feature price.
  let amount = cell_value * (price.toFixed(2));

  // Update calculated price on screen.
  output_cell.text("$" + amount.toFixed(2));

  return amount;
}
    ';

  $wrapped_js = htmlWrap('script', $javascript);



  return $wrapped_style . $wrapped_full_table . $wrapped_js;// . $wrapped_javascript;
}