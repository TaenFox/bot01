<?php
function _get_consts()
{
$message = _callback_getEvent();
define ('VK_INPUT_EVENT', $message['type']); //тип события
if (VK_INPUT_EVENT !== СALLBACK_API_EVENT_MESSAGE_NEW){return;};
$obj = $message['object'];
define ('VK_INPUT_MESSAGE_TEXT', $obj['body']); //текст сообщения
define ('VK_INPUT_MESSAGE_USER_ID', $obj['user_id']); //идентификатор пользователя, в диалоге с которым находится сообщение.
define ('VK_INPUT_MESSAGE_ID', $obj['id']); //идентификатор сообщения (не возвращается для пересланных сообщений).
define ('VK_INPUT_MESSAGE_DATE', $obj['date']); //дата отправки сообщения в формате Unixtime
define ('VK_INPUT_MESSAGE_READ_STATE', $obj['read_state']); //статус сообщения (0 — не прочитано, 1 — прочитано, не возвращается для пересланных сообщений).
define ('VK_INPUT_MESSAGE_TITLE', $obj['title']); //заголовок сообщения или беседы
if (isset($obj['payload']))
{
    define ('VK_INPUT_MESSAGE_PAYLOAD', $obj['payload']); //полезная нагрузка сообщения
};
////////////////////
if (isset($obj['fwd_messages']))          //проверяем наличие пересланных сообщений
{
    // log_msg('fwd: '.json_encode($obj));
    define ('VK_INPUT_MESSAGE_FORW_IS_TRUE',true);
    $forw = $obj['fwd_messages'][0];
    define ('VK_INPUT_MESSAGE_FORW_USER_ID', $forw['user_id']);
    define ('VK_INPUT_MESSAGE_FORW_TEXT', $forw['body']);
    // define ('VK_INPUT_MESSAGE_FORW_ID', $forw['id']);
}else{
    define ('VK_INPUT_MESSAGE_FORW_IS_TRUE',false);
};

setPageParametrs();

define ('VK_USER_ANSWER_TEXT', mb_strtolower(VK_INPUT_MESSAGE_TEXT)); //текст сообщения к нижнему регистру
$regExNum = '/^[0-9]{1,10}[\.]{0,1}[0-9]{0,2}$/';
$regExFloat = '/^0\.[0-9]{1,2}$/';
$regExPercent = '/^[0-9]{1,2}\%$/';
$regExDate = '/^(0[1-9]|[1-2][0-9]|30|31)\.(12|11|10|0[1-9])\.20[0-2][0-9]/';
$regExStr = '/^[а-яА-ЯёЁ ьъйЙрРтТуУфФхХцЦчЧшШщЩэЭюЮ\₽\€\$\£0-9a-zA-Z\-\`\']{1,30}$/';
$regExpPhone1 = '/^[0-9]{8,10}$/';

if (preg_match($regExNum,VK_USER_ANSWER_TEXT))
{define('VK_USER_ANSWER_IS_NUM', true);}else
{define('VK_USER_ANSWER_IS_NUM', false);};

if (preg_match($regExFloat,VK_USER_ANSWER_TEXT))
{
    $dd = substr(VK_USER_ANSWER_TEXT, 0, 2);
    $mm = substr(VK_USER_ANSWER_TEXT, 3, 2);
    $yy = substr(VK_USER_ANSWER_TEXT, 6, 4);
    if (checkdate($dd, $mm, $yy))
    {
        define('VK_USER_ANSWER_IS_FLOAT', true);
    }else{
        define('VK_USER_ANSWER_IS_FLOAT', false);
    };
}else{
    define('VK_USER_ANSWER_IS_FLOAT', false);};

if (preg_match($regExDate,VK_USER_ANSWER_TEXT))
{define('VK_USER_ANSWER_IS_DATE', true);}else
{define('VK_USER_ANSWER_IS_DATE', false);};

if (preg_match($regExPercent,VK_USER_ANSWER_TEXT))
{
    define('VK_USER_ANSWER_IS_PERCENT', true);
    $len = strlen(VK_USER_ANSWER_TEXT);
    $res = substr(VK_USER_ANSWER_TEXT, 0, $len-1)/100;
    define('VK_USER_ANSWER_IS_PERCENT_VALUE', $res);
}else{
    define('VK_USER_ANSWER_IS_PERCENT', false);
};

if (preg_match($regExStr,VK_USER_ANSWER_TEXT))
{define('VK_USER_ANSWER_IS_STR', true);}else
{define('VK_USER_ANSWER_IS_STR', false);};

if (preg_match($regExpPhone1,VK_USER_ANSWER_TEXT))
{define('VK_USER_ANSWER_IS_PHONE', true);}else
{define('VK_USER_ANSWER_IS_PHONE', false);};

// define ('VK_INPUT_MESSAGE_TEXT', $obj['body']);
// define ('VK_INPUT_MESSAGE_TEXT', $obj['body']);
// define ('VK_INPUT_MESSAGE_TEXT', $obj['body']);
$last_msg = vk_Api_messages_last_get();
if($last_msg == false){return;};
define ('VK_LAST_MESSAGE_ID', $last_msg['id']);
define ('VK_LAST_MESSAGE_DATE', $last_msg['date']);
define ('VK_LAST_MESSAGE_OUT', $last_msg['out']);
define ('VK_LAST_MESSAGE_USER_ID', $last_msg['user_id']);
define ('VK_LAST_MESSAGE_TEXT', $last_msg['body']);
define ('VK_LAST_MESSAGE_TIME_OFFSET', ((date('U')*1)-(VK_LAST_MESSAGE_DATE*1)));

}
// _log_write('сообщение от '.VK_INPUT_MESSAGE_USER_ID.' текст: '.VK_INPUT_MESSAGE_TEXT);
// $ex = array(VK_INPUT_MESSAGE_ID, VK_INPUT_MESSAGE_DATE, VK_INPUT_MESSAGE_READ_STATE, VK_INPUT_MESSAGE_TITLE);
// foreach ($ex as $x) {
//   _log_write($x);
// };
