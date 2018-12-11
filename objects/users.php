<?php
class user extends basic_class
{
  public $vk;
  public $phone;
  //--------------------------------------
  public function __construct($field, $param)
  {
      parent::__construct('user', $field, $param);
      self::exec_user();
      if (substr($this->status->top(), 0, 3) == 'Reg' and
        VK_INPUT_MESSAGE_USER_ID == $this->vk)
      {
          $auth = new fst_auth($this);
          exit();
      };
  }
  //--------------------------------------
  private function exec_user()
  {
    $this->vk = $this->moreinfo['vk'];
    $this->phone = $this->moreinfo['phone'];
  }
}
//---------------------------------------------------------
class fst_auth extends basic_fst
{
    protected function Waiting()
    {
         parent::textAnsw('Так или иначе - я буду ждать твоего сообщения');
         parent::set('RegWaiting');
         parent::QAnswKB(new keyboard_regHello);
         exit();
    }
    //--------------------------------------
    protected function askRegist()
    {
         parent::textAnsw('Я заглянул в свои записи - мы с тобой '.
         'ещё не знакомы. Чтобы продолжить мне нужно зарегистрировать '.
         'тебя в своей системе. Для этого, в первую очередь, '.
         'мне нужно твоё согласие.
         Для согласия напиши "Да"');
         parent::txtStatusUser();
         parent::set('RegAsk');
         parent::QAnswKB(new keyboard_yes_no);
         exit();
    }
    //--------------------------------------
    protected function Name($mode = 'current')
    {
        switch ($mode)
        {
            case 'reg':
                parent::textAnsw('Хорошо. Я робот HookMoney. Меня создали для того, ' .
        'чтобы вести учёт долгов. Моя система предлагает несколько удобных ' .
        'инструментов для этого.' . chr(10) . 'Сейчас мне нужно узнать от  ' .
        'тебя твоё имя, точнее то имя, как мне следует тебя запомнить. Обрати  ' .
        'внимание - под этим именем я буду представлять тебя для других  ' .
        'пользователей' . chr(10) . chr(10) . 'Напиши мне "Использовать ' .
        'системное", чтобы я запомнил тебя так, как записано в твоём профиле ' .
        'vk.com или напиши другое имя');
                parent::set('RegName');
                parent::QAnswKB(new keyboard_askNameUser);
                break;

            case 'every':
                parent::textAnsw('Введи другое имя или напиши мне "Использовать ' .
        'системное"' . chr(10) . chr(10) . 'Напиши "Справка" чтобы получить ' .
        'подробную информацию');
                parent::set('RegName');
                parent::QAnswKB(new keyboard_askNameUser);
                break;
            case 'current':
                parent::renameUser();
                self::RegMenu();    //текст для приглашения пользователя к действиям в главном меню
                break;
            case 'sys':
                parent::renameUser($mode);
                self::RegMenu();    //текст для приглашения пользователя к действиям в главном меню
                break;
            case 'cancel':
                parent::objPopStatus();
                break;
        };
        exit();
    }
    //--------------------------------------
    protected function Phone($mode = 'ask')
    {
        switch ($mode)
        {
            case 'ask':
                parent::textAnsw('Введи номер телефона в числовом формате (например 9999999999)');
                parent::set('RegSetPhone');
                parent::QAnswKB(new keyboard_cancel);
                break;
            case 'do':
                $this->obj->phone = VK_USER_ANSWER_TEXT;
                $this->obj->update();
                parent::textAnsw('Номер телефона успешно изменён!' . chr(10));
                self::RegMenu();
                break;
            case 'false':
                $mes = 'Номер телефона не подходит по формату!';
                parent::doNotUnderstnd(new keyboard_cancel, $mes);
                break;
            case 'cancel':
                parent::textAnsw('Это твой выбор' . chr(10));
                self::RegMenu();
                break;
        }
        exit();
    }
    //--------------------------------------
    protected function Delete($mode = 'ask')
    {
        switch ($mode)
        {
            case 'ask':
                parent::textAnsw('Ты уверен, что хочешь обезличить свой профиль? ' .
            'Данные, сохранённые тобой, останутся в системе и так же будут ' .
            'доступны другим пользователям как и раньше. Для удаления такой ' .
            'информации следует делать это в частном порядке из соответствующих меню.');
                parent::set('RegAskDel');
                parent::QAnswKB(new keyboard_yes_no);
                break;
            case 'do':
                $this->obj->phone = null;
                $this->obj->name = '';
                $this->obj->update();
                self::Waiting();
                break;
            case 'false':
                $mes = 'Я жду от тебя одно из двух сообщений - "да" или "нет".';
                parent::doNotUnderstnd(new keyboard_yes_no, $mes);
                break;
            case 'cancel':
                parent::textAnsw('Хорошо' . chr(10));
                self::RegMenu();
                break;
        }
        exit();
    }
    //--------------------------------------
    protected function RegMenu($mode = 'welcome')
    {
        switch ($mode)
        {
            case 'welcome':
                parent::textAnsw('В этом меню можно изменить введённые при ' .
        'регистрации данные.');
                parent::txtStatusUser();
                parent::set('RegWaiting');
                parent::set('RegMenu');
                parent::QAnswKB(new keyboard_user_setup(2));
                break;
            case 'name':
                self::Name('every');
                break;
            case 'phone':
                self::Phone();
                break;
            case 'del':
                self::Delete();
                break;
            case 'done':
                parent::textAnsw('Спасибо за регистрацию! Теперь ты можешь ' .
        'приступить к работе с ботом! Для этого воспользуйся одной из команд, ' .
        'расположенных на твоей клавиатуре. Также всегда доступна команда ' .
        '"Справка"!');
                parent::txtStatusUser();
                parent::set('Waiting');
                parent::QAnswKB(new keyboard_main_menu);
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
        case 'stateRegAsk':
            parent::textAnsw('В процессе регистрации совершенно необходимо ' .
        'сообщить боту своё имя - это обязательное условие. Указанное имя ' .
        'будет использоваться для указания ваших записей и частей записей. ' .
        'А вот телефон указывать совершенно не обязательно, но желательно. ' .
        'Сейчас бот не использует это поле, но в перспективе ' .
        'предполагается его использование для СМС уведомления.');
                break;
        case 'stateRegName':
            parent::textAnsw('Имя можно указать как "вручную", отправив сообщение с ' .
        'соответствующим текстом, так и с помощью специальной команды ' .
        '"Использовать системное". Соответственно, "представляться" такой ' .
        'комбинацией не следует, т.к. бот воспримет её как команду.' . chr(10) .
        'Также, существует ряд ограничений при вводе произвольного имени. ' .
        'Таким образом нельзя ввести текст длинной более 30 символов или ' .
        'содержащий любые специальные символы (кроме дефиса [-], или апострофов ' .
        '[\'] или [\`])');
                break;
        case 'stateRegMenu':
            parent::textAnsw('Сейчас можно внести отдельные изменения имени ' .
        'пользователя, его номер телефона (формат ввода цифровой, пример - ' .
        '9999999999), а также отказаться от регистрации. При этом ваша  ' .
        'учётная запись будет очищена от введённых данных.' . chr(10) .
        'Если ввод данных закончен - можно приступить к непосредственной ' .
        'работе с ботом. Для этого нужно прислать сообщение с текстом ' .
        '"Готово"');
                break;
        case 'stateRegSetPhone':
            parent::textAnsw('В этом режиме можно ввести номер телефона. ' .
        'А можно не вводить. Если проект будет развиваться на основе ' .
        'номера телефона будут подключены различные сервисы, например SMS ' .
        'уведомления.');
                break;
        case 'stateRegAskDel':
            parent::textAnsw('Это отказ от регистрации и дальнейшего ' .
        'использования функционала бота. При отказе и последующем вводе ' .
        'сообщений бот предложит зарегистрироваться снова ' .
        '');
        default:
            parent::textAnsw('Данный раздел справки отсутствует. Пожалуйста, посети ' .
            'vk.com/hookmoney для более подробной информации!');
                break;
        };
        parent::QAnsw();
        exit();
    }
    //--------------------------------------
    protected function stateRegWaiting()
    {
        self::askRegist();
        /*
          Пользователь только создан со статусом 'Waiting'. Бот
          инициирует процедуру регистрации
        */
    }
    //--------------------------------------
    protected function stateRegAsk()
    {
        switch (VK_USER_ANSWER_TEXT)
        {
            case 'да':
                self::Name('reg');
                break;
            case 'нет':
                self::Waiting();
                break;
            case 'справка':
                self::helper();
                break;
            default;
                $mes = 'Необходимо твоё согласие для продолжения работы. ' .
                'Также, можно отказаться от регистрации, написав "Нет", ' .
                'но работа со мной будет невозможна.';
                parent::doNotUnderstnd(new keyboard_yes_no, $mes);
                break;
        };
        exit();
    }
    //--------------------------------------
    protected function stateRegName()
    {
        switch (VK_USER_ANSWER_TEXT)
        {
            case 'справка':
                self::helper();
                break;
            case 'использовать системное':
                self::Name('sys');
                break;
            case 'отмена':
                self::Name('cancel');
                break;
            default:
                if (VK_USER_ANSWER_IS_STR)
                {
                    self::Name();
                } else {
                    $mes = 'Возможно, ты использовал специальные символы ' .
                    'или длинна введённого имени слишком велика (до 30 символов). ' .
                    'Постарайся уложится в эти ограничения';
                    parent::doNotUnderstnd(new keyboard_yes_no, $mes);
                };
                break;
        };
        exit();
    }
    //--------------------------------------
    protected function stateRegMenu()
    {
        switch (VK_USER_ANSWER_TEXT)
        {
            case 'справка':
                self::helper();
                break;
            case 'изменить имя':
                self::RegMenu('name');
                break;
            case 'изменить телефон':
                self::RegMenu('phone');
                break;
            case 'не регистрироваться':
                self::RegMenu('del');
                break;
            case 'готово':
                self::RegMenu('done');
                break;
            default:
                $mes = 'Справка доступна по ' .
                'соответствующей команде.';
                parent::doNotUnderstnd(new keyboard_user_setup(2), $mes);
                break;
        };
        exit();
    }
    //--------------------------------------
    protected function stateRegSetPhone()
    {
        switch (VK_USER_ANSWER_TEXT)
        {
            case 'справка':
                self::helper();
                break;
            case 'отмена':
                self::Phone('cancel');
            default:
                if (VK_USER_ANSWER_IS_PHONE)
                    {
                          self::Phone('do');
                    } else {
                          self::Phone('false');
                    };
        };
        exit();
    }
    //--------------------------------------
    protected function stateRegAskDel()
    {
        switch (VK_USER_ANSWER_TEXT)
        {
            case 'справка':
                self::helper();
                break;
            case 'нет':
                self::Delete('cancel');
                break;
            case 'да':
                self::Delete('do');
                break;
            default:
                self::Delete('false');
                break;
        };
        exit();
    }
}
//---------------------------------------------------------
class fst_user_setup extends basic_fst
{
    protected function SetUp($mode = 'ask')
    {
        switch ($mode)
        {
            case 'ask':
                parent::set('SetUp');
                parent::textAnsw('Будем что-то менять?');
                parent::txtStatusUser();
                parent::QAnswKB(new keyboard_user_setup(1));
                break;
            case 'back':
                parent::textAnsw('Жду твоих команд. Напиши мне "Справка", ' .
                'если тебе нужны подробности');
                parent::txtStatusUser();
                parent::set('Waiting');
                parent::QAnswKB(new keyboard_main_menu);
                break;

        };
        exit();
    }
    //--------------------------------------
    protected function Name($mode = 'answ')
    {
        switch ($mode)
        {
            case 'ask':
                parent::textAnsw('Введи другое имя или напиши мне "Использовать ' .
        'системное"' . chr(10) . chr(10) . 'Напиши "Справка" чтобы получить ' .
        'подробную информацию');
                parent::set('SetName');
                parent::QAnswKB(new keyboard_askNameUser);
                break;
            case 'current':
                parent::renameUser();
                self::SetUp();    //текст для приглашения пользователя к действиям в главном меню
                break;
            case 'sys':
                parent::renameUser($mode);
                self::SetUp();    //текст для приглашения пользователя к действиям в главном меню
                break;
            case 'cancel':
                parent::objPopStatus();
                self::Setup();
                break;
        };
        exit();
    }
    //--------------------------------------
    protected function Phone($mode = 'answ')
    {
        switch ($mode)
        {
            case 'ask':
                parent::textAnsw('Введи номер телефона в числовом формате (например 9999999999)');
                parent::set('SetPhone');
                parent::QAnswKB(new keyboard_cancel);
                break;
            case 'do':
                $this->obj->phone = VK_USER_ANSWER_TEXT;
                $this->obj->update();
                parent::textAnsw('Номер телефона успешно изменён!' . chr(10));
                self::SetUp();
                break;
            case 'false':
                $mes = 'Номер телефона не подходит по формату!';
                parent::doNotUnderstnd(new keyboard_cancel, $mes);
                break;
            case 'cancel':
                parent::textAnsw('Это твой выбор' . chr(10));
                self::SetUp();
                break;
        }
        exit();
    }
    //--------------------------------------
    protected function Delete($mode = 'ask')
    {
        switch ($mode)
        {
            case 'ask':
                parent::textAnsw('Ты уверен, что хочешь обезличить свой профиль? ' .
            'Данные, сохранённые тобой, останутся в системе и так же будут ' .
            'доступны другим пользователям как и раньше. Для удаления такой ' .
            'информации следует делать это в частном порядке из соответствующих меню.');
                parent::set('SetDelete');
                parent::QAnswKB(new keyboard_yes_no);
                break;
            case 'do':
                $this->obj->phone = null;
                $this->obj->name = '';
                $this->obj->update();
                parent::textAnsw('Так или иначе - я буду ждать твоего сообщения');
                parent::set('RegWaiting');
                parent::QAnswKB(new keyboard_regHello);
                exit();
                break;
            case 'false':
                $mes = 'Я жду от тебя одно из двух сообщений - "да" или "нет".';
                parent::doNotUnderstnd(new keyboard_yes_no, $mes);
                break;
            case 'cancel':
                parent::textAnsw('Хорошо' . chr(10));
                self::SetUp();
                break;
        }
        exit();
    }
    //--------------------------------------
    protected function stateSetUp()
    {
        switch (VK_USER_ANSWER_TEXT)
        {
            case 'справка':
                self::helper();
                break;
            case 'изменить имя':
                self::Name('ask');
                break;
            case 'изменить телефон':
                self::Phone('ask');
                break;
            case 'удалить':
                self::Delete('ask');
                break;
            case 'готово':
                self::SetUp('back');
                break;
            case 'настройка';
                self::SetUp('ask');
                break;
            default:
                $mes = 'Справка доступна по ' .
                'соответствующей команде.';
                parent::doNotUnderstnd(new keyboard_user_setup(2), $mes);
                break;
        };
        exit();
    }
    //--------------------------------------
    protected function stateSetName()
    {
        switch (VK_USER_ANSWER_TEXT)
        {
            case 'справка':
                self::helper();
                break;
            case 'использовать системное':
                self::Name('sys');
                break;
            case 'отмена':
                self::Name('cancel');
                break;
            default:
                if (VK_USER_ANSWER_IS_STR)
                {
                    self::Name('current');
                } else {
                    $mes = 'Возможно, ты использовал специальные символы ' .
                    'или длинна введённого имени слишком велика (до 30 символов). ' .
                    'Постарайся уложится в эти ограничения';
                    parent::doNotUnderstnd(new keyboard_yes_no, $mes);
                };
                break;
        };
        exit();
    }
    //--------------------------------------
    protected function stateSetPhone()
    {
        switch (VK_USER_ANSWER_TEXT)
        {
            case 'справка':
                self::helper();
                break;
            case 'отмена':
                self::Phone('cancel');
            default:
                if (VK_USER_ANSWER_IS_PHONE)
                    {
                          self::Phone('do');
                    } else {
                          self::Phone('false');
                    };
        };
        exit();
    }

