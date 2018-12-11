<?php
// честно скопировано с https://habr.com/post/190176/
class stacks
{
    protected $stack;
    protected $limit;

    public function __construct($limit = 10) {
        // инициализация стека
        $this->stack = array();
        // устанавливаем ограничение на количество элементов в стеке
        $this->limit = $limit;
    }

    public function push($item) {
        // проверяем, не полон ли наш стек
        if (count($this->stack) < $this->limit) {
            // добавляем новый элемент в начало массива
            array_unshift($this->stack, $item);
        } else {
            throw new RunTimeException('Стек переполнен!');
        }
    }

    public function pop() {
        if ($this->isEmpty()) {
            // проверка на пустоту стека
	      throw new RunTimeException('Стек пуст!');
	  } else {
            // Извлекаем первый элемент массива
            return array_shift($this->stack);
        }
    }

    public function top() {
        return current($this->stack);
    }

    public function isEmpty() {
        return empty($this->stack);
    }

    public function prnt_arr() {
        // log_msg('Status in JSON - '.json_encode($this->stack));/////////////////////////////////////////////
        return json_encode($this->stack);
    }

    public function clear() {
        $this->stack = array();
    }

}
