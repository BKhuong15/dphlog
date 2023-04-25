<?php
function getActiveUser()
{
  GLOBAL $logged_in_user;
  return $logged_in_user;
}

function getActiveUserID()
{
  GLOBAL $logged_in_user;
  return iis($logged_in_user, 'id');
}