    //--------------------------------------
    protected function stateSetDelete()
    {
        switch (VK_USER_ANSWER_TEXT)
        {
            case 'справка':
                self::helper();
                break;
            case 'нет':
                self::Delete('cancel');
                break;
            case 'да':
                self::Delete('do');
                break;
            default:
                self::Delete('false');
                break;
        };
        exit();
    }
    //--------------------------------------
    protected function helper()
    {
        $backtrace = debug_backtrace();
        $func = $backtrace[1]['function'];
        log_msg(__FILE__ . ' ' . $func);
        switch ($func)
        {
        case 'stateSetUp':
            parent::textAnsw('Это механизм изменения параметров пользователя и ' .
            'его удаления. Доступные команды: ' . chr(10) .
            '- Изменить телефон - предлагает изменить номер телефона' . chr(10) .
            '- Изменить имя - предлагает изменить имя в системе' . chr(10) .
            '- Удалить - предлагает удалить данные о пользователе' . chr(10) .
            '- Справка - вызывает эту справку' . chr(10) .
            '- Готово - возвращает в основной режим бота' . chr(10) .  chr(10) .
            'Более подробная информация по командам представленна в самих режимах');
            break;
        case 'stateSetPhone':
            parent::textAnsw('Здесь от тебя требуется ввести и отправить мне ' .
            'номер телефона, желательно свой. Главное чтобы он подходил под ' .
            'формат. Можно выйти из этого режима написав "Отмена"');
            break;
        case 'stateSetName':
            parent::textAnsw('Сейчас необходимо указать имя, которое будет ' .
            'использоваться в системе для обозначения твоих записей. Можно ' .
            'назваться как угодно не используя никакие специальные символы ' .
            'кроме дефиса[ - ] и апострофов[ \' ][ ` ] и длинной до 30 символов. ' .
            'Но учти, что это имя будет доступно всем, с кем ты связан в ' .
            'системе - правила хорошего тона приветствуются. Можно выйти из ' .
            'этого режима написав "Отмена"');
            break;
        case 'stateSetDelete':
            parent::textAnsw('Удаление - процесс при котором из памяти бота ' .
            'стираются твои личные данные (на данный момент это имя и номер ' .
            'телефона). При этом данные о твоих записях и частях остаются ' .
            'неизменными, поскольку другие пользователи их тоже могут ' .
            'использовать. Для других пользователей они будут подписаны ' .
            'актуальным именем с личной страницы vk.com. При повторной ' .
            'регистрации в боте твои данные будут также доступны тебе.');
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

}
