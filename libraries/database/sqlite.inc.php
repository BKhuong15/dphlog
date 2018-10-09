<?php

class SQLite extends Database
{
  const PLACEHOLDER_TOKEN = ':';

  function __construct($db_path, $username = '', $password = '')
  {
//    if (!file_exists($db_path))
//    {
//      die('No such file: ' . $db_path);
//    }

//    if (!is_writable($db_path))
//    {
//      die($db_path . ' is not writable.');
//    }

    $opt = array(
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => TRUE,
    );

    $connect_string = 'sqlite:' . $db_path;
    $this->db = new PDO($connect_string, $username, $password, $opt);
  }

  function select(SelectQuery $query)
  {
    $sql  = '';
    $sql .= 'SELECT';
    foreach ($query->getFields() as $alias => $details)
    {
      $sql .= ' ' . $details['table_alias'] . '.' . $details['name'] . ' AS ' . $alias . ',';
    }
    $sql = trim($sql, ',');

    // Add tables.
    $sql .= $this->_buildJoins($query, $query->getTables());

    // Where.
    if ($query->getConditions())
    {
      $sql .= ' WHERE ' . $this->_buildConditionGroup($query);
    }

    // Order by.
    if ($query->getOrders())
    {
      $sql .= ' ORDER BY';
      foreach ($query->getOrders() as $alias => $dir)
      {
        $sql .= ' ' . $alias . ' ' . $dir . ',';
      }
    }
    $sql = trim($sql, ',');

    // Pager
    if ($query->getPage())
    {
      $sql .= ' LIMIT ' . $query->getPageSize();
      $sql .= ' OFFSET ' . (($query->getPageSize() * $query->getPage()) - $query->getPageSize());
    }

    $prepared_statement = $this->db->prepare($sql);
    $prepared_statement->execute($query->getValues());
    return $prepared_statement->fetchAll(PDO::FETCH_ASSOC);
  }

  function insert(InsertQuery $query)
  {
    $sql = '';
    $sql .= 'INSERT INTO '  . key($query->getTables()) . ' (';
    foreach ($query->getFields() as $name => $field)
    {
      $sql .= ' ' . $name . ',';
    }
    $sql = trim($sql, ',');

    $sql .= ') VALUES (';
    foreach ($query->getFields() as $name => $field)
    {
      $placeholder = ':' . $field['table_alias'] . '_' . $field['field_alias'];
      $sql .= ' ' . $placeholder . ',';
      $query->addValue($placeholder, $field['value']);
    }
    $sql = trim($sql, ',');

    $sql .= ')';

    $prepared_statement = $this->db->prepare($sql);
    $prepared_statement->execute($query->getValues());
    return $this->db->lastInsertId();
  }

  function update(UpdateQuery $query)
  {
    $sql = '';
    $sql .= 'UPDATE '  . key($query->getTables()) . ' SET';
    foreach ($query->getFields() as $name => $field)
    {
      $placeholder = ':' . $field['table_alias'] . '_' . $field['field_alias'];
      $sql .= ' ' . $name . ' = ' . $placeholder . ',';
      $query->addValue($placeholder, $field['value']);
    }
    $sql = trim($sql, ',');

    // Where.
    if ($query->getConditions())
    {
      $sql .= ' WHERE ' . $this->_buildConditionGroup($query);
    }

    $prepared_statement = $this->db->prepare($sql);
    $prepared_statement->execute($query->getValues());
  }

  function delete(DeleteQuery $query)
  {
    $sql = '';
    $sql .= 'DELETE FROM '  . key($query->getTables());

    // Where.
    if ($query->getConditions())
    {
      $sql .= ' WHERE ' . $this->_buildConditionGroup($query);
    }

    $prepared_statement = $this->db->prepare($sql);
    $prepared_statement->execute($query->getValues());
  }

  // Structure
  function create(CreateQuery $query)
  {
    $primary_key = array();
    foreach($query->getFields() as $name => $value)
    {
      if (array_search('P', $value['flags']) !== FALSE)
      {
        $primary_key[] = $name;
      }
    }
    $key_count = count($primary_key);

    $sql = '';
    $sql .= 'CREATE TABLE `'  . key($query->getTables()) . '` (';

    foreach ($query->getFields() as $name => $value)
    {
      $sql .= ' `' . $name . '` ' . $value['type'];
      foreach ($value['flags'] as $flag)
      {
        if ($flag == 'N')
        {
          $sql .= ' NOT NULL';
        }
        elseif ($flag == 'P' && $key_count == 1)
        {
          $sql .= ' PRIMARY KEY';
        }
        elseif ($flag == 'A')
        {
          $sql .= ' AUTOINCREMENT';
        }
        elseif ($flag == 'U')
        {
          $sql .= ' UNIQUE';
        }
      }

      if ($value['default'] !== FALSE)
      {
        $sql .= ' DEFAULT ' . $value['default'];
      }
      $sql .= ',';
    }
    if ($key_count > 1)
    {
      $sql .= ' PRIMARY KEY(';
      foreach($primary_key as $key)
      {
        $sql .= '`' . $key . '` ' . ', ';
      }
      $sql = trim($sql, ', ');
      $sql .= ')';
    }
    $sql = trim($sql, ',');

    $sql .= ')';

    $prepared_statement = $this->db->prepare($sql);
    $prepared_statement->execute($query->getValues());
  }

  /***************************
   * Helpers
   ***************************/

