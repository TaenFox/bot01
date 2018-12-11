<?php
// require_once('./main.php');
// require_once('./users.php');
// require_once('./stacks.php');
// require_once('./debt.php');
// require_once('./share.php');

require_once('objects/main.php');
require_once('objects/users.php');
require_once('objects/stacks.php');
require_once('objects/debt.php');
require_once('objects/share.php');
require_once('objects/basic_paginator.php');

class basic_class
{
    public  $id;
    public  $name;
    public  $status;
    public  $dateadd;
    public  $moreinfo;
    //--------------------------------------
    public function __construct($classname, $field, $param)
    {
        switch ($classname)
        {
            case 'user':
                if (db_check_user($field, $param)!==true)
                {
                    db_add_user(' ', $field, $param);
                    // $param = db_add_user(' ', $field, $param);
                    // log_msg($param);
                };
                break;
            case 'debt':
                if (db_check_debt($param)!==true)
                {
                    $param = db_add_debt(VK_INPUT_MESSAGE_USER_ID);
                };
                break;
            case 'share':
                if (db_check_share($param)!==true)
                {
                    $param = db_add_share($param);
                }
                break;
            default:
            // log_msg(__FILE__.'::'.__LINE__);
                exit();
        };
        self::exec($classname, $field, $param);
    }
    //--------------------------------------
    private function exec($classname, $field, $param)
    {
      switch ($classname)
      {
          case 'user':
              $obj = db_get_user($field, $param);
              break;
          case 'debt':
              $obj = db_get_debt($param);
              break;
          case 'share':
              $obj = db_get_share($param);
              break;
          default:
              log_error('Didnt set basic_class::classname');
              return;
      }
      $this->id = $obj['id'];
      $this->name = base64_decode($obj['name']);
      $this->dateadd = $obj['dateadd'];
      // log_msg('basic::exec_user::'.$obj['status']);
      $this->exec_status($obj['status']);
      // log_msg($this->status->top());
      $this->moreinfo = $obj;
      // log_msg(json_encode($this));
    }
    //--------------------------------------
    private function exec_status($status)
    {
        $str_stack = json_decode($status);
        $str_stack = array_reverse($str_stack);
        $stack = new stacks;
        foreach ($str_stack as $value)
        {
            // log_msg('value in array - '.$value);
            $stack->push($value);
        };
        $this->status = $stack;
        // log_msg('exec_status#2::'.$this->status->top());
    }
    //--------------------------------------
    public function update()
    {
        $class = get_class($this);
        // log_msg($class);
        $func = 'db_update_'.$class;
        // log_msg($func);
        $func($this);
    }
    //--------------------------------------
    public function set($state)
    {
        if ($this->status->top() !== $state)
        {
            switch ($state)
            {
                case 'Waiting':
                    $this->status->clear();
                    break;
                case 'Ready':
                    $this->status->clear();
                    break;
                case 'RegWaiting':
                    $this->status->clear();
                    break;
                case 'SetUp':
                    $this->status->clear();
                    $this->status->push('Waiting');
                    break;
                default:
                    break;
            };
            $this->status->push($state);
        };
        $this->update();
    }
    //--------------------------------------
    protected function setID($state, $id)
    {
        if ($this->status->top() == $state)
        {
            $this->status->pop();
            $this->status->pop();
        };
        $this->set($id);
        $this->set($state);
    }
    //--------------------------------------
    public function removeID_Status()
    {
        $this->status->pop();
        $this->status->pop();
        $this->update();
    }
    //--------------------------------------
}
//---------------------------------------------------------
class basic_fst
{
    protected  $obj;
    protected  $message;


