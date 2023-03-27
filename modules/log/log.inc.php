<?php

// 54^4 = 8,503,056 ~ 8 Mill => To high a chance of collision.
// 54^5 = 459,165,024 ~ 500 Mill => Just right.
// 54^6 = 24,794,911,296 ~ 24 Bill => Unnecessary and can be used later.
function linkRandomCode($length = 5)
{
  $values = array_merge(
    range('A', 'Z'), // 26
    range('a', 'z'), // 26
    range('0', '9') // 10
//    array('$', '-', '_', '.', '+', '!', '*', '(', ')', ',') // 10
  );

  // -8 ambiguous characters.
  unset($values[array_search('i', $values)]);
  unset($values[array_search('I', $values)]);
  unset($values[array_search('l', $values)]);
  unset($values[array_search('L', $values)]);
  unset($values[array_search('o', $values)]);
  unset($values[array_search('O', $values)]);
  unset($values[array_search('1', $values)]);
  unset($values[array_search('0', $values)]);

  $values = array_values($values);
  $high_offset = count($values) - 1;

  // Total 54.
  $code = '';
  for ($k = 0; $k < $length; $k++)
  {
    $code .= $values[rand(0, $high_offset)];
  }

  return $code;
}
