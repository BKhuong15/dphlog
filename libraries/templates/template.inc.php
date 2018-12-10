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
  protected $table_attrs = array();
  protected $row_attrs = array();

  /**
   * Standard constructor. Pass the path to the template file.
   *
   * @param string $id - The html id of the table.
   */
  public function __construct($id = '')
  {
    if ($id)
    {
      $this->table_attrs['id'] = $id;
    }
  }

  public function setHeader($header)
  {
    $this->header = $header;
    return $this;
  }
//  public function addRows($rows, $attrs)
//  {
//    $this->rows = $rows;
//    $this->row_attrs = $attrs;
//    return $this;
//  }
  public function addRow($row, $attr = array())
  {
    $this->rows[] = $row;
    $this->row_attrs[] = $attr;
    return $this;
  }
  public function __toString()
  {
    $output = '';

    // Generate table.
    $output .= $this->generateHTMLHeader();
    $output .= $this->generateHTMLRows();

    $output = htmlWrap('table', $output, $this->table_attrs);
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
    $count = 0;
    $row_attr = reset($this->row_attrs);
    foreach ($this->rows as $row)
    {
      $row_output = '';
      $count = 1;
      foreach ($row as $cell)
      {
        $cell_attr = array('class' => array('column-' . $count));
        $row_output .= htmlWrap('td', $cell, $cell_attr);
        $count++;
      }

      $class = isset($row_attr['class']) ? $row_attr['class'] : array();
      assert(is_array($class), 'Class attr must be an array when passed to table template.');
      $class[] = $count % 2 === 0 ? 'even' : 'odd';
      $row_attr['class'] = $class;
      $output .= htmlWrap('tr', $row_output, $row_attr);
      $row_attr = next($this->row_attrs);
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