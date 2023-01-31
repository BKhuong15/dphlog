<?php
function phpUnserializePage()
{
  $template = new HTMLTemplate();
  $template->setTitle('DPH Log');
  $template->addCssFilePath('/php_unserialize/php_unserialize.css');

  $template->setMenu(menu());


  $form = new Form('php_unserialize');

  $field = new FieldTextarea('old', 'JSON String');
  $form->addField($field);

  $field = new FieldSubmit('submit', 'Submit');
  $form->addField($field);

  $body = $form . htmlWrap('pre', '', array('class' => array('php_unserialize_output')));
  $template->setBody(htmlWrap('h1', 'JSON Formatter') . $body);
  return $template;
}
