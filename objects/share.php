<?php
class share extends basic_class
{
    public $share;      //сама доля
    public $debt;       //принадлежность к долгу
    public $debt_obj;   //принадлежность к долгу как объект
    public $own;        //чья доля, внутренний идентификатор пользователя
    public $user;       //принадлежность пользователю как объект
    public $dateclose;  //дата закрытия доли
    public $trust;      //подтверждение доли владельцем

    public $type;       //тип записи - доля, процент, сумма
    public $sum;        //вычисленная сумма
    public $error_sum = false;
                        //ошибка вычисления доли
    //--------------------------------------
    public function __construct($id, $debt)
    {
        if (db_check_share($id)!==true)
        {
            $id = db_add_share($id);
        };
        $field = " ";
        parent::__construct('share', $field, $id);
        self::exec_share($debt);
    }
    //--------------------------------------
    private function exec_share($debt_obj)
    {
        $this->share = $this->moreinfo['share'];
        $this->debt = $debt_obj->id;
        $this->own = $this->moreinfo['own'];
        $this->trust = $this->moreinfo['trust'];
        $this->dateadd = $this->moreinfo['dateadd'];
        $this->dateclose = $this->moreinfo['dateclose'];
        if (!is_null($this->own))
        {
            $this->user = new user('id', $this->own);
        }
        $this->debt_obj = $debt_obj;
        parent::update();
        // self::calculateSum();
    }
    //--------------------------------------
    public function calculateSum()
    {
        $union_sum = $this->debt_obj->debtsum;
        $no_equal_sum = db_share_no_equal_sum($this->debt);
        if (is_null($no_equal_sum)){$no_equal_sum=0;};
        $no_equal_perc = db_share_equal_perc($this->debt) * $union_sum;
        $equal_count = db_share_equal_count($this->debt);
        switch ($this->share)
        {
            case '0':
                $this->type = 'равная доля';
                $sum = ($union_sum - ($no_equal_sum + $no_equal_perc)) / $equal_count;
                if ($sum <0){$sum = 0;};
                break;
            default:
                if ($this->share < 1)
                {
                    $this->type = 'доля ' . $this->share * 100 ."%";
                    $sum =
                    $union_sum * $this->share;
                    break;
                }
                else
                {
                    $this->type = 'фиксированная доля';
                    $sum = $this->share;
                }
        }
        $sum = round($sum, 2);
        $this->sum = $sum;
        $chek_sum = $union_sum - $no_equal_sum - $no_equal_perc;
        if ($chek_sum < 0){$this->error_sum = true;};
        log_msg('$this->sum = ' . $this->sum);

    }
}








