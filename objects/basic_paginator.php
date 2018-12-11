<?php
define ('VK_BOT_PAGINATOR_STEP', 5);
function setPageParametrs()
{
    $general_reg_exp = '/^[0-9]{1,9}\-[0-9]{1,9}$/';
    $res = preg_match($general_reg_exp,VK_INPUT_MESSAGE_TEXT);
    // if($res !== 0){return true;}else{return false;};

    if ($res)
        {
            define ('VK_BOT_PAGINATOR_READY', true);
            define ('VK_BOT_PAGINATOR_OFFSET_VISIBLE',
                    basic_paginator::regExOffset(VK_INPUT_MESSAGE_TEXT));
            define ('VK_BOT_PAGINATOR_OFFSET_REAL',
                    basic_paginator::regExOffset(VK_INPUT_MESSAGE_TEXT)-1);
            if (VK_BOT_PAGINATOR_OFFSET_VISIBLE - VK_BOT_PAGINATOR_STEP < 1)
            {
                if (VK_BOT_PAGINATOR_OFFSET_REAL !== 0)
                {
                    $num_row = 1;
                } else {
                    $num_row = VK_BOT_PAGINATOR_OFFSET_REAL + 1;
                };
            } else {
                $num_row = VK_BOT_PAGINATOR_OFFSET_REAL -
                           VK_BOT_PAGINATOR_STEP + 1;
            };

            $page_before_start = $num_row;
            $page_before_stop = $num_row + VK_BOT_PAGINATOR_STEP - 1;
            $page_before = $page_before_start.chr(45).$page_before_stop;
            define ('VK_BOT_PAGINATOR_BEFORE', $page_before);

            if(VK_BOT_PAGINATOR_OFFSET_REAL == 0)
            {
                $page_after_start = $num_row + (VK_BOT_PAGINATOR_STEP * 1);
                $page_after_stop = $num_row + (VK_BOT_PAGINATOR_STEP * 2) - 1;
                $page_after =  $page_after_start.chr(45).$page_after_stop;
                define ('VK_BOT_PAGINATOR_AFTER', $page_after);
            } else {
                $page_after_start = $num_row + (VK_BOT_PAGINATOR_STEP * 2);
                $page_after_stop = $num_row + (VK_BOT_PAGINATOR_STEP * 3);
                $page_after =  $page_after_start.chr(45).$page_after_stop;
                define ('VK_BOT_PAGINATOR_AFTER', $page_after);
            };
        } else {define ('VK_BOT_PAGINATOR_READY', false);};
}


class basic_paginator
{
    public $page;
    public $offset;
    public $step = VK_BOT_PAGINATOR_STEP;   //количество позиций, выдаваемых за раз
    public $result;
    public $count_row;
    public $test;
    public $keyboard;

