<?php

function timeForm()
{
  $form = new Form('time_converter');

  //timestamp
  $field = new FieldText("timestamp", "Timestamp");
  $form->addField($field);

  // Date.
  $field = new FieldDate('date', 'Date');
  $field->setValue('02/01/2023');
  $form->addField($field);

  // Time
  $field = new FieldTime('time', 'Time');
  //$field->setValue('2:00 PM');
  $form->addField($field);

  // Timezone
  $list = getTimeTimezoneList();
  $field = new FieldSelect('timezone', 'Timezone', $list);
  $field->setValue('America/New_York');
  $form->addField($field);


  // Convert button.
  $field = new FieldSubmit('convert-button', 'Convert');
  $form->addField($field);


  /******************
   * Handlers.
   ******************/
  return $form;
}

/**
 * @param string $timezone: string containing iana time zone in this format "America/Chicago"
 * @return string: wrapped html string containing a completed clock based on the timezone given
 */
function timeClock($timezone, $usersInZone)
{
  $output = '';
  $output .= htmlWrap('h2', getTimeTimezoneList($timezone), array('class' => array('header')));

  $face = htmlWrap('div', null, array('class' => array('marking marking-one')));
  $face .= htmlWrap('div', null, array('class' => array('marking marking-two')));
  $face .= htmlWrap('div', null, array('class' => array('marking marking-three')));
  $face .= htmlWrap('div', null, array('class' => array('marking marking-four')));

  $face = htmlWrap('div', $face, array('class' => array('outer-clock-face')));

  //clock hands
  $inner_face = htmlWrap('div', null, array('class' => array('hand hour-hand')));
  $inner_face .= htmlWrap('div', null, array('class' => array('hand min-hand')));
  $inner_face .= htmlWrap('div', null, array('class' => array('hand second-hand')));
  $inner_face = htmlWrap('div', $inner_face, array('class' => array('inner-clock-face')));

  $output .=  htmlWrap('div', $face . $inner_face, array('class' => array('clock')));

  $output .= htmlWrap("div", '01:00:00 PM', array('class' => array('time')));
  $output .= htmlWrap("div", $timezone, array('class' => array('timezone')));

  $userList = '';
  if (is_null($usersInZone)) {
    $userList .= htmlWrap('li', 'None', array('class' => array('user')));
  }
  else {
    foreach ($usersInZone as $user) {
      $userList .= htmlWrap('li', $user, array('class' => array('user')));
    }
  }

  $output .= htmlWrap('ul', $userList, array('class' => array('user-list')));


  $attr = array(
    'id' => array(toMachine($timezone)),
    'class' => array('clock-wrap')
  );
  return htmlWrap('div', $output, $attr);
}

function getTimeTimezoneList($key = FALSE)
{
  $list = array(
    'America/New_York' => 'Eastern',
    'America/Chicago' => 'Central',
    'America/Denver' => 'Mountain',
    //'America/Phoenix' => 'Mountain (Arizona)',
    'America/Los_Angeles' => 'Pacific',
    'America/Anchorage' => 'Alaska',
    'America/Adak' => 'Hawaii',
    //'Pacific/Honolulu' => 'Hawaii (No Daylight Savings)',
  );
  return getListItem($list, $key);
}

function sortUsers()
{
  /*************************
   * User timezone sorting
   *************************/
  $users = getUserList();

  $userHashMap = array();

  foreach($users as $user)
  {
    $currUser = $user['username'];
    $currTimezone = $user['timezone'];

    // If the current timezone key does not exist in the hashmap yet, add it
    if(!array_key_exists($currTimezone, $userHashMap)){
      $userHashMap[$currTimezone] = array();
    }
    // Then add the current user to that timezone map
    $userHashMap[$currTimezone][] = $currUser;
  }
  return $userHashMap;
}