<?php

/******************************************************************************
 *
 * Page Templates.
 *
 ******************************************************************************/

/**
 * Class HTMLTemplate
 */
class HTMLTemplate
{
  protected $title = '';
  protected $css_file_paths = array();
  protected $js_file_paths = array();
  protected $body_attr = array();
  protected $menu = FALSE;
  protected $messages = array();
  protected $body = '';

  function __construct()
  {
    $this->addCssFilePath('/libraries/templates/page.css');
    $this->addCssFilePath('/libraries/templates/form.css');
    $this->addJsFilePath('https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js');
    $this->addJsFilePath('/libraries/templates/global.js');
  }

  function __toString()
  {
    if (isset($_SESSION) && isset($_SESSION['messages']))
    {
      $this->messages = $_SESSION['messages'];
      $_SESSION = array();
    }
    extract(get_object_vars($this));
    ob_start();

    include ROOT_PATH . '/libraries/templates/html.tpl.php';

    return ob_get_clean();
  }

  function setTitle($title)
  {
    $this->title = $title;
  }
  function addCssFilePath($path)
  {
    $this->css_file_paths[] = $path;
  }
  function addJsFilePath($path)
  {
    $this->js_file_paths[] = $path;
  }
  function setBodyAttr($attr)
  {
    $this->body_attr = $attr;
  }
  function getBodyAttr()
  {
    return $this->body_attr;
  }
  function setMenu($menu)
  {
    $this->menu = $menu;
    return $this;
  }
  function setBody($body)
  {
    $this->body = $body;
    return $this;
  }
}

/******************************************************************************
 *
 * HTML Structures
 *
 ******************************************************************************/

/**
 * Class TableTemplate
 */
Class TableTemplate
{
  // Primary values.
  protected $header = array();
  protected $rows = array();

  protected $attr = array();

  /**
   * Standard constructor. Pass the path to the template file.
   */
  public function __construct($id = FALSE)
  {
    if ($id)
    {
      $attr['id'] = $id;
    }
  }

  public function setHeader($header)
  {
    $this->header = $header;
    return $this;
  }
  public function addRows($rows)
  {
    $this->rows = $rows;
    return $this;
  }
  public function addRow($row, $attr = array())
  {
    $this->rows[] = $row;
    return $this;
  }
  public function setAttr($name, $value)
  {
    $this->attr[$name] = $value;
  }
  public function __toString()
  {
    $output = '';

    // Generate table.
    $output .= $this->generateHTMLHeader();
    $output .= $this->generateHTMLRows();

    $output = htmlWrap('table', $output, $this->attr);
    return $output;
  }

  private function generateHTMLHeader()
  {
    // Header columns.
    $output = '';
    $count = 1;
    foreach ($this->header as $label)
    {
      $attr = array('class' => array('column-' . $count));
      $output .= htmlWrap('th', $label, $attr);
      $count++;
    }

    // Header wrappers.
    $output = htmlWrap('tr', $output);
    $output = htmlWrap('thead', $output);
    return $output;
  }

  private function generateHTMLRows()
  {
    $output = '';
    foreach ($this->rows as $row)
    {
      $row_output = '';
      $count = 1;
      foreach ($row as $cell)
      {
        $attr = array('class' => array('column-' . $count));
        $row_output .= htmlWrap('td', $cell, $attr);
        $count++;
      }
      $output .= htmlWrap('tr', $row_output);
    }
    $output = htmlWrap('tbody', $output);
    return $output;
  }
}

Class ListTemplate
{
  protected $list;
  protected $list_type;
  protected $attr;
  protected $pointer = 0;

  public function __construct($list_type = 'ol')
  {
    $this->list_type = $list_type;
  }

  public function setListType($list_type)
  {
    $this->list_type = $list_type;
  }

  public function addListItem($item, $attr = array())
  {
    $this->list[$this->pointer] = $item;
    $this->attr[$this->pointer] = $attr;
    $this->pointer++;
  }

  public function setAttr($attr)
  {
    $this->attr = array_merge($this->attr, $attr);
  }

  public function __toString()
  {
    $output = '';

    for($k = 0; $k < $this->pointer; $k++)
    {
      $list_item = $this->list[$k];
      $attr = $this->attr[$k];
      $output .= htmlWrap('li', $list_item, $attr);
    }

    $output = htmlWrap($this->list_type, $output, $attr);
    return $output;
  }

}