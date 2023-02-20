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
  $output .= htmlWrap('div', timeForm(), array('class' => array('formSection')));

  // Top clock.
  $topClock = htmlWrap('div', "01/01/1970", array('class' => array('current-date')));
  $topClock .= htmlWrap("div", "1234567890", array('class' => array('unix-time')));
  $output .= htmlWrap('div', $topClock, array('class' => array('date-and-epoch')));

  //Reset/Now button.
//  $nowButton = new FieldSubmit('now-button', 'Now');
  $nowButtonAjax = new FieldSubmit('now-button-ajax', 'Now (Ajax)');
  $output .= htmlWrap('div', $nowButtonAjax, array('class' => array('right-form')));

  //Wrap Clock, Form, and Now button.
  $output = htmlWrap('div', $output, array('class' => array('top-section')));

  // Clocks.
  $clocks = '';
  $timezones = getTimeTimezoneList();
  foreach ($timezones as $key => $value)
  {
    $clocks .= timeClock($key);
  }
  $clocks = htmlWrap('div', $clocks, array('class' => array('clockSection')));
  $output .= htmlWrap('div', $clocks, array('class' => array('clock-container')));

  // Done.
  $template->setBody($header . $output);
  return $template;
}

function timeAjax()
{
  $response = array(
    'status' => true,
    'currentServerTime' => time(),
    'localTimezone' => date_default_timezone_get(),
  );
  die(json_encode($response));
}
