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

//  $usersInZone = $userHashMap['America/Anchorage'];
////  print_r($usersInZone) ;
////  foreach ($usersInZone as $u){
////    echo $u;
////  }
//  $clocks .= timeClock('America/Chicago', $usersInZone);
  foreach ($timezones as $key => $value)
  {
    $usersInZone = $userHashMap[$key];
    //echo $usersInZone;
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

//function sortUsers($users)
//{
//  $userHashMap = array();
//
//  // Foreach user in the given array...
//  foreach($users as $user){
//    $timezone = $user['timezone'];
//    $user = $user['user'];
//
//    // If the current timezone key does not exist in the hashmap yet, add it
//    if(!array_key_exists($timezone, $userHashMap)){
//      $userHashMap[$timezone] = array();
//    }
//
//    // Then add the current user to that timezone map
//    $userHashMap[$timezone][] = $user;
//  }
//
//  // Print out the sorted users
//  foreach ($userHashMap as $timezone => $users) {
//    echo "Users in timezone $timezone:\n";
//    foreach ($users as $user) {
//      echo "- $user\n";
//    }
//    echo "\n";
//  }
//
////return $userHashMap;
//}