<?php

function calculatorPage()
{
  $output = '';

  /*****************************
   *   Header
   *****************************/
  $table_header = '';
  $table_header .= htmlWrap('th', '', array('class' => array('column-1')));
  $table_header .= htmlWrap('th', '', array('class' => array('column-2')));

  $label = htmlWrap('span', 'QuickEMR', array('class' => array('label'))) . htmlSolo('br');
  $label .= htmlWrap('span', '$69.00', array('class' => array('total')));
  $label = htmlWrap('div', $label, array('class' => array('total-wrapper')));
  $table_header .= htmlWrap('th', $label, array('class' => array('column-3', 'qemr')));

  $label = htmlWrap('span', 'WebPT', array('class' => array('label'))) . htmlSolo('br');
  $label .= htmlWrap('span', '$180.00', array('class' => array('total')));
  $label = htmlWrap('div', $label, array('class' => array('total-wrapper')));
  $table_header .= htmlWrap('th', $label, array('class' => array('column-4', 'other')));

  $table_header = htmlWrap('thead', htmlWrap('tr', $table_header, array('class' => array('table-header'))));

  /*****************************
   *   Body
   *****************************/
  $table = '';

  // Full time providers
  $provider_info = '';
  $provider_info .= htmlWrap('td', 'Full Time Providers', array('class' => array('column-1')));
  $provider_info .= htmlWrap('td', fieldNumber('full_time_provider', 1), array('class' => array('column-2')));
  $provider_info .= htmlWrap('td', '$0.00', array('id' => 'qemr-provider-value', 'class' => array('column-3')));
  $provider_info .= htmlWrap('td', '$0.00', array('id' => 'other-provider-value', 'class' => array('column-4')));
  $table .= htmlWrap('tr', $provider_info, array('class' => array('provider-info')));

  // Part time providers
  $part_time_providers = '';
  $part_time_providers .= htmlWrap('td', 'Part Time Providers', array('class' => array('column-1')));
  $part_time_providers .= htmlWrap('td', fieldNumber('part_time_provider'), array('class' => array('column-2')));
  $part_time_providers .= htmlWrap('td', '$0.00', array('id' => 'qemr-pt-provider-value', 'class' => array('column-3')));
  $part_time_providers .= htmlWrap('td', '$0.00', array('id' => 'other-pt-provider-value', 'class' => array('column-4')));
  $table .= htmlWrap('tr', $part_time_providers, array('class' => array('pt-provider-info')));

  // Support personnel.
  $support_persons = '';
  $support_persons .= htmlWrap('td', 'Support Personnel', array('class' => array('column-1')));
  $support_persons .= htmlWrap('td', fieldNumber('support'), array('class' => array('column-2')));
  $support_persons .= htmlWrap('td', '$0', array('id' => 'qemr-support-value', 'class' => array('column-3')));
  $support_persons .= htmlWrap('td', '$0', array('id' => 'other-support-value', 'class' => array('column-4')));
  $table .= htmlWrap('tr', $support_persons, array('class' => array('support-info')));

  // Connect.
  $connect = '';
  $connect .= htmlWrap('td', 'Connect', array('class' => array('column-1')));
  $attr = array(
    'type' => 'checkbox',
    'id' => 'connect-input',
    'class' => array('conversion-inputs'),
  );
  $connect_input = htmlSolo('input', $attr);
  $connect_input .= htmlWrap('span', '', array('class' => array('slider', 'round')));
  $connect_input = htmlWrap('label', $connect_input, array('class' => array('switch')));
  $connect .= htmlWrap('td', $connect_input, array('class' => array('column-2')));
  $connect .= htmlWrap('td', '$0.00', array('id' => 'qemr-connect-value', 'class' => array('column-3')));
  $connect .= htmlWrap('td', '$0.00', array('id' => 'other-connect-value', 'class' => array('column-4')));
  $table .= htmlWrap('tr', $connect, array('class' => array('connect-info')));

  // Reminder calls.
  $reminder_calls = '';
  $reminder_calls .= htmlWrap('td', 'Reminder Calls', array('class' => array('column-1')));
  $reminder_calls .= htmlWrap('td', fieldNumber('reminder_calls'), array('class' => array('column-2')));
  $reminder_calls .= htmlWrap('td', '$0.00', array('id' => 'qemr-calls-value', 'class' => array('column-3')));
  $reminder_calls .= htmlWrap('td', '$0.00', array('id' => 'other-calls-value', 'class' => array('column-4')));
  $table .= htmlWrap('tr', $reminder_calls, array('class' => array('calls-info')));

  // Combine items.
  $full_table = $table_header . htmlWrap('tbody', $table);
  $output .= htmlWrap('table', $full_table, array('class' => array('price-comparison-table')));
  $output = htmlWrap('div', $output, array('id' => 'price_calculator'));

  /***********************
   * Template
   ***********************/
  $template = new HTMLTemplate();
  $template->setTitle('Epoch Converter');
  $template->addCssFilePath('https://fonts.googleapis.com/css?family=Montserrat');
  $template->addCssFilePath('/modules/calculator/calculator.css');
  $template->addJsFilePath('/modules/calculator/calculator.js');
  $template->setMenu(menu());
  $template->setBody($output);

  return $template;
}

function fieldNumber($id, $min = 0)
{
  $field = '';

  // 1
  $attr = array(
    'type' => 'button',
    'value' => '',
    'class' => array('minus', 'operator'),
  );
  $field .= htmlSolo('input', $attr);

  // Value.
  $attr = array(
    'type' => 'textfield',
    'min' => $min,
    'value' => $min,
//    'id' => $id,
    'class' => array('value'),
  );
  $field .= htmlSolo('input', $attr);

  // +
  $attr = array(
    'type' => 'button',
    'value' => '',
    'class' => array('plus', 'operator'),
  );
  $field .= htmlSolo('input', $attr);

  return htmlWrap('div', $field, array('id' => $id, 'class' => array('number-field-wrapper')));
}
