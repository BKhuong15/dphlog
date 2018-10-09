<?php

function installLink()
{
  GLOBAL $db;

  $query = new CreateQuery('links');
  $query->addField('code', 'TEXT', array('P', 'U', 'N'));
  $query->addField('link', 'TEXT', array('N'));
  $query->addField('timestamp', 'INTEGER', array('N'));

  $db->create($query);
}

function getLinkList($page)
{
  GLOBAL $db;

  $query = new SelectQuery('links');
  $query->addField('code');
  $query->addField('link');
  $query->addField('timestamp');
  $query->addPager($page);

  return $db->select($query);
}

function getLink($code)
{
  GLOBAL $db;

  $query = new SelectQuery('links');
  $query->addField('link');
  $query->addConditionSimple('code', $code);

  $link = $db->selectObject($query);

  if (!$link)
  {
    return FALSE;
  }
  return $link['link'];
}

function createLink($code, $link)
{
  GLOBAL $db;

  $query = new InsertQuery('links');
  $query->addField('code', $code);
  $query->addField('link', $link);
  $query->addField('timestamp', time());

  $db->insert($query);
}
