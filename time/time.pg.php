<?php
function timezonePage()
{
    //setup file, use template and add file paths
    $template = new HTMLTemplate();
    $template->setTitle('Epoch Converter');
//  $template->addCssFilePath('/time/time.css');
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
    $timeZones .= htmlWrap("ul", "", array('class' => array('eastern')));
    $timeZones .= htmlWrap("ul", "", array('class' => array('central')));
    $timeZones .= htmlWrap("ul", "", array('class' => array('mountain')));
    $timeZones .= htmlWrap("ul", "", array('class' => array('pacific')));
    $timeZones .= htmlWrap("ul", "", array('class' => array('alaska')));
    $timeZones .= htmlWrap("ul", "", array('class' => array('hawaii')));




    //$body = htmlWrap('p','test .php file html wrap ' . time() . ' ' . $pretty);
    $header = "Epoch Converter";
    $template->setBody(htmlWrap('h1', $header) . $body . $myEpochTime . $timeZones);

    return $template;
}
