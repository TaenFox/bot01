<?php
class debt extends basic_class
{
    public $revers;     //отношение владельца к долгу 0-датель/1-получатель
    public $debtsum;    //сумма
    public $currency;   //валюта
    public $user;       //владелец записи
    public $own;        //ид владельца
    //--------------------------------------
    public function __construct($id)
    {
      // log_msg(__FILE__.'::'.__LINE__);

            //проверяю id, если нет, создаю
        if (db_check_debt($id)!==true)
        {
            // log_msg(VK_INPUT_MESSAGE_USER_ID);
            $id = db_add_debt();
        };
            //работа с созданным объектом
            // log_msg(__FILE__.'::'.__LINE__);
        $field = " ";
        parent::__construct('debt', $field, $id);
        // log_msg(__FILE__.'::'.__LINE__);
        self::exec_debt();
        // $this->exec_debt();
        // if ($this->status == 'Waiting')
        // {
              // $editing = new fst_debt($this);
        //       $editing->state();
        // };
    }
    //--------------------------------------
    private function exec_debt()
    {
        $this->revers = $this->moreinfo['revers'];
        $this->debtsum = $this->moreinfo['debtsum'];
        $this->currency = $this->moreinfo['currency'];
        $this->dateadd = $this->moreinfo['dateadd'];
        $this->user = new user('vk', VK_INPUT_MESSAGE_USER_ID);
        $this->own = $this->user->id;
    }
}





