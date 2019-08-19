<?php
function jsonFormatterPage()
{
  $template = new HTMLTemplate();
  $template->setTitle('DPH Log');
  $template->addCssFilePath('/json_formatter/json_formatter.css');
  $template->addJsFilePath('/json_formatter/json_formatter.js');

  $template->setMenu(menu());


  $form = new Form('json_formatter');

  $field = new FieldTextarea('old', 'JSON String');
  $form->addField($field);

  $field = new FieldSubmit('submit', 'Submit');
  $form->addField($field);

  $body = $form . htmlWrap('pre', '', array('class' => array('json_formatter_output')));
  $template->setBody(htmlWrap('h1', 'JSON Formatter') . $body);
  return $template;
}
