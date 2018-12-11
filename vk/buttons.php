<?php
$keys = '[,]';
define('BUTTONS_VK_KEYBOARD_FUNC_BEGIN', '{"one_time":false,"buttons":[');
define('BUTTONS_VK_KEYBOARD_FUNC_END', ']}');
/////////кнопки
class button
{
    public $label;
    public $payload;
    public $color;
    public $key;

    public function __construct($label, $color = 'def')
    {
        $this->payload = '';
        $this->label = $label;
        $this->color = self::SetColor($color);
        $this->key =
        '{
          "action":
              {"type":"text",
              "payload":"{\"button\": \"'.$this->payload.'\"}",
              "label":"'.$this->label.'"},
          "color":'.$this->color.
        '}';
    }
    //--------------------------------------
    private function SetColor($color='def')
    {
          //   Цвета кнопок         // '.vkApiKeyboardReplaceColor().'
          // У кнопок может быть один из 4 цветов:
          // 1. primary — синяя кнопка, обозначает основное действие. #5181B8
          // 2. default — обычная белая кнопка. #FFFFFF
          // 3. negative — опасное действие, или отрицательное действие (отклонить, удалить и тд). #E64646
          // 4. positive — согласиться, подтвердить. #4BB34B
        switch ($color)
        {
            case 'red':
                $res = '"negative"';
                break;
            case 'blue':
                $res = '"primary"';
                break;
            case 'green':
                $res = '"positive"';
                break;
            default:
                $res = '"default"';
                break;
        };
        return $res;
    }
}
//серые
class buttonBack          extends button
{
    public function __construct(){parent::__construct('Назад');}
}
class buttonSetup         extends button
{
    public function __construct(){parent::__construct('Настройка');}
}
class buttonHelp         extends button
{
    public function __construct(){parent::__construct('Справка');}
}
//валюты
class buttonCurRur         extends button
{
    public function __construct(){parent::__construct('₽');}
}
class buttonCurEur         extends button
{
    public function __construct(){parent::__construct('€');}
}
class buttonCurDol         extends button
{
    public function __construct(){parent::__construct('$');}
}
class buttonCurPou         extends button
{
    public function __construct(){parent::__construct('£');}
}
//зелёные
class buttonHello         extends button
{
    public function __construct(){parent::__construct('Привет!','green');}
}
class buttonYes           extends button
{
    public function __construct(){parent::__construct('Да','green');}
}
class buttonReady         extends button
{
    public function __construct(){parent::__construct('Готово','green');}
}
class buttonAddDebt       extends button
{
    public function __construct(){parent::__construct('Добавить запись','green');}
}
class buttonAddShare      extends button
{
    public function __construct(){parent::__construct('Части','green');}
}
class buttonNewShare      extends button
{
    public function __construct(){parent::__construct('Новая','green');}
}
//красные
class buttonNo            extends button
{
    public function __construct(){parent::__construct('Нет','red');}
}
class buttonCancel        extends button
{
    public function __construct(){parent::__construct('Отмена','red');}
}
class buttonDelete        extends button
{
    public function __construct(){parent::__construct('Удалить','red');}
}
class buttonNoRegist      extends button
{
    public function __construct(){parent::__construct('Не регистрироваться','red');}
}
//синие
class buttonMyDebts      extends button
{
    public function __construct(){parent::__construct('Мои записи','blue');}
}
class buttonMyDebtIn      extends button
{
    public function __construct(){parent::__construct('Мне должны','blue');}
}
class buttonMyDebtOut     extends button
{
    public function __construct(){parent::__construct('Я должен','blue');}
}
class buttonChangeName    extends button
{
    public function __construct(){parent::__construct('Изменить имя','blue');}
}
class buttonChangePhone   extends button
{
    public function __construct(){parent::__construct('Изменить телефон','blue');}
}
class buttonChangeCall    extends button
{
    public function __construct(){parent::__construct('Название','blue');}
}
class buttonChangeSum     extends button
{
    public function __construct(){parent::__construct('Сумма','blue');}
}
class buttonChangeDate    extends button
{
    public function __construct(){parent::__construct('Дата','blue');}
}
class buttonChangeRev    extends button
{
    public function __construct(){parent::__construct('Зависимость','blue');}
}
class buttonChangeOwner   extends button
{
    public function __construct(){parent::__construct('Владелец','blue');}
}
class buttonChangeEqual   extends button
{
    public function __construct(){parent::__construct('Равная','blue');}
}
class buttonChangePerc   extends button
{
    public function __construct(){parent::__construct('Процент','blue');}
}
class buttonChangeCurrency   extends button
{
    public function __construct(){parent::__construct('Измерение','blue');}
}
class buttonChangeShare   extends button
{
    public function __construct(){parent::__construct('Изменить','blue');}
}
class buttonPgUp          extends button        //нестандарт
{
    public function __construct()
    {
        if(VK_BOT_PAGINATOR_READY)
            {parent::__construct(VK_BOT_PAGINATOR_BEFORE,'blue');$this->payload = 'PgUp';}
            else
            {$text = '1-' . VK_BOT_PAGINATOR_STEP;
            parent::__construct($text,'blue');}
    }
}
class buttonPgDwn         extends button        //нестандарт
{
    public function __construct()
    {
        if(VK_BOT_PAGINATOR_READY)
            {parent::__construct(VK_BOT_PAGINATOR_AFTER,'blue');;$this->payload = 'PgDwn';}
            else
            {$text = (VK_BOT_PAGINATOR_STEP + 1) . '-' . (VK_BOT_PAGINATOR_STEP * 2);
            parent::__construct($text,'blue');}
    }
}
/////////дополнительные кнопки
class buttonSetMySistemName      extends button
{
    public function __construct(){parent::__construct('Использовать системное','blue');}
}
/////////клавиатуры основные
class vk_keyboard
{
    public $scr;
    public $buttons = array();

