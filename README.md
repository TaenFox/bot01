# HookMoney
## vk.com/hookmoney
Bot for vk.com. Makes notes about debts
***
Бот для вк, который помогает вести учёт долгов
***
Это мой первый большой проект на PHP. Этот робот умеет запоминать людей и вести их записи о следующем:
- что это за долг:
  + сумма долга
  + в каком эквиваленте вычислять (рубли, валюта, произвольная)
  + его количественная составляющая
  + обозначение "я должен - мне должны"
+ кто должен по-дольно:
  + произвольное название доли (напр. "штраф ДПС")
  + указание владельца долга как страницу в вк.сом и дальнейший учёт этой информации
  + равная доля - вычисляется как частное от суммы долга на количество равных частей
  + процентная доля - указывается как процент от 0 до 100 или как дробное значение. Вычисляется первее частной от суммы долга, но после отсчёта фиксированной
  + фиксированная доля - указывается пользователем произвольно, как числовое значение, но больше или равна 1. Если есть - первой вычитается от суммы долга
Также бот предоставляет построчную информацию обо всех доступных долгах с различным обозначением "я должен - мне должны" с суммой и указанием названий и названий или владельцев частей, а также возможность удалять свои записи.
***
На момент публикации кода я осознаю множество допущенных мной ошибок, в том числе запутанную систему классов, но дальнейшая разработка приостановлена на неопределённый срок, а приложение однозначно требует фундаментальной переработки. Сейчас (11.12.2018) оно запущено и доступно всем желающим. **Предоставленный код не содержит нескольких файлов с данными для входа в БД и вк.**
***
В идеале, я бы хотел значительно расширить круг возможностей бота. Одна из главных задач, которые я ставил, но не решил было участие бота в дискуссиях на несколько человек. В теории это здорово помогло бы распределять коллективные траты на неопределённое число людей _(сбор средств на мероприятия, например в образовательных учреждениях)_. Однако, в процессе работы я пришёл к выводу, что несколько переоценил свои возможности, в том числе и из-за системы классов, поэтому эти функции отсутствуют, но я не отказываюсь от идеи внедрить их в будущем.
***
По вопросам и предложениям приглашаю к обсуждению по адресу mokeev . pavel @ g mail . com