class fst_share extends basic_fst
{
    protected function SayHello()
    {
        parent::txtStatusShare();
        parent::set('Waiting');
        parent::QAnswKB(new keyboard_share_menu);
        /*
            Владелец,       Сумма
            Равная,         Процент
            Удалить,        Готово
            Справка
        */
        exit();
    }
    //--------------------------------------
    protected function ShareEqual()
    {
        parent::textAnsw('Часть считается равной!');
        $this->obj->share = 0;
        $this->obj->update();
        self::SayHello();
    }
    //--------------------------------------
    protected function ShareOwner($mode = 'answ')
    {
        switch ($mode)
        {
            case 'cancel':
                self::SayHello();
                break;
            case 'ask';
                parent::textAnsw('Укажи владельца части в сообщении. Чтобы ' .
                'указать пользователя из vk.com перешли мне его сообщение.');
                parent::set(__FUNCTION__);
                parent::QAnswKB(new keyboard_cancel);
                break;
            case 'answ':
                if (VK_INPUT_MESSAGE_FORW_IS_TRUE)
                    {
                        $ktrAgent = new user('vk', VK_INPUT_MESSAGE_FORW_USER_ID);
                        $this->obj->own = $ktrAgent->id;
                        parent::textAnsw('Запомнил пользователя из vk.com');
                    }else{
                        $this->obj->name = VK_USER_ANSWER_TEXT;
                        parent::textAnsw('Запомнил твою заметку');
                    };
                $this->obj->update();
                self::SayHello();
                break;
        }
        exit();
    }
    //--------------------------------------
    protected function ShareValue($mode = 'answ')
    {
        switch ($mode)
        {
            case 'cancel':
                self::SayHello();
                break;
            case 'ask';
                parent::textAnsw('Укажи сумму части в сообщении больше 1.' .
                ' Использование такого метода зарезервирует фиксированую часть ' .
                'от суммы записи. Если ты укажешь меньше 1, т.е. или какой-то ' .
                'процент от 0% до 100% я сам вычислю долю от записи. ' .
                'Если укажешь 0 - я буду считать эту запись равной.');
                parent::set(__FUNCTION__);
                parent::QAnswKB(new keyboard_cancel);
                break;
            case 'answ':

                if (VK_USER_ANSWER_IS_FLOAT or VK_USER_ANSWER_IS_NUM)
                {
                    $this->obj->share = VK_USER_ANSWER_TEXT;
                    $this->obj->update();
                };

                if (VK_USER_ANSWER_IS_PERCENT)
                {
                    $this->obj->share = VK_USER_ANSWER_IS_PERCENT_VALUE;
                    $this->obj->update();
                }
                parent::set('Waiting');
                parent::textAnsw('Изменения внесены!');
                self::SayHello();
                break;
        }
        exit();
    }
    //--------------------------------------
    protected function ShareExit()
    {
        parent::textAnsw('Ты хочешь создать новую часть или изменить существующую?');
        parent::set('Waiting');
        $this->obj->debt_obj->set('Waiting');
        $this->obj->debt_obj->set('DebtShare');
        $this->obj->debt_obj->update();
        parent::QAnswKB(new keyboard_debt_share_menu);
        exit();
    }
    //--------------------------------------
    protected function ShareDel($mode = 'answ')
    {
        switch ($mode)
        {
            case 'cancel':
                self::SayHello();
                break;
            case 'ask';
                parent::textAnsw('Ты точно хочешь удалить эту часть?');
                parent::set(__FUNCTION__);
                parent::QAnswKB(new keyboard_yes_no);
                break;
            case 'answ':
                parent::textAnsw('Часть удалена!');
                parent::delete();
                // $this->obj->status->pop();
                // $this->obj->status->pop();
                self::ShareExit();
                break;
        }
        exit();
    }
    //--------------------------------------
    protected function Waiting($mode = 'current')
    {
        switch ($mode)
        {
            case 'current':
                parent::textAnsw('Сейчас можно обновить характеристики или ' .
                'удалить выбранную часть записи');
                self::SayHello();
                break;
            case 'owner':
                self::ShareOwner('ask');
                break;
            case 'sum';
                self::ShareValue('ask');
                break;
            case 'equal':
                self::ShareEqual();
                break;
            case 'perc':
                self::ShareValue('ask');
                break;
            case 'del':
                self::ShareDel('ask');
                break;
            case 'ready':
                self::ShareExit();
                break;
            default:
                self::SayHello();
                break;
        };
        exit();
    }
    //--------------------------------------
    protected function stateWaiting($mode = 'answ')
    {
        $func = substr(__FUNCTION__, 5);
        switch (VK_USER_ANSWER_TEXT)
        {
            case 'справка':
                self::helper();
                break;
            case 'владелец':
                self::$func('owner');
                break;
            case 'сумма':
                self::$func('sum');
                break;
            case 'равная':
                self::$func('equal');
                break;
            case 'процент';
                self::$func('perc');
                break;
            case 'удалить';
                self::$func('del');
                break;
            case 'готово';
                self::$func('ready');
                break;
            case 'да':
                self::SayHello();
                break;
            default:
                self::SayHello();
                break;
        };
        exit();
    }
    //--------------------------------------
    protected function stateShareOwner()
    {
        $func = substr(__FUNCTION__, 5);
        switch (VK_USER_ANSWER_TEXT)
        {
            case 'справка':
                self::helper();
                break;
            case 'отмена':
                self::$func('cancel');
                break;
            default:
                if (VK_USER_ANSWER_IS_STR or VK_INPUT_MESSAGE_FORW_IS_TRUE)
                {
                    self::$func();
                } else {
                    $mes = 'Возможно, ты использовал специальные символы ' .
                    'или длинна введённого имени слишком велика (до 30 символов). ' .
                    'Постарайся уложится в эти ограничения';
                    parent::doNotUnderstnd(new keyboard_cancel, $mes);
                };
                break;
        }
        exit();
    }
    //--------------------------------------
    protected function stateShareValue()
    {
        $func = substr(__FUNCTION__, 5);
        switch (VK_USER_ANSWER_TEXT)
        {
            case 'справка':
                self::helper();
                break;
            case 'отмена':
                self::$func('cancel');
                break;
            default:
                if (VK_USER_ANSWER_IS_NUM or VK_USER_ANSWER_IS_FLOAT or VK_USER_ANSWER_IS_PERCENT)
                {
                    self::$func();
                } else {
                    $mes = 'Необходимо ввести цифровое значение';
                    parent::doNotUnderstnd(new keyboard_cancel, $mes);
                };
                break;
        }
        exit();
    }
    //--------------------------------------
    protected function stateShareDel()
    {
        $func = substr(__FUNCTION__, 5);
        switch (VK_USER_ANSWER_TEXT)
        {
            case 'справка':
                self::helper();
                break;
            case 'отмена':
                self::$func('cancel');
                break;
            case 'нет':
                self::$func('cancel');
                break;
            case 'да':
                self::$func();
                break;
            default:
                $mes = 'Мне нужен ответ или "Да" или "Нет"". Нет вернёт в меню настройки части"';
                parent::doNotUnderstnd(new keyboard_yes_no, $mes);
                break;
        }
        exit();
    }
    //--------------------------------------
    protected function helper()
    {
        $backtrace = debug_backtrace();
        $func = $backtrace[1]['function'];
        switch ($func)
        {
        case 'stateWaiting':
            parent::textAnsw('В этом меню можно настроить часть записи - ' .
            'указать владельца и сумму. Владельца можно указать вручную ' .
            'или указать страницу в vk.com, подробности в справке этого раздела. ' .
            'Сумма настраивается указанием точной суммы, равной доли или ' .
            'процента. Подробности также в соотвествующем разделе. Ещё часть ' .
            'можно удалить');
            break;
        case 'stateShareOwner':
            parent::textAnsw('Для того чтобы указать пользователя vk.com нужно ' .
            'всего лишь переслать его сообщение в этот чат. Пользователь ' .
            'получит возможность учитывать у себя эту часть вместе с её ' .
            'суммой, но управлять не сможет.');
            break;
        case 'stateShareValue':
            parent::textAnsw('При указании суммы нужно помнить, что сумма не ' .
            'может быть нулевой и меньше единицы. Я буду трактовать это как ' .
            'равную часть и процентную долю соответственно. Сумму я запомниаю ' .
            'с точностью до двух знаков после запятой и при вычислениях ' .
            'округляю классически:
            1,2,3,4 -- 0,
            5,6,7,8,9 -- +1.');
            break;
        case 'stateShareDel':
            parent::textAnsw('Для подтверждения удаления введи "Да" и ' .
            'введи "Нет" для отмены."');
            break;
        default:
            parent::textAnsw('Данный раздел справки отсутствует. Пожалуйста, посети ' .
            'vk.com/hookmoney для более подробной информации!');
            break;
        };
        parent::QAnsw();
        exit();
    }
}
