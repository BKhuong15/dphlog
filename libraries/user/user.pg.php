<?php

function userLoginForm()
{
  // Submit.
  if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST'))
  {
    try
    {
      GLOBAL $logged_in_user;
      $logged_in_user = getUserLogin($_POST['username'], $_POST['password']);
    }
    catch(Exception $e)
    {
      echo htmlWrap('h3', $e->getMessage());
    }
    session_regenerate_id();
    redirect('/');
  }

  $template = new HTMLTemplate();
  $template->setTitle('Login');

  $form = new Form('user_login_form');
  $form->setTitle('Please login to continue to dphlog.');

  $field = new FieldText('username', 'Username');
  $form->addField($field);

  $field = new FieldPassword('password', 'Password');
  $form->addField($field);

  $field = new FieldSubmit('submit', 'Submit');
  $form->addField($field);

  $template->setBody($form);

  return $template;
}

function userLogout()
{
  session_destroy();
  redirect('/');
}

function userListPage()
{
  $template = new HTMLTemplate();
  $template->setTitle('Users');
  $template->setMenu(menu());

  $table = new TableTemplate('user_list');
  $table->setHeader(array('ID', 'Username', 'E-Mail', 'Timezone'));
  $users = getUserList();
  foreach($users as $user)
  {
    $row = array();
    $row[] = $user['id'];
    $row[] = htmlWrap('a', sanitize($user['username']), array('href' => '/user?user_id=' . $user['id']));
    $row[] = sanitize($user['email']);

    $row[] = $user['timezone'];

    $table->addRow($row);
  }

  $template->setBody(htmlWrap('h1', 'User List') . htmlWrap('a', 'New User', array('href' => '/user')) . $table);

  return $template;
}

function userUpsertForm()
{
  // Submit.
  if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST'))
  {
    userUpsertFormSubmit();
  }

  $template = new HTMLTemplate();
  $template->setTitle('Edit User');
  $template->setMenu(menu());

  $user_id = getUrlID('user_id');
  $form = new Form('user_upsert_form');

  $title = 'Create User';
  if ($user_id)
  {
    $title = 'Update User';
    $user = getUser($user_id);
    $form->setValues($user);
  }
  $form->setTitle($title);

  $field = new FieldHidden('id', 'User ID');
  $field->setValue(0);
  $form->addField($field);

  $field = new FieldText('username', 'Username');
  $form->addField($field);

  $field = new FieldText('email', 'E-Mail');
  $form->addField($field);

  if ($user_id)
  {
    $field = new FieldPassword('new_password', 'New Password');
    $form->addField($field);

    $field = new FieldPassword('new_password_confirm', 'Confirm New Password');
    $form->addField($field);
  }

  // Password.
  GLOBAL $logged_in_user;
  if ((int)$logged_in_user['id'] !== 1 || !$user_id || (int)$user['id'] === 1)
  {
    $field = new FieldPassword('password', 'Password');
    $form->addField($field);
  }

  // Timezone
  $list = getTimeTimezoneList();
  $field = new FieldSelect('timezone', 'Timezone', $list);
  $field->setValue('America/New_York');
  $form->addField($field);

  $field = new FieldSubmit('submit', 'Submit');
  $form->addField($field);

  if ($user_id && $user_id != 1)
  {
    $field = new FieldSubmit('delete', 'Delete');
    $form->addField($field);
  }
  $template->setBody($form);

  return $template;
}

function userUpsertFormSubmit()
{
  GLOBAL $logged_in_user;
  $user = $_POST;

  try
  {
    // Required.
    if (!isset($user['username']))
    {
      throw new Exception('Username is required.', EXCEPTION_REQUIRED_FIELD_MISSING);
    }

    // Required.
    if (!isset($user['email']))
    {
      throw new Exception('E-Mail is required.', EXCEPTION_REQUIRED_FIELD_MISSING);
    }

    // Requires a specific format.
    if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL))
    {
      throw new Exception('E-Mail must be in the format me@example.com.', EXCEPTION_REQUIRED_FIELD_MISSING);
    }

    // If id is not defined this is a create operation.
    if (!$user['id'])
    {
      // Password is required to create a new user.
      if (!isset($user['password']))
      {
        throw new Exception('Password is required.', EXCEPTION_REQUIRED_FIELD_MISSING);
      }
      $user['id'] = createUser($user);
      message('Created user ' . $user['id'] . '.');
    }
    else
    {
      // Need original username for password check in case username was changed.
      $old_user = getUser($user['id']);

      // If not the super-user require the password.
      if (((int)$user['id'] === 1 || (int)$logged_in_user['id'] !== 1) && !getUserLogin($old_user['username'], $user['password']))
      {
        throw new Exception('Incorrect password. Password is required to make changes.', EXCEPTION_PERMISSION_DENIED);
      }

      if (isset($user['new_password']))
      {
        if (!isset($user['new_password_confirm']))
        {
          throw new Exception('Missing password confirm.', EXCEPTION_REQUIRED_FIELD_MISSING);
        }
        if ($user['new_password'] !== $user['new_password_confirm'])
        {
          throw new Exception('New Password and Confirm Password do not match.', EXCEPTION_FIELD_INVALID);
        }
        updateUserPassword($user['id'], $user['new_password']);
      }
      updateUser($user);
      message('User updated.');
    }
    redirect('/users');
  }
  catch (Exception $e)
  {
    // User login throws an error if password is incorrect bypassing the custom error. Correct that here.
    if ($e->getCode() == EXCEPTION_PERMISSION_DENIED)
    {
      message('Incorrect password. Password is required to make changes.');
    }
    else
    {
      message($e->getMessage());
    }
    redirect(u('', array('preserve_query' => 1)));
  }
}