    public function __construct($keys = array(array()))
    {
        self::add(BUTTONS_VK_KEYBOARD_FUNC_BEGIN);
        $count_rows = count($keys);
        $current_row = 1;
        foreach ($keys as $row)
        {
          self::add('[');
          $count_buttons = count($row);
          $current_button = 1;
          foreach ($row as $key)
          {
              self::add($key->key);
              if ($current_button == $count_buttons) {
                  self::add(']');
                  // делаем что-либо с последним элементом...
               }
               else {
                   self::add(',');
                   // делаем что-либо с каждым элементом
               }
               $this->buttons[get_class($key)] = $key;
               $current_button++;
          }
          if ($current_row !== $count_rows) {
              self::add(',');
              // делаем что-либо с не последним элементом...
           };
           $current_row++;
        }
        self::add(BUTTONS_VK_KEYBOARD_FUNC_END);
    }
    //--------------------------------------
    private function add($txt = '')
    {
        $this->scr = $this->scr . $txt;
    }
}
class keyboard_user_setup       extends vk_keyboard
{
    public function __construct($mode = 1)  //1-удалить, 2-не регистрировать
    {
        switch ($mode)
        {case 1: $btnDel = new buttonDelete; break;
         case 2: $btnDel = new buttonNoRegist; break;};
        $kb = array (
            array(new buttonChangePhone, new buttonChangeName),
            array($btnDel, new buttonHelp, new buttonReady));
        parent::__construct($kb);
    }
}
class keyboard_main_menu        extends vk_keyboard
{
    public function __construct()
    {
        $kb = array (
            array(new buttonAddDebt, new buttonMyDebts),
            array(new buttonMyDebtIn, new buttonMyDebtOut),
            array(new buttonSetup, new buttonHelp));
        parent::__construct($kb);
    }
}
class keyboard_debt_menu        extends vk_keyboard
{
    public function __construct()
    {
        $kb = array (
            array(new buttonChangeCall, new buttonChangeRev, new buttonChangeSum),
            array(new buttonAddShare, new buttonChangeCurrency, new buttonChangeDate),
            array(new buttonDelete, new buttonReady),
            array(new buttonHelp));
        parent::__construct($kb);
    }
}
class keyboard_debt_share_menu        extends vk_keyboard
{
    public function __construct()
    {
        $kb = array (
            array(new buttonNewShare, new buttonChangeShare),
            array(new buttonCancel),
            array(new buttonHelp));
        parent::__construct($kb);
    }
}
class keyboard_share_menu       extends vk_keyboard
{
    public function __construct()
    {
        $kb = array (
            array(new buttonChangeOwner, new buttonChangeSum),
            array(new buttonChangeEqual, new buttonChangePerc),
            array(new buttonDelete, new buttonReady),
            array(new buttonHelp));
        parent::__construct($kb);
    }
}
class keyboard_page             extends vk_keyboard
{
    public function __construct()
    {
        $kb = array (
            array(new buttonPgUp, new buttonPgDwn),
            array(new buttonBack),
            array(new buttonHelp));
        parent::__construct($kb);
    }
}
/////////клавиатуры дополнительные
class keyboard_yes_no           extends vk_keyboard
{
    public function __construct()
    {
        $kb = array (
            array(new buttonNo, new buttonYes),
            array(new buttonHelp));
        parent::__construct($kb);
    }
}
class keyboard_askNameUser      extends vk_keyboard
{
    public function __construct()
    {
        $kb = array (
            array(new buttonSetMySistemName),
            array(new buttonCancel),
            array(new buttonHelp));
        parent::__construct($kb);
    }
}
class keyboard_regHello         extends vk_keyboard
{
    public function __construct()
    {
        $kb = array (
            array(new buttonHello));
        parent::__construct($kb);
    }
}
class keyboard_cancel           extends vk_keyboard
{
    public function __construct()
    {
        $kb = array (
            array(new buttonCancel),
            array(new buttonHelp));
        parent::__construct($kb);
    }
}
class keyboard_currency           extends vk_keyboard
{
    public function __construct()
    {
        $kb = array (
            array(new buttonCurRur, new buttonCurEur),
            array(new buttonCurDol, new buttonCurPou),
            array(new buttonCancel),
            array(new buttonHelp));
        parent::__construct($kb);
    }
}
////////

