<?php
function jsonFormatterPage()
{
  $template = new HTMLTemplate();
  $template->setTitle('DPH Log');
  $template->addCssFilePath('/json_formatter/json_formatter.css');
  $template->addJsFilePath('/json_formatter/json_formatter.js');

  $template->setMenu(menu());


  $form = new Form('json_formatter');

  /***********************
   * top group.
   ***********************/
  $group = 'top_group';
  $form->addGroup($group);

  // old.
  $field = new FieldTextarea('old', 'JSON String');
  $field->setGroup($group);
  $form->addField($field);

  $field = new FieldDate('date', 'Date');
//  $field->setValue('01/08/2023');
  $form->addField($field);

  $field = new FieldTime('time', '<none>');
//  $field->setValue('01/08/2023');
  $form->addField($field);

  /***********************
   * submit_group
   ***********************/
  $group = 'submit_group';
  $form->addGroup($group);

  // submit.
  $field = new FieldSubmit('submit', 'Submit');
  $field->setGroup($group);
  $form->addField($field);

  /***********************
   * handlers.
   ***********************/
  $body = $form . htmlWrap('pre', '', array('class' => array('json_formatter_output')));
  $template->setBody(htmlWrap('h1', 'JSON Formatter') . $body);
  return $template;
}
