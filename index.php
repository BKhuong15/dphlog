<?php
include 'libraries/bootstrap.inc.php';

// Database.
if (!file_exists(DB_PATH))
{
  die('Database file does not exist. Visit /install.php to create a new one.');
}
if (!is_writable(dirname(DB_PATH)))
{
  die('Database file is not writable. Edit the file permission to give apache read/write access.');
}
GLOBAL $db;
$db = new SQLite(DB_PATH);

// Path.
GLOBAL $url;
$url = new URL();
$path = $url->getPath();

$registry = array(
  '/' => 'home',
  'home' => 'home',
  'user' => 'userUpsertForm',
  'users' => 'userListPage',
  'login' => 'userLoginForm',
  'unknown' => 'unknown',
  'logout' => 'userLogout',
  'links' => 'linkListPage'
);

if (!array_key_exists($path, $registry))
{
  $link = getLink($path);

  if ($link)
  {
    redirect($link);
  }
  else
  {
    $path = 'unknown';
  }
}

session_name('dphmus');
session_start();

GLOBAL $logged_in_user;
if (!$logged_in_user && $path !== 'unknown')
{
  $path = 'login';
}

// Retrieve body.
echo $registry[$path]();

/******************************************************************************
 *
 * Pages.
 *
 ******************************************************************************/
function home()
{
  $template = new HTMLTemplate();
  $template->setTitle('DPH URL Minify');
  $template->addCssFilePath('/link/link.css');
  $template->addJsFilePath('/link/link.js');

  $template->setMenu(menu());
  $template->setBody(htmlWrap('h1', 'Welcome to Daniel P Henry\'s URL Minify Site') . linkGenerateForm());

  return $template;
}

function unknown()
{
  header('HTTP/1.1 404 Not Found');

  $template = new HTMLTemplate();
  $template->setTitle('Unknown');
  $template->setBody(htmlWrap('h1', 'Page Not Found'));

  return $template;
}

function menu()
{
  $output = '';

  // Users
  $output .= htmlWrap('a', 'Home', array('href' => '/home'));

  // Users
  $output .= htmlWrap('a', 'Users', array('href' => '/users'));

  // Links.
  $output .= htmlWrap('a', 'Links', array('href' => '/links'));

  // Username.
  GLOBAL $logged_in_user;
  $output .=htmlWrap('a', $logged_in_user['username'], array('href' => '/user?user_id=' . $logged_in_user['id']));
  $submenu = new ListTemplate('ul');
  $submenu->addListItem(htmlWrap('a', 'Logout', array('href' => '/logout')));
  $output .= $submenu;

  $attr = array('id' => 'menu', 'class' => array('menu'));
  $output = htmlWrap('div', $output, $attr);
  return $output;
}