// define('BUTTONS_VK_KEYBOARD_HELLO',
// '{"action":{"type":"text","payload":"{\"button\": \"\"}","label":"'.
//             "Привет!"
// .'"},"color":'.vkApiKeyboardReplaceColor('blue').'}');
//
// define('BUTTONS_VK_KEYBOARD_YES',
// '{"action":{"type":"text","payload":"{\"button\": \"\"}","label":"'.
//             "Да"
// .'"},"color":'.vkApiKeyboardReplaceColor('green').'}');
//
// define('BUTTONS_VK_KEYBOARD_NO',
// '{"action":{"type":"text","payload":"{\"button\": \"\"}","label":"'.
//             "Нет"
// .'"},"color":'.vkApiKeyboardReplaceColor('red').'}');
//
// define('BUTTONS_VK_KEYBOARD_BACK',
// '{"action":{"type":"text","payload":"{\"button\": \"\"}","label":"'.
//             "Назад"
// .'"},"color":'.vkApiKeyboardReplaceColor('red').'}');
//
// define('BUTTONS_VK_KEYBOARD_BACK_TO_SHARE',
// '{"action":{"type":"text","payload":"{\"button\": \"\"}","label":"'.
//             "Назад к долям"
// .'"},"color":'.vkApiKeyboardReplaceColor('red').'}');
//
// define('BUTTONS_VK_KEYBOARD_CANCEL',
// '{"action":{"type":"text","payload":"{\"button\": \"\"}","label":"'.
//             "Отмена"
// .'"},"color":'.vkApiKeyboardReplaceColor('def').'}');
//
// define('BUTTONS_VK_KEYBOARD_HELP',
// '{"action":{"type":"text","payload":"{\"button\": \"\"}","label":"'.
//             "Что ты умеешь?"
// .'"},"color":'.vkApiKeyboardReplaceColor('blue').'}');
//
// define('BUTTONS_VK_KEYBOARD_NEW_ORDER',
// '{"action":{"type":"text","payload":"{\"button\": \"\"}","label":"'.
//             "Добавить запись"
// .'"},"color":'.vkApiKeyboardReplaceColor('blue').'}');
//
// define('BUTTONS_VK_KEYBOARD_MY_ORDER',
// '{"action":{"type":"text","payload":"{\"button\": \"\"}","label":"'.
//             "Посмотреть мои записи"
// .'"},"color":'.vkApiKeyboardReplaceColor('blue').'}');
//
// define('BUTTONS_VK_KEYBOARD_SETTING',
// '{"action":{"type":"text","payload":"{\"button\": \"\"}","label":"'.
//             "Настроить"
// .'"},"color":'.vkApiKeyboardReplaceColor('def').'}');
//
// define('BUTTONS_VK_KEYBOARD_REVERS_1',
// '{"action":{"type":"text","payload":"{\"button\": \"\"}","label":"'.
//             "Мне должны"
// .'"},"color":'.vkApiKeyboardReplaceColor('green').'}');
//
// define('BUTTONS_VK_KEYBOARD_REVERS_0',
// '{"action":{"type":"text","payload":"{\"button\": \"\"}","label":"'.
//             "Я должен"
// .'"},"color":'.vkApiKeyboardReplaceColor('blue').'}');
//
// define('BUTTONS_VK_KEYBOARD_CURRENCY_EURO',
// '{"action":{"type":"text","payload":"{\"button\": \"\"}","label":"'.
//             "€"
// .'"},"color":'.vkApiKeyboardReplaceColor('green').'}');
//
// define('BUTTONS_VK_KEYBOARD_CURRENCY_DOLLAR',
// '{"action":{"type":"text","payload":"{\"button\": \"\"}","label":"'.
//             "$"
// .'"},"color":'.vkApiKeyboardReplaceColor('green').'}');
//
// define('BUTTONS_VK_KEYBOARD_CURRENCY_POUND',
// '{"action":{"type":"text","payload":"{\"button\": \"\"}","label":"'.
//             "£"
// .'"},"color":'.vkApiKeyboardReplaceColor('green').'}');
//
// define('KEYBOARDS_VK_SET_CLEAR',
//     BUTTONS_VK_KEYBOARD_FUNC_BEGIN.
//     BUTTONS_VK_KEYBOARD_FUNC_END);
//
// define('KEYBOARDS_VK_SET_YES_NO',
//     BUTTONS_VK_KEYBOARD_FUNC_BEGIN.
// '['.BUTTONS_VK_KEYBOARD_YES.','.BUTTONS_VK_KEYBOARD_NO.']'.
//     BUTTONS_VK_KEYBOARD_FUNC_END);
//
//
// define('KEYBOARDS_VK_SET_YES_NO_CANCEL',
//     BUTTONS_VK_KEYBOARD_FUNC_BEGIN.
// '['.BUTTONS_VK_KEYBOARD_YES.','.BUTTONS_VK_KEYBOARD_NO.'],'.
// '['.BUTTONS_VK_KEYBOARD_CANCEL.']'.
//     BUTTONS_VK_KEYBOARD_FUNC_END);
//
// define('KEYBOARDS_VK_SET_CANCEL',
//     BUTTONS_VK_KEYBOARD_FUNC_BEGIN.
// '['.BUTTONS_VK_KEYBOARD_CANCEL.']'.
//     BUTTONS_VK_KEYBOARD_FUNC_END);
//
// define('KEYBOARDS_VK_SET_BACK',
//     BUTTONS_VK_KEYBOARD_FUNC_BEGIN.
// '['.BUTTONS_VK_KEYBOARD_BACK.']'.
//     BUTTONS_VK_KEYBOARD_FUNC_END);
//
// define('KEYBOARDS_VK_SET_BACK_TO_SHARE',
//     BUTTONS_VK_KEYBOARD_FUNC_BEGIN.
// '['.BUTTONS_VK_KEYBOARD_BACK_TO_SHARE.']'.
//     BUTTONS_VK_KEYBOARD_FUNC_END);
//
// define('KEYBOARDS_VK_SET_HELLO',
//     BUTTONS_VK_KEYBOARD_FUNC_BEGIN.
// '['.BUTTONS_VK_KEYBOARD_HELLO.']'.
//     BUTTONS_VK_KEYBOARD_FUNC_END);
//
// define('KEYBOARDS_VK_SET_HELLO_HELP',
//     BUTTONS_VK_KEYBOARD_FUNC_BEGIN.
// '['.BUTTONS_VK_KEYBOARD_HELLO.'],'.
// '['.BUTTONS_VK_KEYBOARD_HELP.']'.
//     BUTTONS_VK_KEYBOARD_FUNC_END);
//
// define('KEYBOARDS_VK_SET_WAITING_NORMAL',
//     BUTTONS_VK_KEYBOARD_FUNC_BEGIN.
// '['.BUTTONS_VK_KEYBOARD_NEW_ORDER.','.BUTTONS_VK_KEYBOARD_MY_ORDER.'],'.
// '['.BUTTONS_VK_KEYBOARD_SETTING.','.BUTTONS_VK_KEYBOARD_HELP.']'.
//     BUTTONS_VK_KEYBOARD_FUNC_END);
//
// define('KEYBOARDS_VK_SET_ASK_REVERS',
//     BUTTONS_VK_KEYBOARD_FUNC_BEGIN.
// '['.BUTTONS_VK_KEYBOARD_REVERS_1.','.BUTTONS_VK_KEYBOARD_REVERS_0.']'.
//     BUTTONS_VK_KEYBOARD_FUNC_END);
//
// define('KEYBOARDS_VK_SET_ANOTHER_CURRENCY',
//     BUTTONS_VK_KEYBOARD_FUNC_BEGIN.
// '['.BUTTONS_VK_KEYBOARD_CURRENCY_EURO.','.BUTTONS_VK_KEYBOARD_CURRENCY_DOLLAR.'],'.
// '['.BUTTONS_VK_KEYBOARD_CURRENCY_POUND.','.BUTTONS_VK_KEYBOARD_CANCEL.']'.
//     BUTTONS_VK_KEYBOARD_FUNC_END);

