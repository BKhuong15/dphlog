<?php
DEFINE('ROOT_PATH', __DIR__ . '/..');

// Include the platform file.
$platform_path = ROOT_PATH . '/platform.inc.php';
if (!file_exists($platform_path))
{
  die('platform.inc.php is missing.');
}
include $platform_path;

include ROOT_PATH . '/libraries/global.inc.php';
include ROOT_PATH . '/libraries/database/database.inc.php';
include ROOT_PATH . '/libraries/database/sqlite.inc.php';
include ROOT_PATH . '/libraries/form.inc.php';
include ROOT_PATH . '/libraries/templates/template.inc.php';
include ROOT_PATH . '/libraries/session/session.db.php';
include ROOT_PATH . '/libraries/session/session.inc.php';
include ROOT_PATH . '/libraries/user/user.db.php';
include ROOT_PATH . '/libraries/user/user.inc.php';
include ROOT_PATH . '/libraries/user/user.pg.php';

include ROOT_PATH . '/link/link.db.php';
include ROOT_PATH . '/link/link.inc.php';
include ROOT_PATH . '/link/link.pg.php';