    //--------------------------------------
    public function __construct($obj)
    {
        $this->obj = $obj;
        $func = 'state'.$obj->status->top();
        // if (method_exists('basic_fst', $func) !== true)
        // {
        //     self::set('Waiting');
        //     $func = 'stateWaiting';      //доработать
        // };
        static::$func();
        exit();
        // $this->$func();
    }
    //--------------------------------------
    protected function doNotUnderstnd($kb, $mes = '')
    {
        self::textAnsw('Я тебя не понимаю...' . chr(10));   //текст недоумения
        if ($mes !== '') {self::textAnsw($mes . chr(10)); };   //текст доумения
        self::textAnsw('Для получения более подробной ' .
                       'информации напиши "Справка"' . chr(10));   //подсказка справки
        // self::set('AskAnotherShare');   //установка статуса у доли
        self::QAnswKB($kb);    //отправка набранного сообщения
        exit();
    }
    //--------------------------------------
    protected function _txtStatus($header='Информация',
                                  $properts=array())
    {
        self::textAnsw($header . chr(10) . chr(10));
        foreach ($properts as $prop => $value)
        {
            self::textAnsw($prop . ' - ' . $value. chr(10));
        };
    }
    //--------------------------------------
    protected function txtStatusUser()
    {
        $header = '' . chr(10) . 'Информация о пользователе:';
        if ($this->obj->name == ' ' || $this->obj->name == '')
        {
            $name = '<Незарегистрирован>';
            $phone = '<Незарегистрирован>';
        }else{
            $name = $this->obj->name;
            $phone = $this->obj->phone;
            if ($phone == ''){$phone = 'нет';};
        };
        $properts = array(
            'Имя'             => $name,
            'Номер телефона'  => $phone
        );
        self::_txtStatus($header, $properts);
    }
    //--------------------------------------
    protected function txtStatusDebt()
    {
        $header = '' . chr(10) . 'Информация о записе:';

            $name = $this->obj->name;
            $owner = '@id' . $this->obj->user->vk . '(' .$this->obj->user->name. ')';
            $debtsum = $this->obj->debtsum;
            $revers = $this->obj->revers;
            $currency = $this->obj->currency;
            $dateadd = date('d.m.Y', strtotime($this->obj->dateadd));

            if ($revers == 1)
            {
                $who = 'Должны';
                $rev = 'тебе';
            } else {
                $who = 'Должен';
                $rev = 'ты';
            };

        $properts = array(
            'Владелец'          => $owner,
            'Название'          => $name,
            'Сумма'             => $debtsum,
            'Измеряем в'        => $currency,
            'Зависимость'       => $who . ' ' . $rev,
            'Дата добавления'   => $dateadd
        );
        self::_txtStatus($header, $properts);
    }
    //--------------------------------------
    protected function txtStatusShare()
    {
        $header = '' . chr(10) . 'Информация о часте:';

            $share = $this->obj;
            $share->calculateSum();
            if (is_null($share->own))
            {
                $name_key = 'Название';
                $name = $share->name;
            }
            else
            {
                $name_key = 'Владелец';
                $own_user = new user('id', $share->own);
                $name = '@id' . $own_user->vk . '(' . vkApi_getUserFirstName($own_user->vk) . ')';
            };

            $err_sum = 'ошибок не обнаружено';
            if ($share->error_sum){$err_sum = 'есть ошибки, проверьте неравные части';};

        $properts = array(
            $name_key      => $name,
            'Тип'          => $share->type,
            'Сумма'        => $share->sum .' '. $share->debt_obj->currency,
            'Ошибки'       => $err_sum,
        //     'Зависимость'       => $who . ' ' . $rev,
        //     'Дата добавления'   => $dateadd
        );
        self::_txtStatus($header, $properts);
    }
    //--------------------------------------
    protected function set($state)
    {
        if ($this->obj->status->top() !== $state)
        {
            switch ($state)
            {
                case 'Waiting':
                    $this->obj->status->clear();
                    break;
                case 'Ready':
                    $this->obj->status->clear();
                    break;
                case 'RegWaiting':
                    $this->obj->status->clear();
                    break;
                case 'SetUp':
                    $this->obj->status->clear();
                    $this->obj->status->push('Waiting');
                    break;
                default:
                    break;
            };
            $this->obj->status->push($state);
        };
        $this->obj->update();
    }
    //--------------------------------------
    protected function setID($state, $id)
    {
        if ($this->obj->status->top() == $state)
        {
            $this->obj->status->pop();
            $this->obj->status->pop();
        };
        $this->set($id);
        $this->set($state);
    }
    //--------------------------------------
    protected function delete()
    {
      $class = get_class($this->obj);
      $func = 'db_delet_'.$class;
      $func($this->obj->id);
    }
    //--------------------------------------
    protected function changeDate()
    {
        if(VK_USER_ANSWER_IS_DATE)
        {
            $res = strtotime(VK_INPUT_MESSAGE_TEXT);
            $this->obj->dateadd = date('Y-m-d', $res) . ' 00:00:00';
            $this->obj->update();
        }
    }
    //--------------------------------------
    protected function drop_state()
    {
        $this->obj->status->pop();
    }
    //--------------------------------------
    protected function QAnsw()
    {
        vkApi_messagesSend(VK_INPUT_MESSAGE_USER_ID, $this->message);
    }
    //--------------------------------------
    protected function QAnswFWD()
    {
        vk_Api_messagesSend_forw(VK_INPUT_MESSAGE_USER_ID, $this->message, VK_INPUT_MESSAGE_ID);
    }
    //--------------------------------------
    protected function QAnswKB($kb)
    {
        vk_Api_messagesSend_keys(VK_INPUT_MESSAGE_USER_ID, $this->message, $kb->scr);
    }
    //--------------------------------------
    protected function QAnswKBFWD($kb)
    {
        vk_Api_messagesSend_forw_keys(VK_INPUT_MESSAGE_USER_ID, $this->message, VK_INPUT_MESSAGE_ID, $kb->scr);
    }
    //--------------------------------------
    protected function textAnsw($txt)
    {
        $this->message = $this->message.$txt;
        // log_msg($this->message);
    }
    //--------------------------------------
    protected function textAnswClear()
    {
        $this->message = '';
        // log_msg($this->message);
    }
    //--------------------------------------
    protected function renameUser($mode = 'current')
    {
        switch ($mode)
        {
            case 'current':
                $this->obj->name = VK_INPUT_MESSAGE_TEXT;
                break;
            case 'sys':
                $this->obj->name = vkApi_getUserFirstName(VK_INPUT_MESSAGE_USER_ID);
                break;
        }
        $this->obj->update();
    }
    //--------------------------------------
    protected function childId()
    {
        $status_before = $this->obj->status->pop();
        $id = $this->obj->status->top();
        $this->set($status_before);
        return $id;
    }
    //--------------------------------------
    protected function objPopStatus()
    {
        $this->obj->status->pop();
    }
    //--------------------------------------
}



function prnt($value)
{
  echo "<pre>";
  print_r($value);
  echo "</pre>";
}