//--------------------------------------
// function vkApiKeyboardReplaceColor($color='somecolor')
// {
//       //   Цвета кнопок         // '.vkApiKeyboardReplaceColor().'
//       // У кнопок может быть один из 4 цветов:
//       // 1. primary — синяя кнопка, обозначает основное действие. #5181B8
//       // 2. default — обычная белая кнопка. #FFFFFF
//       // 3. negative — опасное действие, или отрицательное действие (отклонить, удалить и тд). #E64646
//       // 4. positive — согласиться, подтвердить. #4BB34B
//     switch ($color)
//     {
//         case 'red':
//             return '"negative"';
//             break;
//         case 'blue':
//             return '"primary"';
//             break;
//         case 'green':
//             return '"positive"';
//             break;
//         default:
//             return '"default"';
//             break;
//     };
// }
// //--------------------------------------
// // function vkApiKeyboard($buttons = array())
// // {
// //
// //   $keyboard = [
// //       'one_time'    =>false,
// //       'buttons'     =>vkApiKeyboardButton('Старт', 'blue')
// //       // 'buttons'     =>vkApiKeyboardButton('Старт', 'blue', 'empty')
// //       // 'buttons'     =>[$buttons]
// //   ];
// //   return json_encode($keyboard, JSON_UNESCAPED_UNICODE);

// }
