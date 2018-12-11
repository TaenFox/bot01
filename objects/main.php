<?php
class fst_main extends basic_fst
{
    //--------------------------------------
    protected function Waiting($mode = 'answ')
    {
        switch ($mode)
        {
            case 'ask':
                parent::textAnsw('Жду твоих команд. Напиши мне "Справка", ' .
                'если тебе нужны подробности');
                parent::txtStatusUser();
                parent::set('Waiting');
                parent::QAnswKB(new keyboard_main_menu);
                break;
            case 'answ':
                switch (VK_USER_ANSWER_TEXT)
                {
                    case 'добавить запись':
                        self::AddDebt('ask');
                        break;
                    case 'мои записи':
                        self::MyDebt('ask');
                        break;
                    case 'я должен':
                        self::DebtOut();
                        break;
                    case 'мне должны':
                        self::DebtIn();
                        break;
                    case 'настройка':
                        self::SetUp('ask');
                        break;
                    case 'справка':
                        self::helper();
                        break;
                    default;
                        $mes = 'Выбери одну из предложенных команд. Если команды ' .
                        'не отображены внизу - напиши "Справка"';
                        parent::doNotUnderstnd(new keyboard_main_menu, $mes);
                        break;
                };
                break;
        };
        exit();
    }
    //--------------------------------------
    protected function AddDebt($mode = 'answ', $debt_id = 0)
    {
        switch ($mode)
        {
            case 'ask':
                self::Debt();
                break;
            case 'answ':
                self::Debt('edit');
                break;
        };
        exit();
    }
    //--------------------------------------
    protected function Debt($mode = 'add')
    {
        switch ($mode)
        {
            case 'add':
                $debt = new debt(0);
                parent::setID('AddDebt', $debt->id);
                break;
            case 'edit':
                $debt = new debt(parent::childId());
                break;
        };
        $FST = new fst_debt($debt);
        exit();
    }
    //--------------------------------------
    protected function MyDebt($mode = 'answ')
    {
        switch ($mode)
        {
            case 'ask':
                parent::txtStatusUser();
                parent::textAnsw('Есть доступ к следующим записям:' . chr(10));
                $pag = new paginator_debt;
                $res = $pag->sendRows();

                // self::Debt('edit');
                break;
            case 'answ':
                switch (VK_USER_ANSWER_TEXT)
                {
                    case 'справка':
                        self::helper();
                        break;
                    case 'назад':
                        // parent::set('Waiting');
                        self::Waiting('ask');
                        break;
                    default;
                        if (VK_BOT_PAGINATOR_READY)
                        {
                            $pag = new paginator_debt;
                            $rw = $pag->sendRows();
                            log_msg($rw);
                            if ($rw == false){exit();};
                            parent::set($rw);
                            parent::set('AddDebt');
                        } else {
                            if (VK_INPUT_MESSAGE_FORW_IS_TRUE)
                            {
                                $pag = new paginator_debt;
                                $rw = $pag->sendRows();
                                if ($rw == false){exit();};
                                parent::set($rw);
                                parent::set('AddDebt');
                                self::Debt('edit');
                            } else
                            {
                                $mes = 'Неверная команда. Лучше выбирать с кнопок. Если не получается - напиши мне "Справка"';
                                parent::doNotUnderstnd(new keyboard_page, $mes);
                            }
                        };
                        break;
                };
                break;
        };
        exit();
    }
    //--------------------------------------
    protected function DebtOut($mode = 'answ')
    {
        $user = $this->obj;
        $table = db_debt_out_table($user->id);

        $id_own_user = 0;
        $id_debt = 0;
        $cnt = 1;
        $summary = array();

        foreach ($table as $row)
        {
            if ($id_debt !== $row['debt_id'])
            {
                $id_debt = $row['debt_id'];
            }
            $debt = new debt($id_debt);
            $share = new share($row['share_id'],$debt);
            $share->calculateSum();

            if ($id_own_user !== $row['own_id'])
            {
                if (!is_null($row['own_id']))
                {
                    $own_user = new user('id', $row['own_id']);
                    parent::textAnsw('Пользователь - @id' . $own_user->vk .
                                    '(' . vkApi_getUserFirstName($own_user->vk) .
                                    '):' . chr(10));

                } else {
                    parent::textAnsw('Записи с пометкой "' .
                            $share->name . '":' . chr(10));
                };
            };
            $err_sum = 'ошибок не обнаружено';
            if ($share->error_sum){$err_sum = 'есть ошибки, проверьте неравные части';};
            parent::textAnsw('долг на сумму ' . $share->sum . ' ' .
                    $share->debt_obj->currency . ' за ' . $debt->name . chr(10));

            if (isset($summary[$share->debt_obj->currency]))
            {
                $summary[$share->debt_obj->currency] =
                        $summary[$share->debt_obj->currency] + $share->sum;
            }else{
                $summary[$share->debt_obj->currency] = $share->sum;
            };

            if ($cnt % 5 == 0)
            {
                parent::QAnsw();
                parent::textAnswClear();
            }

            $cnt++;
        };

    $cnt = 1;
    parent::textAnsw('Суммарно:'. chr(10));
    foreach ($summary as $key => $value)
        {
            parent::textAnsw($value . ' ' . $key);

            if ($cnt % 15 == 0)
            {
                parent::QAnsw();
                parent::textAnswClear();
            }

            $cnt++;
        };
    if (empty($summary)){parent::textAnsw('<ничего>');}
    if ($this->message !== ''){parent::QAnsw();};
    }
    //--------------------------------------
    protected function DebtIn($mode = 'answ')
    {
        $user = $this->obj;
        $table = db_debt_in_table($user->id);

        $id_own_user = 0;
        $id_debt = 0;
        $cnt = 1;
        $summary = array();

        foreach ($table as $row)
        {
            if ($id_debt !== $row['debt_id'])
            {
                $id_debt = $row['debt_id'];
            }
            $debt = new debt($id_debt);
            $share = new share($row['share_id'],$debt);
            $share->calculateSum();

            if ($id_own_user !== $row['own_id'])
            {
                if (!is_null($row['own_id']))
                {
                    $own_user = new user('id', $row['own_id']);
                    parent::textAnsw('Пользователь - @id' . $own_user->vk .
                                    '(' . vkApi_getUserFirstName($own_user->vk) .
                                    '):' . chr(10));

                } else {
                    parent::textAnsw('Записи с пометкой "' .
                            $share->name . '":' . chr(10));
                };
            };
            $err_sum = 'ошибок не обнаружено';
            if ($share->error_sum){$err_sum = 'есть ошибки, проверьте неравные части';};
            parent::textAnsw('долг на сумму ' . $share->sum . ' ' .
                    $share->debt_obj->currency . ' за ' . $debt->name . chr(10));

            if (isset($summary[$share->debt_obj->currency]))
            {
                $summary[$share->debt_obj->currency] =
                        $summary[$share->debt_obj->currency] + $share->sum;
            }else{
                $summary[$share->debt_obj->currency] = $share->sum;
            };

            if ($cnt % 5 == 0)
            {
                parent::QAnsw();
                parent::textAnswClear();
            }

            $cnt++;
        };

    $cnt = 1;
    parent::textAnsw('Суммарно:'. chr(10));
    foreach ($summary as $key => $value)
        {
            parent::textAnsw($value . ' ' . $key);

            if ($cnt % 15 == 0)
            {
                parent::QAnsw();
                parent::textAnswClear();
            }

            $cnt++;
        };
    if (empty($summary)){parent::textAnsw('<ничего>');}
    if ($this->message !== ''){parent::QAnsw();};
    }
    //--------------------------------------
    protected function SetUp($mode = 'answ')
    {
        switch ($mode)
        {
            case 'ask':
                $this->obj->set('SetUp');
                $FST = new fst_user_setup($this->obj);
            case 'answ':
                $FST = new fst_user_setup($this->obj);
                break;
        };
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
    protected function stateAddDebt()
    {
        $func = substr(__FUNCTION__, 5);
        self::$func();
        exit();
    }
    //--------------------------------------
    protected function stateMyDebt()
    {
        $func = substr(__FUNCTION__, 5);
        self::$func();
        exit();
    }
    //--------------------------------------
    protected function stateDebtOut()
    {
        $func = substr(__FUNCTION__, 5);
        self::$func();
        exit();
    }
    //--------------------------------------
    protected function stateDebtIn()
    {
        $func = substr(__FUNCTION__, 5);
        self::$func();
        exit();
    }
    //--------------------------------------
    protected function stateSetUp()
    {
        $func = substr(__FUNCTION__, 5);
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
        case 'Waiting':
            parent::textAnsw('Добро пожаловать в диалог с ботом HookMoney! ' .
        'Данный робот создан с целью предоставить тебе инструменты хранения и ' .
        'контроля за различными долговыми обязательствами. Хотя, используя его ' .
        'функционал можно решать и другие задачи, это не возбраняется :) ');
            parent::QAnsw();
            $this->message = '';
            parent::textAnsw('Сейчас тебе доступны для ввода следующие команды' .
        ' Добавить запись - создаёт запись и открывает доступ к меню ' .
        'настройки этой записи' . chr(10) .       //  chr(149) = •
        ' Мои записи - открывает список всех доступных для просмотра ' .
        'и изменения записей' . chr(10) .       //  chr(149) = •
        ' Я должен - выводит суммарную информацию о том, сколько ты ' .
        'должен в соответствии с данными в системе' . chr(10) .       //  chr(149) = •
        ' Мне должны - выводит аналогичную информацию о том, сколько ' .
        'тебе должны' . chr(10) .       //  chr(149) = •
        ' Настройка - открывает меню настройки пользователя. Можно ' .
        'поменять имя и телефон или вовсе удалить данные о пользователе' . chr(10) .
        ' Справка - сейчас и всегда предлагает к ознакомлению текст ' .
        'справки, который актуален к текущему этапу общения с ботом');
            parent::QAnsw();
            $this->message = '';
            parent::textAnsw('Я искренне надеюсь что этот бот облегчит твою ' .
        'жизнь! Ваши отзывы и пожелания оставляйте пожалуйста на ' .
        '@club' . CALLBACK_API_GROUP_ID . ' (странице бота)!' . chr(10) .
        'С Уважением, ' . chr(10) .
        '@id11313383 (Создатель бота)');
            break;
        default:
            parent::textAnsw('Данный раздел справки отсутствует. Пожалуйста, посети ' .
            'vk.com/hookmoney для более подробной информации!');
            break;
        };
        parent::QAnsw();
        exit();
    }
    //--------------------------------------
    //далее функции перенаправлющие на следующие FST SETUP
    //--------------------------------------
    protected function stateSetName()
    {
        self::SetUp();
        exit();
    }
    //--------------------------------------
    protected function stateSetPhone()
    {
        self::SetUp();
        exit();
    }
    //--------------------------------------
    protected function stateSetDelete()
    {
        self::SetUp();
        exit();
    }
}