class fst_debt extends basic_fst
{
    protected function SayHello()       //отправляет сообщение и после выполнения завершает процесс
    {
        parent::txtStatusDebt();
        parent::QAnswKB(new keyboard_debt_menu);
        exit();
    }
    protected function ChangeRevers()   //меняет зависимость долга
    {
        $rev = $this->obj->revers;
        if ($rev == 1){$rev = 0;}else{$rev = 1;};
        $this->obj->revers = $rev;
        $this->obj->update();
    }
    protected function ChangeSum()
    {
        $this->obj->debtsum = VK_USER_ANSWER_TEXT;
        $this->obj->update();
    }
    protected function ChangeDate()
    {
        parent::changeDate();
    }
    protected function ChangeCur()
    {
        $this->obj->currency = VK_USER_ANSWER_TEXT;
        $this->obj->update();
    }
    protected function DebtExit()
    {
        parent::textAnsw('Жду твоих команд. Напиши мне "Справка", ' .
        'если тебе нужны подробности');
        parent::set('Waiting');
        $this->obj->user->set('Waiting');
        parent::QAnswKB(new keyboard_main_menu);
        exit();
    }
    protected function Waiting($mode = 'answ')
    {
        switch ($mode)
        {
            case 'ask':
                parent::set('Waiting');
                self::SayHello();
                break;
            case 'answ':
                switch (VK_USER_ANSWER_TEXT)
                {
                    case 'добавить запись':
                        parent::textAnsw('Новая запись. Что с ней сделаем?' . chr(10));
                        self::SayHello();
                        break;
                    case 'зависимость':
                        self::ChangeRevers();
                        parent::textAnsw('Поменяли зависимость записи!');
                        self::SayHello();
                        break;
                    case 'сумма':
                        self::DebtSum();
                        break;
                    case 'название':
                        self::DebtName();
                        break;
                    case 'дата':
                        self::DebtDate();
                        break;
                    case 'части':
                        self::DebtShare();
                        break;
                    case 'измерение':
                        self::DebtCur();
                        break;
                    case 'удалить':
                        self::DebtDelete();
                        break;
                    case 'готово':
                        self::DebtExit();
                        break;
                    case 'справка':
                        self::helper();
                        break;
                    default:
                        parent::textAnsw('Жду ввода команд!' . chr(10));
                        parent::txtStatusDebt();
                        parent::QAnswKB(new keyboard_debt_menu);
                        break;
                }
        }
        exit();
    }
    protected function DebtName($mode = 'answ')
    {
        switch ($mode)
        {
            case 'ask':
                break;
            case 'answ':
                parent::textAnsw('Введи новое название' . chr(10) . chr(10) .
                'Напиши "Справка" чтобы получить подробную информацию');
                parent::set('DebtName');
                parent::QAnswKB(new keyboard_cancel);
                break;
            case 'current':
                parent::renameUser();
                parent::textAnsw('Название изменено!');
                self::Waiting('ask');    //текст для приглашения пользователя к действиям в главном меню
                break;
            case 'cancel':
                parent::objPopStatus();
                self::Waiting('ask');
                break;
        }
        exit();
    }
    protected function DebtSum($mode = 'answ')
    {
        switch ($mode)
        {
            case 'ask':
                break;
            case 'answ':
                parent::textAnsw('Введи новую сумму' . chr(10) . chr(10) .
                'Напиши "Справка" чтобы получить подробную информацию');
                parent::set('DebtSum');
                parent::QAnswKB(new keyboard_cancel);
                break;
            case 'current':
                self::ChangeSum();
                parent::textAnsw('Сумма изменена!');
                self::Waiting('ask');    //текст для приглашения пользователя к действиям в главном меню
                break;
            case 'cancel':
                parent::objPopStatus();
                self::Waiting('ask');
                break;
        }
        exit();
    }
    protected function DebtDate($mode = 'answ')
    {
        switch ($mode)
        {
            case 'ask':
                break;
            case 'answ':
                parent::textAnsw('Введи дату в формате дд.мм.гггг. В первых ' .
                'числах месяца ноль в переди - обязателен!' . chr(10) . chr(10) .
                'Напиши "Справка" чтобы получить подробную информацию');
                parent::set('DebtDate');
                parent::QAnswKB(new keyboard_cancel);
                break;
            case 'current':
                self::ChangeDate();
                parent::textAnsw('Сумма изменена!');
                self::Waiting('ask');    //текст для приглашения пользователя к действиям в главном меню
                break;
            case 'cancel':
                parent::objPopStatus();
                self::Waiting('ask');
                break;
        }
        exit();
    }
    protected function DebtDelete($mode = 'answ')
    {
        switch ($mode)
        {
            case 'ask':
                break;
            case 'answ':
                parent::textAnsw('Ты уверен что хочешь удалить эту запись со ' .
                'внесёнными настройками?' . chr(10) . chr(10) .
                'Напиши "Справка" чтобы получить подробную информацию');
                parent::set('DebtDelete');
                parent::QAnswKB(new keyboard_yes_no);
                break;
            case 'current':
                parent::textAnsw('Запись удалена!' . chr(10));
                parent::delete();
                self::DebtExit();    //текст для приглашения пользователя к действиям в главном меню
                break;
            case 'cancel':
                parent::objPopStatus();
                self::Waiting('ask');
                break;
        }
        exit();
    }
    protected function DebtCur($mode = 'answ')
    {
        switch ($mode)
        {
            case 'ask':
                break;
            case 'answ':
                parent::textAnsw('Введи название единицы измерения - рубли, ' .
                'евро, книжки или что угодно. Ограничение такое же как и на ' .
                'названия - 30 символов, спецсимволы запрещены' . chr(10) . chr(10) .
                'Напиши "Справка" чтобы получить подробную информацию');
                parent::set('DebtCur');
                parent::QAnswKB(new keyboard_currency);
                break;
            case 'current':
                self::ChangeCur();
                parent::textAnsw('Единицы измерения изменены!' . chr(10));
                self::Waiting('ask');    //текст для приглашения пользователя к действиям в главном меню
                break;
            case 'cancel':
                parent::objPopStatus();
                self::Waiting('ask');
                break;
        }
        exit();
    }
    //--------------------------------------
    protected function DebtShare()
    {
        parent::set('DebtShare');
        $share_pag = new fst_debt_shares($this->obj);
        exit();
    }
    //--------------------------------------
    protected function stateWaiting()
    {
        $func = substr(__FUNCTION__, 5);
        self::$func();
        exit();
    }
    //--------------------------------------
    protected function stateDebtName()
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
                if (VK_USER_ANSWER_IS_STR)
                {
                    self::$func('current');
                } else {
                    $mes = 'Возможно, ты использовал специальные символы ' .
                    'или длинна введённого имени слишком велика (до 30 символов). ' .
                    'Постарайся уложится в эти ограничения';
                    parent::doNotUnderstnd(new keyboard_cancel, $mes);
                };
                break;
        };
        self::$func();
        exit();
    }
    //--------------------------------------
    protected function stateDebtSum()
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
                if (VK_USER_ANSWER_IS_NUM)
                {
                    self::$func('current');
                } else {
                    $mes = 'К сожалению, присланное тобой сообщение я не могу ' .
                    'трактовать как числовое... Попробуй по другому';
                    parent::doNotUnderstnd(new keyboard_cancel, $mes);
                };
                break;
        };
        self::$func();
        exit();
    }
    //--------------------------------------
    protected function stateDebtDate()
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
                if (VK_USER_ANSWER_IS_DATE)
                {
                    self::$func('current');
                } else {
                    $mes = 'Мне кажется это не дата';
                    parent::doNotUnderstnd(new keyboard_cancel, $mes);
                };
                break;
        };
        self::$func();
        exit();
    }
    //--------------------------------------
    protected function stateDebtDelete()
    {
        $func = substr(__FUNCTION__, 5);
        switch (VK_USER_ANSWER_TEXT)
        {
            case 'справка':
                self::helper();
                break;
            case 'нет':
                self::$func('cancel');
                break;
            case 'да':
                self::$func('current');
                break;
            default:
                $mes = 'Мне нужен ответ или "Да" или "Нет"". Нет вернёт в меню настройки записи"';
                parent::doNotUnderstnd(new keyboard_yes_no, $mes);
                break;
        };
        self::$func();
        exit();
    }
    //--------------------------------------
    protected function stateDebtCur()
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
                if (VK_USER_ANSWER_IS_STR)
                {
                    self::$func('current');
                } else {
                    $mes = 'Возможно, ты использовал специальные символы ' .
                    'или длинна введённого имени слишком велика (до 30 символов). ' .
                    'Постарайся уложится в эти ограничения';
                    parent::doNotUnderstnd(new keyboard_currency, $mes);
                };
                break;
        };
        self::$func();
        exit();
    }
    //--------------------------------------
    protected function stateDebtShare()
    {
        $share_pag = new fst_debt_shares($this->obj);
        self::SayHello();
        exit();
    }
    //--------------------------------------
    protected function stateDebtShareNew()
    {
        $share_pag = new fst_debt_shares($this->obj);
        self::SayHello();
        exit();
    }
    //--------------------------------------
    protected function stateDebtShareEdit()
    {
        $share_pag = new fst_debt_shares($this->obj);
        self::SayHello();
        exit();
    }
    //--------------------------------------
    protected function helper()
    {
        $backtrace = debug_backtrace();
        $func = $backtrace[1]['function'];
        switch ($func)
        {
        case 'Waiting':
            parent::textAnsw('Ты только что создал запись! Сейчас ты можешь ' .
            'изменить её параметры');
            break;
        case 'stateDebtName':
            parent::textAnsw('Нужно написать название записи чтобы ты и другие ' .
            'пользователи не запутались');
            break;
        case 'stateDebtSum':
            parent::textAnsw('Введи сумму записи в формате [0.00]. Если хочешь ' .
            'указать дробную часть  - это совершенно не обязательно - используй ' .
            'точку в качестве резделителя целых и дробных частей. В системе ' .
            'может храниться информация только о двух знаках дроби');
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





class fst_debt_shares extends basic_fst
{
    protected function SayHello()
    {
        parent::txtStatusDebt();
        parent::set('DebtShare');
        parent::QAnswKB(new keyboard_debt_share_menu);
        exit();
    }
    protected function DebtShare($mode = 'answ')
    {
        switch ($mode)
        {
            case 'ask':
                parent::textAnsw('Ты хочешь создать новую часть или изменить существующую?');
                self::SayHello();
                break;
            case 'new':
                self::DebtShareNew('new');
                break;
            case 'edit':
                self::DebtShareEdit('ask');
                break;
            case 'cancel':
                parent::set('Waiting');
                return;
                break;
            case 'answ':
                switch (VK_USER_ANSWER_TEXT)
                {
                    case 'справка':
                        self::helper();
                        break;
                    default;
                        $mes = 'Выбери одну из предложенных команд. Если команды ' .
                        'не отображены внизу - напиши "Справка"';
                        parent::doNotUnderstnd(new keyboard_debt_share_menu, $mes);
                        break;
                };
                break;
        };
        exit();
    }
    //--------------------------------------
    protected function Share($mode = 'add')
    {
        switch ($mode)
        {
            case 'add':
                $share = new share(0, $this->obj);
                parent::setID('DebtShareNew', $share->id);
                break;
            case 'edit':
                $share = new share(parent::childId(), $this->obj);
                break;
        };
        $FST = new fst_share($share, $this);
        exit();
    }
    //--------------------------------------
    protected function DebtShareNew($mode = 'answ')
    {
        switch ($mode)
        {
            case 'new':
                parent::textAnsw('Создали новую часть к записи');
                self::Share();
                break;
            case 'answ':
                self::Share('edit');
                break;
        }
        exit();
    }
    //--------------------------------------
    protected function DebtShareEdit($mode = 'answ')
    {
        switch ($mode)
        {
            case 'ask':
                parent::txtStatusDebt();
                parent::textAnsw('Список частей для изменения:' . chr(10));
                $pag = new paginator_share($this->obj);
                $res = $pag->sendRows();
                break;
            case 'answ':
                switch (VK_USER_ANSWER_TEXT)
                {
                    case 'справка':
                        self::helper();
                        break;
                    case 'назад':
                        parent::set('Waiting');
                        self::SayHello();
                        break;
                    default;
                        if (VK_BOT_PAGINATOR_READY)
                        {
                            $pag = new paginator_share($this->obj);
                            $rw = $pag->sendRows();
                            // log_msg($rw);
                            if ($rw == false){exit();};
                            parent::set($rw);
                            parent::set('DebtShareNew');
                        } else {
                            if (VK_INPUT_MESSAGE_FORW_IS_TRUE)
                            {
                                $pag = new paginator_share($this->obj);
                                $rw = $pag->sendRows();
                                if ($rw == false){exit();};

                                parent::set($rw);
                                parent::set('DebtShareNew');
                                self::Share('edit');
                            } else
                            {
                                $mes = 'Неверная команда. Лучше выбирать с кнопок. ' .
                                'Если не получается - напиши мне "Справка"';
                                parent::doNotUnderstnd(new keyboard_page, $mes);
                            }
                        };
                        break;
                };
                break;
            case 'exitShare':
                self::SayHello();
                break;
        };
        exit();
    }
    //--------------------------------------
    protected function stateDebtShare($mode = 'answ')
    {
        $func = substr(__FUNCTION__, 5);
        switch (VK_USER_ANSWER_TEXT)
        {
            case 'справка':
                self::helper();
                break;
            case 'части':
                self::$func('ask');
                break;
            case 'назад':
                self::$func('cancel');
                break;
            case 'изменить':
                self::$func('edit');
                break;
            case 'новая';
                self::$func('new');
                break;
            case 'отмена':
                parent::set('Waiting');
                parent::txtStatusDebt();
                parent::QAnswKB(new keyboard_debt_menu);
                break;
            default:
                $mes = 'Воспользуйся предложенным меню. Доступные команды ' .
                '"Новая", "Изменить", "Отмена" и "Справка"';
                parent::doNotUnderstnd(new keyboard_debt_share_menu, $mes);
                break;
        };
        exit();
    }
    protected function stateDebtShareNew()
    {
        $func = substr(__FUNCTION__, 5);
        self::$func();
        exit();
    }
    protected function stateDebtShareEdit()
    {
        $func = substr(__FUNCTION__, 5);
        if (VK_USER_ANSWER_TEXT == 'отмена'){self::$func('exitShare');exit();}
        self::$func();
        exit();
    }
    //--------------------------------------
    protected function helper()
    {
        $backtrace = debug_backtrace();
        $func = $backtrace[1]['function'];
        switch ($func)
        {
        case 'stateDebtShare':
            parent::textAnsw('Сейчас можно создать новую часть записи или ' .
            'настроить существующие. Для создания новой введи "Новая", для ' .
            'получения списка доступных частей введи "Изменить". Для возврата ' .
            'в предыдущее меню введи "Отмена"');
            break;
        case 'DebtShare':
            parent::textAnsw('Сейчас можно создать новую часть записи или ' .
            'настроить существующие. Для создания новой введи "Новая", для ' .
            'получения списка доступных частей введи "Изменить". Для возврата ' .
            'в предыдущее меню введи "Отмена"');
            break;
        case 'DebtShareEdit':
            parent::textAnsw('В этом меню представленны все части редактируемой ' .
            'записи. Для навигации по списку используй кнопки с указанием ' .
            'номеров записей, либо введи "х-у", где х - это начальная ' .
            'позиция, а у - конечная (х+5). Для выбора интересующей - ' .
            'перешли мне сообщение с описанием части.');
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
