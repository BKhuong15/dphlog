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

  // Top clock.
  $output = htmlWrap('div', "01/01/1970", array('class' => array('current-date')));
  $output .= htmlWrap("div", "1234567890", array('class' => array('unix-time')));
  $output = htmlWrap('div', $output, array('class' => array('date-and-epoch')));

  // Form.
  $output .= htmlWrap('div', timeForm(), array('class' => array('formSection')));

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
    'status' => TRUE,
    'data' => time(),
  );
  die(json_encode($response));
}
