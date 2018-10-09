<?php

class SQLSrvSessionHandler
{
  public function open()
  {
    return TRUE;
  }
  public function close()
  {
    return TRUE;
  }
  public function read($session_id)
  {
    GLOBAL $logged_in_user;
    GLOBAL $start_session_id;
    $start_session_id = $session_id;
    $session = getSession($session_id);

    if (!$session)
    {
      $session = array(
        'id' => $session_id,
        'user_id' => 0,
        'ip' => $_SERVER['REMOTE_ADDR'],
        'timestamp' => time(),
        'data' => '',
      );
      createSession($session);
    }
    else
    {
      $logged_in_user = getUser($session['user_id']);
    }
    return $session['data'];
  }
  public function write($session_id, $data)
  {
    GLOBAL $logged_in_user;
    GLOBAL $start_session_id;
    $user_id = 0;
    if ($logged_in_user)
    {
      $user_id = $logged_in_user['id'];
    }

    $session = array(
      'id' => $session_id,
      'user_id' => $user_id,
      'ip' => $_SERVER['REMOTE_ADDR'],
      'timestamp' => time(),
      'data' => $data,
    );

    // Check if session id was refreshed. Usually occurs on login.
    if ($session_id != $start_session_id)
    {
      deleteSession($start_session_id);
      createSession($session);
    }
    else
    {
      updateSession($session);
    }
    return TRUE;
  }
  public function destroy($id)
  {
    deleteSession($id);
    return TRUE;
  }
  public function gc()
  {
    // Remove expired sessions.
    deleteSessionsExpired();

    return TRUE;
  }
}

$handler = new SQLSrvSessionHandler();
session_set_save_handler(
  array($handler, 'open'),
  array($handler, 'close'),
  array($handler, 'read'),
  array($handler, 'write'),
  array($handler, 'destroy'),
  array($handler, 'gc')
);
session_register_shutdown();


function message($message)
{
  if (!session_id())
  {
    error_log($message);
    return false;
  }
  if (!isset($_SESSION['messages']) || !is_array($_SESSION['messages']))
  {
    $_SESSION['messages'] = array();
  }
  $_SESSION['messages'][] = $message;
}
