<?php
function phpUnserializePage()
{
  $template = new HTMLTemplate();
  $template->setTitle('Unserialize');
  $template->addCssFilePath('/modules/unserialize/unserialize.css');
  $template->addJsFilePath('/modules/unserialize/unserialize.js');

  $template->setMenu(menu());


  $form = new Form('unserialize');

  $field = new FieldCheckbox('base64-check', 'Base64 Decode');
  $form->addField($field);

  $field = new FieldCheckbox('unserialize-check', 'Unserialize');
  $form->addField($field);

  $field = new FieldTextarea('string-input', 'JSON String Input');
  $form->addField($field);

  $field = new FieldSubmit('submit', 'Submit');
  $form->addField($field);


//
//  if ($_SERVER['REQUEST_METHOD'] === 'POST')
//  {
//
//    $user_input = $_POST['string-input'];
//    $base64_encoded = $_POST['base64-check'];
//    $serialized = $_POST['unserialize-check'];
//
//    echo $base64_encoded;
//  }
//
//  unserialize();

  $body = $form . htmlWrap('pre', '', array('class' => array('json_formatter_output')));
  $template->setBody(htmlWrap('h1', 'Unserialize') . $body);
  return $template;
}

function phpUnserializeAjax()
{
  die('hello');
  $response = array(
    'status' => TRUE,
    'data' => '',
  );
  try
  {
    if (!isset($_GET['operation']))
    {
      throw new Exception('operation parameter is required.');
    }
    $operation = $_GET['operation'];
    switch($operation)
    {
      case 'view':
      {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        {
          throw new Exception('Expected post method.');
        }
        if (!isset($_POST['data']))
        {
          throw new Exception('Where is my data?');
        }

        $base_64 = isset($_POST['base_64']) ? $_POST['base_64'] : 1;
        $serialized = isset($_POST['serialized']) ? $_POST['serialized'] : 1;
        $raw_input = isset($_POST['raw_input']) ? $_POST['raw_input'] : 1;

        // processing here!!
        $response['data'] = 'unserlialize stuff';

      }
      default:
      {
        throw new Exception('Unknown operation.');
      }
    }
  }
  catch (Exception $e)
  {
    $response['status'] = FALSE;
    $response['data'] = $e->getMessage();
  }
  header('Content-Type: application/json');
  echo json_encode($response, JSON_PARTIAL_OUTPUT_ON_ERROR);
  die();


}