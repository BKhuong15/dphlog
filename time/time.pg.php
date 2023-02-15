<?php
function timezonePage()
{
    //setup file, use template and add file paths
    $template = new HTMLTemplate();
    $template->setTitle('Epoch Converter');
    $template->addCssFilePath('/time/time.css');
    $template->addJsFilePath('/time/time.js');
    $template->setMenu(menu());

    $body = htmlWrap('div', "01/01/1970", array('class' => array('current-date')));

    $myEpochTime = htmlWrap("div", "1234567890", array('class' => array('unix-time')));

    $baseTimeWrap = htmlWrap('div', $body . $myEpochTime, array('class' => array('date-and-epoch')));

    //create new form
    $form = new Form('time_converter');

    $group = 'top_group';
    $form->addGroup($group);

    //date select field
    $field = new FieldDate('date', 'Date');
    $form->addField($field);

    //time select dropdown field
    $field = new FieldTime('time', 'Datetime');
    $form->addField($field);

    //dict of iana timezones -> general zone, matches with one in js file
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

    //select timezone form button
    $field = new FieldSelect('timezone', 'Timezone', $list);
    $field->setValue('America/New_York');
    $form->addField($field);

    //convert times button
    $field = new FieldSubmit('convert-button', 'Convert');
    $field->setGroup($group);
    $form->addField($field);

    //reset times to user's current time button
    $field = new FieldSubmit('now-button', 'Now');
    $field->setGroup($group);
    $form->addField($field);

    $formSection = htmlWrap('div', $form, array('class' => array('formSection')));

    $fullClockHTMLWrap = '';

    //create each clock based on the given dictionary of timezones
    foreach ($list as $key => $value)
    {
      $fullClockHTMLWrap .= createClock($key, $value);
    }

    $clockSection = htmlWrap('div', $fullClockHTMLWrap, array('class' => array('clockSection')));
    $clockContainter = htmlWrap('div', $clockSection, array('class' => array('clock-container')));

    $header = 'Epoch Converter';

    $clearFix = htmlWrap('div', 'clearfix', array('class' => array('clearfix')));
    $template->setBody(htmlWrap('h1', $header) . $baseTimeWrap . $formSection . $clockContainter . $clearFix);
    return $template;
}

/*
 * @param $ianaTimeZoneName: string containing iana time zone in this format "America/Chicago"
 * @param $abbreviatedTimeZoneName: string containing the abbreviated version, "central"
 * @return string: wrapped html string containing a completed clock based on the timezone given
 */
function createClock($ianaTimeZoneName, $abbreviatedTimeZoneName)
{
    $output = '';
    $output .= htmlWrap('h2', $abbreviatedTimeZoneName, array('class' => array('header')));

    $face = htmlWrap('div', null, array('class' => array('marking marking-one')));
    $face .= htmlWrap('div', null, array('class' => array('marking marking-two')));
    $face .= htmlWrap('div', null, array('class' => array('marking marking-three')));
    $face .= htmlWrap('div', null, array('class' => array('marking marking-four')));

    $face = htmlWrap('div', $face, array('class' => array('outer-clock-face')));

    //clock hands
    $hands = htmlWrap('div', null, array('class' => array('hand hour-hand')));
    $hands .= htmlWrap('div', null, array('class' => array('hand min-hand')));
    $hands .= htmlWrap('div', null, array('class' => array('hand second-hand')));
    $innerFace = htmlWrap('div', $hands, array('class' => array('inner-clock-face')));

    $output .=  htmlWrap('div', $face . $innerFace, array('class' => array('clock')));

    $output .= htmlWrap("div", '01:00:00 PM', array('class' => array('time')));
    $output .= htmlWrap("div", $ianaTimeZoneName, array('class' => array('timezone')));

    $attr = array(
      'id' => array(toMachine($ianaTimeZoneName)),
      'class' => array('clock-wrap')
    );
    return htmlWrap("div", $output, $attr);
}

function getCurrentTime() {
  $time = time();
  return array('time' => $time);
}