    public function __construct($page, $table, $key, $filter)
    {
        $this->page = $page;
        $offset = self::regExOffset($page)-1;
        $this->offset = $offset;
        for ($i = $offset; $i < $offset + $this->step; $i++)
        {
            if ($i == 0) {$num_row = 0;} else {$num_row = $i;};
            $query = init_query_db();
            $string_q =           'SELECT * FROM `'.$table.'` ';
            $string_q = $string_q.'WHERE `'.$table.'`.`'.$key.'` = '.$filter.' ';
            $string_q = $string_q.'LIMIT '.$num_row.', 1;';
            $result = mysqli_query($query, $string_q);
            $result = mysqli_fetch_assoc($result);
            close_query_db($query);
            $res_arr[$i-$offset] = $result;
        }
        $this->result = $res_arr;
        $this->keyboard = new keyboard_page;
    }
    //--------------------------------------
    public function regExOffset($ex)
    {
        if(self::isRegEx($ex))
        {
            $pos = stripos($ex, '-');
            $pos = substr($ex, 0, $pos);
            return $pos;
        };

    }
    //--------------------------------------
    public function isRegEx($ex)
    {
        $general_reg_exp = '/^[0-9]{1,9}\-[0-9]{1,9}$/';
        $res = preg_match($general_reg_exp,$ex);
        if($res !== 0){return true;}else{return false;};
    }
    // //--------------------------------------
    // protected function setKeyboard()
    // {
    //     if ($this->offset + 1 - $this->step < 1)
    //     {
    //         if ($this->offset !== 0) {$num_row = 1;} else {$num_row = $this->offset + 1;};
    //     } else {
    //         $num_row = $this->offset - $this->step + 1;
    //     };
    //
    //     $page_before_start = $num_row;
    //     $page_before_stop = $num_row + $this->step - 1;
    //     $page_before = $page_before_start.chr(45).$page_before_stop;
    //
    //     if($this->offset == 0)
    //     {
    //         $page_after_start = $num_row + ($this->step * 1);
    //         $page_after_stop = $num_row + ($this->step * 2) - 1;
    //         $page_after =  $page_after_start.chr(45).$page_after_stop;
    //     } else {
    //         $page_after_start = $num_row + ($this->step * 2);
    //         $page_after_stop = $num_row + ($this->step * 3);
    //         $page_after =  $page_after_start.chr(45).$page_after_stop;
    //     };
    //
    //     define('BUTTONS_VK_KEYBOARD_PAGE_BEFORE',
    //     '{"action":{"type":"text","payload":"{\"button\": \"\"}","label":"'.
    //                 $page_before
    //                 // $page_before_start.chr(45).$page_before_stop
    //     .'"},"color":'.vkApiKeyboardReplaceColor('blue').'}');
    //
    //     define('BUTTONS_VK_KEYBOARD_PAGE_AFTER',
    //     '{"action":{"type":"text","payload":"{\"button\": \"\"}","label":"'.
    //                 $page_after
    //                 // $page_after_start.chr(45).$page_after_stop
    //     .'"},"color":'.vkApiKeyboardReplaceColor('blue').'}');
    //
    //     // echo 'BUTTONS_VK_KEYBOARD_PAGE_BEFORE = ' . BUTTONS_VK_KEYBOARD_PAGE_BEFORE. '<br>';
    //     // echo 'BUTTONS_VK_KEYBOARD_PAGE_AFTER = ' . BUTTONS_VK_KEYBOARD_PAGE_AFTER. '<br>';
    //     $keyboard = BUTTONS_VK_KEYBOARD_FUNC_BEGIN.
    //     '['.BUTTONS_VK_KEYBOARD_PAGE_BEFORE.','.BUTTONS_VK_KEYBOARD_PAGE_AFTER.'],'.
    //     '['.BUTTONS_VK_KEYBOARD_BACK.']'.
    //         BUTTONS_VK_KEYBOARD_FUNC_END;
    //     $this->keyboard = $keyboard;
    // }
    //--------------------------------------
    protected function QAnswKB($message)
    {
        return
        vk_Api_messagesSend_forw_keys(VK_INPUT_MESSAGE_USER_ID,
                                      $message,
                                      VK_INPUT_MESSAGE_ID,
                                      $this->keyboard);
    }
    //--------------------------------------
    public function execId($string)
    {
        $pos = strripos($string, ' ');
        $result = substr($string, $pos + 1);
        return $result;
    }
}

//--------------------------------------
class paginator_debt  extends basic_paginator
{
    public $pages = array();
    public $user;

