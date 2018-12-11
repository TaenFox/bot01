<?php
// include_once('conf_hostland.php');
include_once('conf_lc.php');
// if(db_vk_check_user('2134')){echo'true';};
// db_vk_add_user('test','547');
// echo base64_decode(db_get_user('vk', '547')['name']);
// echo base64_decode(db_vk_get_user('547')['name']);
// echo db_ph_get_user('12358')['status'];
//--------------------------------------
function init_query_db()                      //функция для открытия запроса
{
    global $bd_host, $bd_usr, $bd_pas, $bd_base;
    $link = mysqli_connect($bd_host, $bd_usr, $bd_pas, $bd_base)
      or die("Ошибка " . mysqli_error($link));
    return $link;
}
function close_query_db($connect_db)          //функция для закрытия запроса
{
    mysqli_close($connect_db);
}
//--------------------------------------
function db_check_user($field, $param)          //общий метод для проверки наличия пользователя в системе
                                                //по параметрам (поле параметра -> значение)
{
    $query = init_query_db();
    $string_q = 'SELECT '.$field.' FROM users WHERE '.$field.' = \''.$param.'\';';
    // echo $string_q.'<br>';
    $result = mysqli_query($query, $string_q);
    $res_rows = mysqli_num_rows($result);
    close_query_db($query);
    if ($res_rows<>0)
    {
        // log_msg('Проверка пользователя - есть');
        return true;
    }else{
        // log_msg('Проверка пользователя - нет');
        return false;
    }
}
//--------------------------------------
function db_vk_check_user($vk_id)
{
    return db_check_user('vk', $vk_id);
}
//--------------------------------------
function db_ph_check_user($phone)
{
    return db_check_user('phone', $phone);
}
//--------------------------------------
function db_add_user($name, $field, $identify)  //общий метод для добавления пользователя в систему
                                                //по параметрам (имя юзера, поле параметра -> значение)
{
    $name = base64_encode($name);
    $query = init_query_db();
    $string_q ='INSERT INTO `users`
               (`name`, `'.$field.'`)
        VALUES (\''.$name.'\', \''.$identify.'\');';
    // log_msg($string_q);
    // echo $string_q.'<br>';
    mysqli_query($query, $string_q);
    $new_id = mysqli_insert_id($query);
    close_query_db($query);
    return $new_id;
}
//--------------------------------------
function db_vk_add_user ($vk_name, $vk_id)
{
    db_add_user($vk_name, 'vk', $vk_id);
}
//--------------------------------------
function db_ph_add_user($vk_name, $phone)
{
    db_add_user($vk_name, 'phone', $phone);
}
//--------------------------------------
function db_get_user($field, $param)            //общий метод получения информации по пользователю
                                                //из системы по одному из полей (поле параметра -> значение)
{
      $query = init_query_db();
      $string_q = 'SELECT * FROM users WHERE '.$field.' = \''.$param.'\';';
      // log_msg('запрос - '.$string_q);
      // echo $string_q.'<br>';
      $result = mysqli_query($query, $string_q);
      // log_msg(json_encode(mysqli_fetch_row($result)));
      $result = mysqli_fetch_assoc($result);
      close_query_db($query);
      return $result;
}
//--------------------------------------
function db_vk_get_user ($vk_id)
{
    return db_get_user('vk', $vk_id);
}
//--------------------------------------
function db_ph_get_user ($phone)
{
    return db_get_user('phone', $phone);
}
//--------------------------------------
function db_update_user ($user)
{
    $status = $user->status->prnt_arr();
    $query = init_query_db();
    $string_q = 'UPDATE `users` ';
    $string_q = $string_q.'SET `name` = \''.base64_encode($user->name).'\', ';
    $string_q = $string_q.'`vk` = \''.$user->vk.'\', ';
    $string_q = $string_q.'`phone` = \''.$user->phone.'\', ';
    $string_q = $string_q.'`status` = \''.$status.'\' ';
    $string_q = $string_q.'WHERE `users`.`id` = \''.$user->id.'\'';
    mysqli_query($query, $string_q);
    // log_msg('Updated');/////////////////////////////////////////////
    close_query_db($query);
}
//--------------------------------------
function db_check_debt($id)          //общий метод для проверки наличия записи в системе
                                     //по параметрам (поле параметра -> значение)
{
    // log_msg('1');
    $query = init_query_db();
    $string_q = 'SELECT id FROM debt WHERE id = \''.$id.'\';';
    // log_msg($string_q);
    $result = mysqli_query($query, $string_q);
    $res_rows = mysqli_num_rows($result);
    // log_msg('количество строк '.$res_rows);
    close_query_db($query);
    if ($res_rows<>0)
    {
        return true;
    }else{
        return false;
    }
}
//--------------------------------------
function db_add_debt()        //общий метод для добавления пользователя в систему
                                          //по параметрам (имя юзера, поле параметра -> значение)
{
    $user = new user('vk', VK_INPUT_MESSAGE_USER_ID);
    $user = $user->id;
    $query = init_query_db();
    $string_q ='INSERT INTO `debt`
               (`own`)
        VALUES (\''.$user.'\');';
    // log_msg($string_q);
    // echo $string_q.'<br>';
    mysqli_query($query, $string_q);
    $new_id = mysqli_insert_id($query);
    close_query_db($query);
    return $new_id;
}
//--------------------------------------
function db_get_debt($id)            //общий метод получения информации по пользователю
                                                //из системы по одному из полей (поле параметра -> значение)
{
    $query = init_query_db();
    $string_q = 'SELECT * FROM debt WHERE id = \''.$id.'\';';
    // echo $string_q.'<br>';
    $result = mysqli_query($query, $string_q);
    $result = mysqli_fetch_assoc($result);
    close_query_db($query);
    return $result;
}
//--------------------------------------
function db_delet_debt($id)
{
    $query = init_query_db();
    $string_q = 'DELETE FROM `debt` WHERE `debt`.`id` = '.$id.';';
    mysqli_query($query, $string_q);
    close_query_db($query);
}
//--------------------------------------
function db_update_debt ($debt)
{
    $status = $debt->status->prnt_arr();
    $query = init_query_db();
    $string_q = 'UPDATE `debt` ';
    $string_q = $string_q.'SET ';

    $string_q = $string_q.'`name` = \''.base64_encode($debt->name).'\', ';
    $string_q = $string_q.'`own` = \''.$debt->own.'\', ';
    $string_q = $string_q.'`revers` = \''.$debt->revers.'\', ';
    $string_q = $string_q.'`debtsum` = \''.$debt->debtsum.'\', ';
    $string_q = $string_q.'`dateadd` = \''.$debt->dateadd.'\', ';
    $string_q = $string_q.'`currency` = \''.$debt->currency.'\', ';
    $string_q = $string_q.'`status` = \''.$status.'\' ';

    $string_q = $string_q.'WHERE `debt`.`id` = \''.$debt->id.'\'';
    mysqli_query($query, $string_q);
    // log_msg($string_q);
    // log_msg($string_q);/////////////////////////////////////////////
    // log_msg('Updated');/////////////////////////////////////////////
    close_query_db($query);
}
//--------------------------------------
function db_check_share($id)          //общий метод для проверки наличия записи в системе
                                     //по параметрам (поле параметра -> значение)
{
    // log_msg('1');
    $query = init_query_db();
    $string_q = 'SELECT id FROM share WHERE id = \''.$id.'\';';
    // log_msg($string_q);
    $result = mysqli_query($query, $string_q);
    $res_rows = mysqli_num_rows($result);
    // log_msg('количество строк '.$res_rows);
    close_query_db($query);
    if ($res_rows<>0)
    {
        return true;
    }else{
        return false;
    }
}
//--------------------------------------
function db_add_share($debt)        //общий метод для добавления пользователя в систему
                                          //по параметрам (имя юзера, поле параметра -> значение)
{
    $query = init_query_db();
    $string_q ='INSERT INTO `share`
               (`debt`)
        VALUES (\''.$debt.'\');';
    // log_msg($string_q);
    // echo $string_q.'<br>';
    mysqli_query($query, $string_q);
    $new_id = mysqli_insert_id($query);
    close_query_db($query);
    return $new_id;
}
//--------------------------------------
function db_get_share($id)            //общий метод получения информации по пользователю
                                                //из системы по одному из полей (поле параметра -> значение)
{
    $query = init_query_db();
    $string_q = 'SELECT * FROM share WHERE id = \''.$id.'\';';
    // echo $string_q.'<br>';
    $result = mysqli_query($query, $string_q);
    $result = mysqli_fetch_assoc($result);
    close_query_db($query);
    return $result;
}
//--------------------------------------
function db_delet_share($id)
{
    $query = init_query_db();
    $string_q = 'DELETE FROM `share` WHERE `share`.`id` = '.$id.';';
    mysqli_query($query, $string_q);
    close_query_db($query);
}
//--------------------------------------
function db_update_share ($share)
{
    $status = $share->status->prnt_arr();
    $query = init_query_db();
    $string_q = 'UPDATE `share` ';
    $string_q = $string_q.'SET ';

    if (!is_null($share->name)){$string_q = $string_q.'`name` = \''.base64_encode($share->name).'\', ';};
    $string_q = $string_q.'`debt` = \''.$share->debt.'\', ';
    if (!is_null($share->own)){$string_q = $string_q.'`own` = \''.$share->own.'\', ';};
    if (!is_null($share->share)){$string_q = $string_q.'`share` = \''.$share->share.'\', ';};
    if (!is_null($share->dateclose)){$string_q = $string_q.'`dateclose` = \''.$share->dateclose.'\', ';};
    $string_q = $string_q.'`trust` = \''.$share->trust.'\', ';
    $string_q = $string_q.'`status` = \''.$status.'\' ';

    $string_q = $string_q.'WHERE `share`.`id` = \''.$share->id.'\'';
    mysqli_query($query, $string_q);
    // log_msg($string_q);/////////////////////////////////////////////
    // log_msg('Updated');/////////////////////////////////////////////
    close_query_db($query);
}
//--------------------------------------
function db_share_no_equal_sum($id_debt)
{
    $query = init_query_db();
    $string_q = 'SELECT sum(`share`) as sshare FROM `share` WHERE `debt` = '.$id_debt.' and `share` >= 1';
    $result = mysqli_query($query, $string_q);
    $result = mysqli_fetch_assoc($result);
    $result = $result['sshare'];
    close_query_db($query);
    return $result;
}
//--------------------------------------
function db_share_equal_count($id_debt)
{
    $query = init_query_db();
    $string_q = 'SELECT COUNT(`id`) as scount FROM `share` WHERE `share` = 0 and `debt` = '.$id_debt;
    $result = mysqli_query($query, $string_q);
    $result = mysqli_fetch_assoc($result);
    $result = $result['scount'];
    close_query_db($query);
    return $result;
}
//--------------------------------------
function db_share_equal_perc($id_debt)
{
    $query = init_query_db();
    $string_q = 'SELECT sum(`share`) as sshare FROM `share` WHERE `share` > 0
                and `share` < 1 and `debt` = '.$id_debt;
    $result = mysqli_query($query, $string_q);
    $result = mysqli_fetch_assoc($result);
    $result = $result['sshare'];
    close_query_db($query);
    return $result;
}
//--------------------------------------
function db_debt_in_table($user_id)
{
    $query = init_query_db();
    $string_q =
   '(SELECT `debt`.`id` as debt_id, `share`.`id` as share_id, `share`.`own` as own_id
    FROM `users` JOIN
        (`debt` JOIN `share` ON `debt`.`id` = `share`.`debt`)
                                  ON `users`.`id` = `debt`.`own`
    WHERE `debt`.`revers` = 1 and `users`.`id` = ' . $user_id .
          ' and `share`.`own` <> ' . $user_id . ' and `share`.`id` is not null
          ORDER BY `share`.`own`)
    UNION
    (SELECT `debt`.`id` as debt_id, `share`.`id` as share_id, `debt`.`own` as own_id
     FROM `users` JOIN
         (`debt` JOIN `share` ON `debt`.`id` = `share`.`debt`)
                                   ON `users`.`id` = `debt`.`own`
     WHERE `debt`.`revers` = 0 and `users`.`id` <> ' . $user_id .
           ' and `share`.`own` = ' . $user_id . ' and `share`.`id` is not null
           ORDER BY `share`.`own`)';
    // log_msg($string_q);
    $result = mysqli_query($query, $string_q);
    $num_row = mysqli_num_rows($result);
    $res_arr = array();
    for ($i=0; $i < $num_row; $i++)
    {
        $res_arr[$i] = mysqli_fetch_assoc($result);
    }
    // $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
    close_query_db($query);
    return $res_arr;
}
//--------------------------------------
function db_debt_out_table($user_id)
{
    $query = init_query_db();
    $string_q =
   '(SELECT `debt`.`id` as debt_id, `share`.`id` as share_id, `share`.`own` as own_id
    FROM `users` JOIN
        (`debt` JOIN `share` ON `debt`.`id` = `share`.`debt`)
                                  ON `users`.`id` = `debt`.`own`
    WHERE `debt`.`revers` = 0 and `users`.`id` = ' . $user_id .
          ' and `share`.`own` <> ' . $user_id . ' and `share`.`id` is not null
          ORDER BY `share`.`own`)
    UNION
    (SELECT `debt`.`id` as debt_id, `share`.`id` as share_id, `debt`.`own` as own_id
     FROM `users` JOIN
         (`debt` JOIN `share` ON `debt`.`id` = `share`.`debt`)
                                   ON `users`.`id` = `debt`.`own`
     WHERE `debt`.`revers` = 1 and `users`.`id` <> ' . $user_id .
           ' and `share`.`own` = ' . $user_id . ' and `share`.`id` is not null
           ORDER BY `share`.`own`)';
    // log_msg($string_q);
    $result = mysqli_query($query, $string_q);
    $num_row = mysqli_num_rows($result);
    $res_arr = array();
    for ($i=0; $i < $num_row; $i++)
    {
        $res_arr[$i] = mysqli_fetch_assoc($result);
    }
    // $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
    close_query_db($query);
    return $res_arr;
}
//--------------------------------------
//--------------------------------------
