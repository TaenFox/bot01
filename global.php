<?php
//взято с https://github.com/VKCOM/bot-example-php/blob/master/www/global.php
define('BOT_BASE_DIRECTORY', __DIR__);
define('BOT_LOGS_DIRECTORY', BOT_BASE_DIRECTORY.'/logs');
function log_msg($message) {
  if (is_array($message)) {
    $message = json_encode($message);
  }
  _log_write('[INFO] ' . $message);
}
function log_error($message) {
  if (is_array($message)) {
    $message = json_encode($message);
  }
  _log_write('[ERROR] ' . $message);
  exit();
}
function _log_write($message) {
    return;     //закомментировать эту строку чтобы писать логи
  $trace = debug_backtrace();
  $function_name = isset($trace[2]) ? $trace[2]['function'] : '-';
  $mark = date("H:i:s") . ' [' . $function_name . ']';
  $log_name = BOT_LOGS_DIRECTORY.'/log_' . date("j.n.Y") . '.txt';
  $cont = $mark . " : " . $message;
  file_put_contents($log_name, $cont."\n", FILE_APPEND);
  // vkApi_messagesSend(VK_INPUT_MESSAGE_USER_ID, $cont, array());   //Раскоментировать для вывода в вк
  // vk_Api_messagesSend_forw(VK_INPUT_MESSAGE_USER_ID, $cont, VK_INPUT_MESSAGE_ID);   //Раскоментировать для вывода в вк
}
