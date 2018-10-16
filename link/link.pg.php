<?php
function linkGenerateForm()
{
  // Submit.
  if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST'))
  {
    try
    {
      if (!isset($_POST) || !array_key_exists('url', $_POST))
      {
        throw new Exception('URL field is required.', EXCEPTION_REQUIRED_FIELD_MISSING);
      }

      if (!filter_var($_POST['url'], FILTER_VALIDATE_URL))
      {
        throw new Exception('URL is not in the correct format. Use format like: https://example.com/example?query=true', EXCEPTION_FIELD_INVALID);
      }

      do
      {
        $code = linkRandomCode();
      } while (getLink($code));

      createLink($code, $_POST['url']);

      GLOBAL $url;
      $url->getPath();
      $link = u('/' . $code, array('absolute' => TRUE));
      $message = 'Your short link is: ';
      $message .= htmlWrap('span', $link, array('class' => array('short-link')));
      $message .= htmlSolo('input', array('type' => 'text', 'value' => $link, 'style' => 'display:none', 'class' => array('short-link-hidden')));
      $message .= htmlSolo('input', array('type' => 'button', 'value' => 'Copy Link', 'class' => array('copy-button')));

      message($message);
    }
    catch(Exception $e)
    {
      message('Error: ' . $e->getMessage());
    }
  }

  $form = new Form('link_generate');

  $field = new FieldText('url', 'URL to Shorten');
  $form->addField($field);

  $field = new FieldSubmit('submit', 'shorten');
  $form->addField($field);

  return $form;
}

function linkListPage()
{
  $template = new HTMLTemplate();
  $template->setTitle('Links');
  $template->setMenu(menu());

  $table = new TableTemplate('link_list');
  $table->setHeader(array('Short', 'Target', 'User', 'Visits', 'Created'));
  $links = getLinkList(getUrlID('page', 1));
  foreach($links as $link)
  {
    $row = array();
    $row[] = u('/' . $link['code'], array('absolute' => TRUE));
    $row[] = sanitizeXss($link['link']);
    $user = getUser($link['user_id']);
    $row[] = sanitize($user['username']);
    $row[] = $link['visits'];
    $row[] = date(DATE_FORM, $link['timestamp']);
    $table->addRow($row);
  }

  $template->setBody(htmlWrap('h1', 'Link List') . $table);

  return $template;
}
