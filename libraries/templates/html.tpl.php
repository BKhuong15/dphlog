<!DOCTYPE HTML>
<html lang="en">
  <head>
    <meta http-equiv="Content-Language" content="en">
    <link rel="shortcut icon" href="/favicon.png" type="image/x-icon"/>
    <title><?php echo $title ?></title>
    <?php foreach($css_file_paths as $css_file_path): ?>
      <link rel="stylesheet" href="<?php echo $css_file_path?>">
    <?php endforeach;?>
    <?php foreach($js_file_paths as $js_file_path): ?>
      <script src="<?php echo $js_file_path?>"></script>
    <?php endforeach;?>
  </head>
  <body<?php echo buildAttr($body_attr)?>>
    <?php if ($menu) : ?>
      <?php echo $menu; ?>
    <?php endif;?>
    <?php if (isset($messages)) : ?>
      <messages>
        <?php foreach($messages as $message) : ?>
          <message><?php if (is_object($message) || is_array($message)) {print_r($message);} else {echo $message;} ?></message>
        <?php endforeach; ?>
      </messages>
    <?php endif;?>
    <?php echo $body?>
  </body>
</html>