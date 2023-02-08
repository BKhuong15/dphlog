<?php
function timezonePage()
{
    //setup file, use template and add file paths
    $template = new HTMLTemplate();
    $template->setTitle('Epoch Converter');
    $template->addCssFilePath('/time/time.css');
    $template->addJsFilePath('/time/time.js');
    $template->setMenu(menu());

//  $code_snippet = htmlWrap('script', $js);
    $now = new DateTime();
    $pretty = $now->format('m/d/Y h:i a e');
    $body = '';
//    $body .= htmlWrap('h1', 'It is time!');
    $body .= htmlWrap('div', "", array('class' => array('human-time')));
    //$body .= htmlWrap('div', time(), array('class' => array('unix-time')));

    $myEpochTime = htmlWrap("div", "", array('class' => array('unix-time')));

    $timeZones = "";
    $timeZones .= htmlWrap("li", "", array('class' => array('eastern')));
    $timeZones .= htmlWrap("li", "", array('class' => array('central')));
    $timeZones .= htmlWrap("li", "", array('class' => array('mountain')));
    $timeZones .= htmlWrap("li", "", array('class' => array('pacific')));
    $timeZones .= htmlWrap("li", "", array('class' => array('alaska')));
    $timeZones .= htmlWrap("li", "", array('class' => array('hawaii')));
    $timeZones .= htmlWrap("ul", "", array('class' => array('timeZoneList')));

    //create new form
    $form = new Form('json_formatter');

    $group = 'top_group';
    $form->addGroup($group);

    //date select field
    $field = new FieldDate('date', 'Date');
//  $field->setValue('01/08/2023');
    $form->addField($field);

    //time select dropdown field
    $field = new FieldTime('time', 'Datetime');
//  $field->setValue('01/08/2023');
    $form->addField($field);

    $list = array(
        'America/New_York' => 'Eastern',
        'America/Chicago' => 'Central',
        'America/Denver' => 'Mountain',
        'America/Phoenix' => 'Mountain (Arizona)',
        'America/Los_Angeles' => 'Pacific',
        'America/Anchorage' => 'Alaska',
        'America/Adak' => 'Hawaii',
        'Pacific/Honolulu' => 'Hawaii (No Daylight Savings)',
    );

    $field = new FieldSelect('timezone', 'Timezone', $list);
    $field->setValue('America/Chicago');
    $form->addField($field);
//    $formSection = htmlWrap('div', $form, array('class' => array('formSection')));

    $fullClockHTMLWrap = createClock('eastern_standard', 'EST') . createClock('central', 'cst');


    $clockSection = htmlWrap('ul', $fullClockHTMLWrap, array('class' => array('clockSection')));

    //$body = htmlWrap('p','test .php file html wrap ' . time() . ' ' . $pretty);
    $header = 'Epoch Converter';
    $template->setBody(htmlWrap('h1', $header) . $body . $myEpochTime . $timeZones . $form . $clockSection);
    return $template;
}

function createClock($timezone, $timezoneLabel)
{
    //clock face
    $labelWrap = htmlWrap('h2', $timezoneLabel, array('class' => array('timezone-header')));

    $face = htmlWrap('div',NULL, array('class' => array('marking marking-one')));
    $face .= htmlWrap('div',NULL, array('class' => array('marking marking-two')));
    $face .= htmlWrap('div',NULL, array('class' => array('marking marking-three')));
    $face .= htmlWrap('div',NULL, array('class' => array('marking marking-four')));

    $face = htmlWrap('div', $face, array('class' => array('outer-clock-face')));

    //clock hands
    $hands = htmlWrap('div', NULL, array('class' => array('hand hour-hand')));
    $hands .= htmlWrap('div', NULL, array('class' => array('hand min-hand')));
    $hands .= htmlWrap('div', NULL, array('class' => array('hand second-hand')));
    $innerFace = htmlWrap('div', $hands, array('class' => array('inner-clock-face')));

    $fullClockHTMLWrap = $labelWrap . htmlWrap('div',  $face . $innerFace, array('id' => array('timezone_' . $timezone), 'class' => array('clock')));

    //$fullClockHTMLWrap .= htmlWrap("li",$fullClockHTMLWrap, Null);

    return $fullClockHTMLWrap;
}
