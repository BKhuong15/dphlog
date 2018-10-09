<?php

/******************************************************************************
 *
 * Constants
 *
 ******************************************************************************/
define('DEFAULT_PAGER_SIZE', 100);

define('SEC_MIN', 60);
define('SEC_HOUR', 3600);
define('SEC_DAY', 86400);
define('SEC_WEEK', 604800);
define('SEC_MONTH', 2419200);
define('SEC_YEAR', 31449600);

define('EXCEPTION_NOT_FOUND', 1);
define('EXCEPTION_PERMISSION_DENIED', 2);
define('EXCEPTION_EXPIRED', 3);
define('EXCEPTION_REQUIRED_FIELD_MISSING', 4);
define('EXCEPTION_FIELD_INVALID', 5);

define('DATE_FORM', 'm/d/Y');


/******************************************************************************
 *
 * DEBUG Helpers
 *
 ******************************************************************************/
function debugPrint($variable, $name = '', $die = TRUE)
{
  echo '<h1 class="debug">' . $name . '</h1>';
  echo '<pre>';
  print_r($variable);
  echo '</pre>';
  if ($die)
  {
    die();
  }
}

/******************************************************************************
 *
 * URL Helpers
 *
 ******************************************************************************/
class URL
{
  protected $path = '';
  protected $query = array();
  protected $fragment = '';

  function __construct($url = FALSE)
  {
    if (!$url)
    {
      $url = $_SERVER['REQUEST_URI'];
    }

    // Home path.
    if ($url == '/')
    {
      $this->path = '/';
      return;
    }

    $start = strpos($url, '/') + 1;
    $end = strpos($url, '?');

    // No query string. Only the path is defined.
    if ($end === FALSE)
    {
      $this->path = substr($url, $start);
      return;
    }
    $this->path = substr($url, $start, $end - 1);

    // Build the query string.
    $query = substr($url, $end + 1);
    $query = explode('&', $query);
    foreach ($query as $parameter)
    {
      $parts = explode('=', $parameter);
      $this->query[$parts[0]] = isset($parts[1]) ? urldecode($parts[1]) : FALSE;
    }
  }

  function getPath()
  {
    return $this->path;
  }

  function getQuery()
  {
    return $this->query;
  }
}

function getUrlID($name, $default = FALSE)
{
  if (!isset($_GET[$name]) || !is_numeric($_GET[$name]))
  {
    return $default;
  }

  return abs($_GET[$name]);
}

function redirect($path = FALSE, $statusCode = '303')
{
  if ($path === FALSE)
  {
    GLOBAL $url;
    $path = $url->getPath();
  }
  header('Location: ' . $path, TRUE, $statusCode);
  die();
}

function u($target = '', $options = array())
{
  GLOBAL $url;

  $link = '';
  if (isset($options['external']))
  {
    $link .= $target;
  }
  else
  {
    // If the url should be absolute include the protocol and domain.
    if (isset($options['absolute']))
    {
      $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
      $protocol = strLeft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/") . $s;
      $link .= $protocol . "://" . $_SERVER['SERVER_NAME'];
    }

    // Build the internal link.
    $path = $url->getPath();
    $relative = strpos($target, '/') !== 0;
    if ($relative && !empty($path))
    {
      $link .= '/' . $path;
    }
    if (!empty($target))
    {
      if ($relative)
      {
        $link .= '/';
      }
      $link .= $target;
    }
  }

  // Add query string.
  $preserve_query = array();
  if (isset($options['preserve_query']))
  {
    $preserve_query = $url->getQuery();
  }

  $new_query = array();
  if (isset($options['query']))
  {
    $new_query = $options['query'];
  }

  $query = array_merge($preserve_query, $new_query);
  if (!empty($query))
  {
    $link .= '?';
    $first = TRUE;
    foreach($query as $key => $value)
    {
      if (!$first)
      {
        $link .= '&';
      }
      if ($value !== FALSE)
      {
        $link .= urlencode($key) . '=' . urlencode($value);
        $first = FALSE;
      }
    }
  }

  // Add fragment.
  if (isset($options['fragment']))
  {
    $link .= $options['fragment'];
  }

  return $link;
}

/******************************************************************************
 *
 * HTML Helpers
 *
 ******************************************************************************/
function buildAttr($attr)
{
  assert(is_array($attr), 'Attributes passed to buildAttr should be an array().');
  $attr_string = '';
  foreach ($attr as $name => $value)
  {
    if (is_numeric($name))
    {
      continue;
    }
    if (is_array($value))
    {
      $value = implode(' ', $value);
    }
    $attr_string .= ' ' . $name . '="' . $value . '"';
  }

  return $attr_string;
}

function htmlWrap($tag, $content, $attr = array())
{
  return '<' . $tag . buildAttr($attr) . '>' . $content . '</' . $tag . '>';
}

function htmlSolo($tag, $attr = array())
{
  return '<' . $tag . buildAttr($attr) . '>';
}

function stringToAttr($string)
{
  $replace = array(' ', '_');
  return strtolower(str_replace($replace, '-', $string));
}

function optionList($list, $selected = FALSE)
{
  $output = '';
  foreach ($list as $key => $value)
  {
    $attr = array(
      'value' => $key,
    );
    if ($key == $selected)
    {
      $attr['selected'] = 'selected';
    }
    $output .= htmlWrap('option', $value, $attr);
  }
  return $output;
}

function strLeft($s1, $s2)
{
  return substr($s1, 0, strpos($s1, $s2));
}

/*****************************************************************************
 *
 * Other Helpers
 *
 ******************************************************************************/
function generateRandomString($length = 32)
{
  return base64_encode(openssl_random_pseudo_bytes($length));
}

