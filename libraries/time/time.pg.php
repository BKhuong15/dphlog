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
  $topClock .= htmlWrap('div', 'test', array('id' => array('clock-state')));
  $output .= htmlWrap('div', $topClock, array('class' => array('date-and-epoch')));

  //Reset/Now button.
  $nowButton = new FieldSubmit('now-button', 'Now');
  $output .= htmlWrap('div', $nowButton, array('class' => array('right-form')));

  //Wrap Clock, Form, and Now button.
  $output = htmlWrap('div', $output, array('class' => array('top-section')));

  // Clocks.
  $clocks = '';
  $timezones = getTimeTimezoneList();
  $userHashMap = sortUsers();

  // For each timezone, add a list of users in each timezone under each clock.
  foreach ($timezones as $key => $value)
  {
    $usersInZone = $userHashMap[$key];
    $clocks .= timeClock($key, $usersInZone);
  }
  $clocks = htmlWrap('div', $clocks, array('class' => array('clockSection')));
  $output .= htmlWrap('div', $clocks, array('class' => array('clock-container')));


  /*****************************
   * Return final html template
   *****************************/
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
