<?php
define('СALLBACK_API_EVENT_CONFIRMATION', 'confirmation');
define('СALLBACK_API_EVENT_MESSAGE_NEW', 'message_new');
require_once('bd/qr.php');
require_once('auth.serv.php');
require_once('objects/basic.php');
require_once('vk/api.php');
require_once('vk/input_message.php');
require_once('vk/buttons.php');
require_once('global.php');

_get_consts();

if (VK_INPUT_EVENT === СALLBACK_API_EVENT_CONFIRMATION)
{echo CALLBACK_API_CONFIRMATION_TOKEN;exit();}
else{   echo 'ok';
        if (VK_INPUT_EVENT == СALLBACK_API_EVENT_MESSAGE_NEW)
        {
            vkApi_markAsRead(VK_INPUT_MESSAGE_USER_ID, VK_INPUT_MESSAGE_ID);
        }else{exit();};
    };

if (!isset($_REQUEST)) {exit();};
// log_msg(VK_LAST_MESSAGE_TIME_OFFSET);
$is_set_history = defined('VK_LAST_MESSAGE_TIME_OFFSET');
log_msg('$is_set_history - '. $is_set_history);
if ($is_set_history == true)
{
    if (VK_LAST_MESSAGE_TIME_OFFSET<2)
    {
        log_msg('Слишком частая отправка сообщений');
        exit()
    ;};
};

// exit();     //для остановки всего - раскомментировать

log_msg('-------------------------------');
log_msg('Входящее сообщение: ' . VK_INPUT_MESSAGE_TEXT);
log_msg('Дата и время: ' . date('d.m.y H:i:s' ,VK_INPUT_MESSAGE_DATE));
$user = new user('vk', VK_INPUT_MESSAGE_USER_ID);
$main = new fst_main($user);
exit();
