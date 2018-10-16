<?php

function installLink()
{
  GLOBAL $db;

  $query = new CreateQuery('links');
  $query->addField('code', 'TEXT', 16, array('P', 'U', 'N'));
  $query->addField('link', 'TEXT', 2048, array('N'));
  $query->addField('user_id', 'INTEGER', 0, array('N'));
  $query->addField('timestamp', 'INTEGER', 0, array('N'));
  $query->addField('visits', 'INTEGER', 0, array('N'), 0);

  $db->create($query);
}

function getLinkList($page)
{
  GLOBAL $db;

  $query = new SelectQuery('links');
  $query->addField('code');
  $query->addField('link');
  $query->addField('user_id');
  $query->addField('visits');
  $query->addField('timestamp');
  $query->addOrderSimple('timestamp', QueryOrder::DIRECTION_DESC);
  $query->addPager($page);

  return $db->select($query);
}

function getLink($code)
{
  GLOBAL $db;

  $query = new SelectQuery('links');
  $query->addField('code');
  $query->addField('link');
  $query->addField('visits');
  $query->addConditionSimple('code', $code);

  $link = $db->selectObject($query);

  if (!$link)
  {
    return FALSE;
  }
  return $link;
}

function createLink($code, $link)
{
  GLOBAL $db;
  GLOBAL $logged_in_user;

  $query = new InsertQuery('links');
  $query->addField('code', $code);
  $query->addField('link', $link);
  $query->addField('user_id', $logged_in_user['id']);
  $query->addField('timestamp', time());

  $db->insert($query);
}

function updateLink($code, $visits)
{
  GLOBAL $db;

  $query = new UpdateQuery('links');
  $query->addField('visits', $visits);
  $query->addConditionSimple('code', $code);

  $db->update($query);
}