  protected function _buildConditionGroup(Query $query, $group_name = 'default', $type = QueryConditionGroup::GROUP_AND)
  {
    $conditions = array();
    foreach($query->getConditions() as $condition)
    {
      if ($condition->getGroup() == $group_name)
      {
        $conditions[] = $this->_buildCondition($query, $condition);
      }
    }

    foreach($query->getConditionGroups() as $subgroup_name => $condition_group)
    {
      if ($condition_group->getParent() == $group_name)
      {
        $conditions[] = '(' . $this->_buildConditionGroup($query, $subgroup_name, $condition_group->getType()) . ')';
      }
    }

    $join = ' AND ';
    if ($type == QueryConditionGroup::GROUP_OR)
    {
      $join = ' OR ';
    }
    return implode($join, $conditions);
  }

  protected function _buildConditionGroupTable(Query $query, QueryTable $table, $group_name = 'default', $type = QueryConditionGroup::GROUP_AND)
  {
    $conditions = array();
    foreach($table->getConditions() as $condition)
    {
      if ($condition->getGroup() == $group_name)
      {
        $conditions[] = $this->_buildCondition($query, $condition);
      }
    }

    foreach($table->getConditionGroups() as $subgroup_name => $condition_group)
    {
      if ($condition_group->getParent() == $group_name)
      {
        $conditions[] = '(' . $this->_buildConditionGroupTable($query, $table, $subgroup_name, $condition_group->getType()) . ')';
      }
    }

    $join = ' AND ';
    if ($type == QueryConditionGroup::GROUP_OR)
    {
      $join = ' OR ';
    }
    return implode($join, $conditions);
  }

  protected function _buildCondition(Query $query, QueryCondition $condition)
  {
    $sql = $condition->getTable() . '.' . $condition->getField();
    switch($condition->getComparison())
    {
      case QueryCondition::COMPARE_EQUAL:
      {
        $sql .= ' =';
        break;
      }
      case QueryCondition::COMPARE_NOT_EQUAL:
      {
        $sql .= ' !=';
        break;
      }
      case QueryCondition::COMPARE_LESS_THAN:
      {
        $sql .= ' <';
        break;
      }
      case QueryCondition::COMPARE_LESS_THAN_EQUAL:
      {
        $sql .= ' <=';
        break;
      }
      case QueryCondition::COMPARE_GREATER_THAN:
      {
        $sql .= ' >';
        break;
      }
      case QueryCondition::COMPARE_GREATER_THAN_EQUAL:
      {
        $sql .= ' >=';
        break;
      }
      case QueryCondition::COMPARE_NULL:
      {
        $sql .= ' = NULL';
        break;
      }
      case QueryCondition::COMPARE_NOT_NULL:
      {
        $sql .= ' != NULL';
        break;
      }
      case QueryCondition::COMPARE_LIKE:
      {
        $sql .= ' LIKE ';
        break;
      }
    }

    if ($condition->isValueField())
    {
      $sql .= ' ' . $condition->getValue();
    }
    else
    {
      $placeholder = self::PLACEHOLDER_TOKEN . $condition->getTable() . '_' . $condition->getField();
      $sql .= ' ' . $placeholder;
      $query->addValue($placeholder, $condition->getValue());
    }

    return $sql;
  }

  /**
   * @param Query $query
   * @param QueryTable[] $tables
   * @return string
   */
  protected function _buildJoins(Query $query, $tables)
  {
    $sql = '';
    $first = TRUE;
    foreach($tables as $table)
    {
      // Join.
      if ($first)
      {
        $sql .= ' FROM';
        $first = FALSE;
      }
      elseif ($table->getJoin() == QueryTable::INNER_JOIN)
      {
        $sql .= ' JOIN';
      }
      elseif ($table->getJoin() == QueryTable::OUTER_JOIN)
      {
        $sql .= ' OUTER JOIN';
      }
      elseif ($table->getJoin() == QueryTable::LEFT_JOIN)
      {
        $sql .= ' LEFT JOIN';
      }
      elseif ($table->getJoin() == QueryTable::RIGHT_JOIN)
      {
        $sql .= ' RIGHT JOIN';
      }

      // Table.
      $sql .= ' ' . $table->getName() . ' ' . $table->getAlias();

      // Condition
      if ($table->getConditions())
      {
        $sql .= ' ON ' . $this->_buildConditionGroupTable($query, $table);
      }
    }

    return $sql;
  }

  protected function _buildOrder(QueryOrder $order)
  {
    $sql = '';
    $sql .= self::structureEscape($order->getTable());
    $sql .= '.';
    $sql .= self::structureEscape($order->getField());
    $sql .= ' ';
    if ($order->getDirection() == QueryOrder::DIRECTION_ASC)
    {
      $sql .= 'ASC';
    }
    elseif ($order->getDirection() == QueryOrder::DIRECTION_DESC)
    {
      $sql .= 'DESC';
    }
    else
    {
      assert(FALSE, 'Unhandled order option.');
      $sql .= 'ASC';
    }

    return $sql;
  }

  function concatenate()
  {
    $string = '';
    $args = func_get_args();
    foreach($args as $arg)
    {
      $string .= ' || ' . $arg;
    }
    $string = trim($string, ' |');
    return $string;
  }

  function literal($string)
  {
    return '\'' . $string . '\'';
  }

  function likeEscape($string)
  {
    $string = str_replace('%', '\\%', $string);
    return str_replace('_', '\\_', $string);
  }

  function structureEscape($string)
  {
    return '"' . $string . '"';
  }
}
