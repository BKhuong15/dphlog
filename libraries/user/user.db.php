<?php

function installUser()
{
  GLOBAL $db;

  $query = new CreateQuery('users');
  $query->addField('id', 'INTEGER', 0, array('P', 'A'));
  $query->addField('email', 'TEXT', 128, array('U', 'N'));
  $query->addField('username', 'TEXT', 64, array('U', 'N'));
  $query->addField('password', 'TEXT', 256, array('N'));
  $query->addField('reset_code', 'TEXT', 256);
  $query->addField('reset_timestamp', 'INTEGER', 0);

  $db->create($query);

  $initial_user = array(
    'email' => 'admin@example.com',
    'username' => 'admin',
    'password' => 'admin',
  );
  createUser($initial_user);
}

function getUserList()
{
  GLOBAL $db;

  $query = new SelectQuery('users');
  $query->addField('id');
  $query->addField('email');
  $query->addField('username');

  return $db->select($query);
}

function getUser($user_id)
{
  GLOBAL $db;

  $query = new SelectQuery('users');
  $query->addField('id');
  $query->addField('email');
  $query->addField('username');
  $query->addConditionSimple('id', $user_id);

  return $db->selectObject($query);
}

function getUserLogin($username, $password)
{
  GLOBAL $db;

  $query = new SelectQuery('users');
  $query->addField('id');
  $query->addField('email');
  $query->addField('username');
  $query->addField('password');
  $query->addConditionSimple('username', $username);
  $user_login = $db->selectObject($query);

  if (!$user_login)
  {
    throw new Exception('Unknown user name.', EXCEPTION_NOT_FOUND);
  }

  if (password_verify($password, $user_login['password']))
  {
    unset($user_login['password']);
    return $user_login;
  }
  throw new Exception('Password invalid for given user.', EXCEPTION_PERMISSION_DENIED);
}

function getUserReset($username, $reset_code)
{
  GLOBAL $db;

  $query = new SelectQuery('users');
  $query->addField('id');
  $query->addField('email');
  $query->addField('username');
  $query->addField('reset_code');
  $query->addField('reset_timestamp');
  $query->addConditionSimple('username', $username);
  $user_login = $db->selectObject($query);

  if (!$user_login)
  {
    throw new Exception('Unknown user name.', EXCEPTION_NOT_FOUND);
  }

  if ($reset_code != $user_login['reset_code'])
  {
    throw new Exception('Reset code invalid for given user.', EXCEPTION_PERMISSION_DENIED);
  }

  if ($user_login['reset_timestamp'] + SEC_DAY < time())
  {
    throw new Exception('Reset code expired.', EXCEPTION_EXPIRED);
  }

  unset($user_login['password']);
  return $user_login;
}

function createUser($user)
{
  GLOBAL $db;

  $query = new InsertQuery('users');
  $query->addField('email', $user['email']);
  $query->addField('username', $user['username']);
  $query->addField('password', password_hash($user['password'], PASSWORD_BCRYPT));

  return $db->insert($query);
}

function updateUser($user)
{
  GLOBAL $db;

  $query = new UpdateQuery('users');
  $query->addField('email', $user['email']);
  $query->addField('username', $user['username']);
  $query->addConditionSimple('id', $user['id']);

  $db->update($query);
}

function updateUserPassword($user_id, $password)
{
  GLOBAL $db;

  $query = new UpdateQuery('users');
  $query->addField('password', password_hash($password, PASSWORD_BCRYPT));
  $query->addConditionSimple('id', $user_id);

  $db->update($query);
}

function updateUserResetCode($user_id)
{
  GLOBAL $db;
  $code = generateRandomString();

  $query = new UpdateQuery('users');
  $query->addField('reset_code', $code);
  $query->addField('reset_timestamp', time());
  $query->addConditionSimple('id', $user_id);

  $db->update($query);

  return $code;
}

function deleteUser($user_id)
{
  GLOBAL $db;

  $query = new DeleteQuery('users');
  $query->addConditionSimple('id', $user_id);
  $db->delete($query);
}
