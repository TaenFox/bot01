<?php
//из примера на https://vk.com/dev/

//--------------------------------------
define('VK_API_VERSION', '5.69'); //Используемая версия API
define('VK_API_ENDPOINT', 'https://api.vk.com/method/');
//--------------------------------------
function vkApi_messagesSend($peer_id, $message, $attachments = array()) {
  return _vkApi_call('messages.send', array(
    'peer_id'    => $peer_id,
    'message'    => $message,
    'attachment' => implode(',', $attachments)
  ));
}
//--------------------------------------
function vk_Api_messagesSend_forw($peer_id, $message, $forward)
{
    return _vkApi_call('messages.send', array(
      'peer_id'             => $peer_id,
      'message'             => $message,
      'forward_messages'    => $forward
    ));
}
//--------------------------------------
function vkApi_usersGet($user_id) {
  return _vkApi_call('users.get', array(
    'user_id' => $user_id,
  ));
}
//--------------------------------------
function vkApi_photosGetMessagesUploadServer($peer_id) {
  return _vkApi_call('photos.getMessagesUploadServer', array(
    'peer_id' => $peer_id,
  ));
}
//--------------------------------------
function vkApi_photosSaveMessagesPhoto($photo, $server, $hash) {
  return _vkApi_call('photos.saveMessagesPhoto', array(
    'photo'  => $photo,
    'server' => $server,
    'hash'   => $hash,
  ));
}
//--------------------------------------
function vkApi_docsGetMessagesUploadServer($peer_id, $type) {
  return _vkApi_call('docs.getMessagesUploadServer', array(
    'peer_id' => $peer_id,
    'type'    => $type,
  ));
}
//--------------------------------------
function vkApi_docsSave($file, $title) {
  return _vkApi_call('docs.save', array(
    'file'  => $file,
    'title' => $title,
  ));
}
//--------------------------------------
function _vkApi_call($method, $params = array()) {
  $params['access_token'] = VK_API_ACCESS_TOKEN;
  $params['v'] = VK_API_VERSION;
  $query = http_build_query($params);
  $url = VK_API_ENDPOINT.$method.'?'.$query;
  // log_msg($url);
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $json = curl_exec($curl);
  $error = curl_error($curl);
  if ($error)
  {
    log_error($error);
    throw new Exception("Failed {$method} request");
  }
  curl_close($curl);
  $response = json_decode($json, true);
  if (!$response || !isset($response['response']))
  {
    throw new Exception("Invalid response for {$method} request");
  }
  return $response['response'];
}
//--------------------------------------
function vkApi_upload($url, $file_name) {
  if (!file_exists($file_name)) {
    throw new Exception('File not found: '.$file_name);
  }
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, array('file' => new CURLfile($file_name)));
  $json = curl_exec($curl);
  $error = curl_error($curl);
  if ($error) {
    // log_error($error);
    throw new Exception("Failed {$url} request");
  }
  curl_close($curl);
  $response = json_decode($json, true);
  if (!$response) {
    throw new Exception("Invalid response for {$url} request");
  }
  return $response;
}
//--------------------------------------
//--------------------------------------
//--------------------------------------
//--------------------------------------
//дальше самодеятельность
//--------------------------------------
//--------------------------------------
//--------------------------------------
//--------------------------------------
function vkApi_markAsRead($peer_id, $mess)
{
  return _vkApi_call('messages.markAsRead', array(
    'peer_id'           => $peer_id,
    'start_message_id'  => $mess
  ));
}
//--------------------------------------
function vkApi_getUserFirstName($peer_id)
{

  $data =  _vkApi_call('users.get', array(
    'user_id'           => $peer_id
  ));
  // log_msg($data);
  // log_msg($data[0]['first_name']);
  // $data = json_decode($data, true);
  return $data[0]['first_name'];
}
//--------------------------------------
function vk_Api_messagesSend_forw_keys($peer_id, $message, $forward, $keys)
{
    return _vkApi_call('messages.send', array(
      'peer_id'             => $peer_id,
      'message'             => $message,
      'forward_messages'    => $forward,
      'keyboard'            => $keys
    ));
    // _log_write($peer_id);
    // _log_write($message);
    // _log_write($forward);
}//--------------------------------------
function vk_Api_messagesSend_keys($peer_id, $message, $keys)
{

    $resp = _vkApi_call('messages.send', array(
      'peer_id'             => $peer_id,
      'message'             => $message,
      'keyboard'            => $keys
    ));
    return $resp;
}
//--------------------------------------
function vk_Api_messages_last_get()
{
    $resp = _vkApi_call('messages.getHistory', array(
      'count'               => 2,
      'user_id'             => VK_INPUT_MESSAGE_USER_ID,
      'group_id'            => CALLBACK_API_GROUP_ID
    ));
    // log_msg($resp);
    $is_set_last = isset($resp['items']['1']['id']);
    if (!$is_set_last){return false;};

    $id_mes = $resp['items']['1']['id'];
    $lst = _vkApi_call('messages.getById', array(
      'message_ids'               => $id_mes,
      'group_id'                  => CALLBACK_API_GROUP_ID
    ));

    return $lst['items']['0'];
                                      /*
    [id] =>
    [date] =>
    [out] =>
    [user_id] =>
    [read_state] =>
    [title] =>
    [body] =>
    [random_id] =>
    [fwd_messages] => Array
        (
            [0] => Array
                (
                    [user_id] =>
                    [date] =>
                    [body] =>
                )

        )                        */
}
//--------------------------------------
function vk_Api_messades_getbyid($id)
{
    $lst = _vkApi_call('messages.getById', array(
      'message_ids'               => $id,
      'group_id'                  => CALLBACK_API_GROUP_ID
    ));
    return $lst['items']['0'];
}