    public function __construct()
    {
        if (VK_BOT_PAGINATOR_READY)
        {
            $page = VK_USER_ANSWER_TEXT;
        } else {
            $page = '1-5';
        };
        $this->user = new user('vk', VK_INPUT_MESSAGE_USER_ID);
        $status = $this->user->status->pop();
        $this->pages = $this->user->status->top();
        $this->user->status->push($status);

        parent::__construct($page,
                            'debt',
                            'own',
                            $this->user->id);
    }
    //--------------------------------------
    public function sendRows()
    {
        if (VK_INPUT_MESSAGE_FORW_IS_TRUE)
        {
            $rw = $this->getRows();
            if ($rw !== false){return $rw;};
        };

        $this->user->status->pop();

        vkApi_messagesSend(VK_INPUT_MESSAGE_USER_ID, 'Страница '. $this->page, array());
        $list = $this->pages;
        for ($i = 0; $i < $this->step; $i++)
        {
            if(is_null($this->result[$i]))
            {
                vk_Api_messagesSend_keys(VK_INPUT_MESSAGE_USER_ID,
                    '[Конец записей]', $this->keyboard->scr);
                break;
            };
            $debt =  new debt($this->result[$i]['id']);
            log_msg($debt->id);
            $mes = $this->offset + $i+1 .'. Запись "'.$debt->name.'" на сумму '.
                   $debt->debtsum.' '.$debt->currency;
            $row_mes_id = vkApi_messagesSend(VK_INPUT_MESSAGE_USER_ID, $mes, array());
            $list->$row_mes_id = $debt->id;
        };
        $this->pages = $list;
        $this->user->set('Waiting');
        $this->user->set($this->pages);
        $this->user->set('MyDebt');
        vk_Api_messagesSend_keys(VK_INPUT_MESSAGE_USER_ID,
        'Для обработки записи перешли её мне', $this->keyboard->scr);
        return false;
    }
    protected function getRows()
    {
        log_msg(json_encode($this->pages));
        if (VK_INPUT_MESSAGE_FORW_IS_TRUE)
        {
            foreach($this->pages as $key => $value)
            {
                $fwd_txt = vk_Api_messades_getbyid($key)['body'];
                log_msg('сравнение - ' . $fwd_txt . ' == ' .VK_INPUT_MESSAGE_FORW_TEXT. ' => ' . VK_INPUT_MESSAGE_FORW_TEXT == $fwd_txt);
                if (VK_INPUT_MESSAGE_FORW_TEXT == $fwd_txt)
                {return $value;};
            }
        }else{
            return false;
        }
        exit();
    }
}
//--------------------------------------
class paginator_share  extends basic_paginator
{
    public $pages;
    public $debt;

    public function __construct($debt)
    {
        $this->debt = $debt;
        if (VK_BOT_PAGINATOR_READY)
        {
            $page = VK_USER_ANSWER_TEXT;
        } else {
            $page = '1-5';
        };
        $this->pages = new stdClass();
        $status = $this->debt->status->pop();
        $top = $this->debt->status->top();
        if (is_array($top) or is_object($top))
        {$this->pages = $top;};

        $this->debt->status->push($status);

        parent::__construct($page,
                            'share',
                            'debt',
                            $this->debt->id);
    }
    //--------------------------------------
    public function sendRows()
    {
        if (VK_INPUT_MESSAGE_FORW_IS_TRUE)
        {
            $rw = $this->getRows();
            if ($rw !== false){return $rw;};
        };

        $this->debt->status->pop();

        vkApi_messagesSend(VK_INPUT_MESSAGE_USER_ID, 'Страница '. $this->page, array());
        $list = $this->pages;
        for ($i = 0; $i < $this->step; $i++)
        {
            if(is_null($this->result[$i]))
            {
                vk_Api_messagesSend_keys(VK_INPUT_MESSAGE_USER_ID,
                    '[Конец записей]', $this->keyboard->scr);
                break;
            };

            $share =  new share($this->result[$i]['id'], $this->debt);
            $share->calculateSum();

            if (is_null($share->own))
            {
                $name_key = 'Название';
                $name = ' "' . $share->name . '"';
            }
            else
            {
                $name_key = 'Владелец';
                $own_user = new user('id', $share->own);
                $name = ' @id' . $own_user->vk . '(' . vkApi_getUserFirstName($own_user->vk) . ')';
            };

            $mes = $this->offset + $i+1 .'. '.$name_key.$name.' на сумму '.
                   $share->sum . ' ('.$share->type.')';
            $row_mes_id = vkApi_messagesSend(VK_INPUT_MESSAGE_USER_ID, $mes, array());
            $list->$row_mes_id = $share->id;
        };
        $this->pages = $list;
        $this->debt->set('Waiting');
        $this->debt->set('DebtShare');
        $this->debt->set($this->pages);
        $this->debt->set('DebtShareEdit');
        vk_Api_messagesSend_keys(VK_INPUT_MESSAGE_USER_ID,
        'Для обработки записи перешли её мне', $this->keyboard->scr);
        return false;
    }
    protected function getRows()
    {
        if (VK_INPUT_MESSAGE_FORW_IS_TRUE)
        {
            foreach($this->pages as $key => $value)
            {
                $fwd_txt = vk_Api_messades_getbyid($key)['body'];
                if (VK_INPUT_MESSAGE_FORW_TEXT == $fwd_txt)
                {return $value;};
            }
        }else{
            return false;
        }
        exit();
    }
}
