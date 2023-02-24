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
  $topClock = htmlWrap('div', '01/01/1970', array('class' => array('current-date')));
  $topClock .= htmlWrap('div', '1234567890', array('class' => array('unix-time')));
  $topClock .= htmlWrap('div', '', array('id' => array('clock-state')));
  $output .= htmlWrap('div', $topClock, array('class' => array('date-and-epoch')));

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
  $template->setBody($header . $output);
  return $template;
}
