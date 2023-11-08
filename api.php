<?php
function connect_to_db()
{
   $conn = mysqli_connect("IP:Port","user","password","db");
   $conn->set_charset("utf8");
   if (mysqli_connect_errno()){echo "Произошла ошибка! Мы скоро это исправим.";}
   return $conn;
}

function check_token($conn)
{
   if (!empty($_SESSION['Token']))
   {
      $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT Token FROM klienty WHERE Login = '".$_SESSION['Username']."'"));
      if($check['Token'] != $_SESSION['Token']) {session_unset(); session_destroy(); $_SESSION['Status'] = 'Гость'; header("Location: login.php");}
   }
}

function save_logs($conn, $action)
{
   if (!empty($_SESSION['Status'])){$session_status = $_SESSION['Status'];}
   $ip_adress = filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP', FILTER_VALIDATE_IP)
       ?: filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', FILTER_VALIDATE_IP)
       ?? $_SERVER['REMOTE_ADDR'];
   $datetime = date("Y-m-d G:i:s");
   if(!empty($_SESSION['Username'])){$session_username = $_SESSION['Username'];}else{$session_username = 'Незарегистрированный'; $session_status = 'Гость';}
   $sql ="INSERT INTO logs (`IP`, `User`, `Activity`, `Status`, `Datetime`) VALUES ('$ip_adress', '$session_username', '$action', '$session_status', '$datetime')";
   mysqli_query($conn, $sql);
}

function filters_count($conn)
{
    //Бухучёт
    $result = mysqli_query($conn, "SELECT count(*) FROM onlineqest WHERE Tema='Бухучёт'");
    $count_Acc = mysqli_fetch_array($result)[0];
    //Ликвидация
    $result = mysqli_query($conn, "SELECT count(*) FROM onlineqest WHERE Tema='Ликвидация'");
    $count_Liq = mysqli_fetch_array($result)[0];
    //Налоги
    $result = mysqli_query($conn, "SELECT count(*) FROM onlineqest WHERE Tema='Налоги'");
    $count_Tax = mysqli_fetch_array($result)[0];
    //Зарплата
    $result = mysqli_query($conn, "SELECT count(*) FROM onlineqest WHERE Tema='Зарплата'");
    $count_Sal = mysqli_fetch_array($result)[0];
    //Документация
    $result = mysqli_query($conn, "SELECT count(*) FROM onlineqest WHERE Tema='Документы'");
    $count_Doc = mysqli_fetch_array($result)[0];
    //Лицензирование
    $result = mysqli_query($conn, "SELECT count(*) FROM onlineqest WHERE Tema='Лицензирование'");
    $count_Lic = mysqli_fetch_array($result)[0];
    //Оценка
    $result = mysqli_query($conn, "SELECT count(*) FROM onlineqest WHERE Tema='Оценка'");
    $count_Eval = mysqli_fetch_array($result)[0];
    //Другое
    $result = mysqli_query($conn, "SELECT count(*) FROM onlineqest WHERE Tema='Другое'");
    $count_Else = mysqli_fetch_array($result)[0];
    //Всё
    $result = mysqli_query($conn, "SELECT count(*) FROM onlineqest");
    $count_all = mysqli_fetch_array($result)[0];
    //Открытые
    $result = mysqli_query($conn, "SELECT count(*) FROM onlineqest WHERE Status='Открыто'");
    $count_open = mysqli_fetch_array($result)[0];
    //Кол. пользователей
    $result = mysqli_query($conn, "SELECT count(*) FROM klienty");
    $count_users = mysqli_fetch_array($result)[0];
   return [$count_Acc, $count_Liq, $count_Tax, $count_Sal, $count_Doc, $count_Lic, $count_Eval, $count_Else, $count_all, $count_open, $count_users];
}

function test_input($data)
{
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}

function get_filter()
{
   if (strpos($_SERVER['REQUEST_URI'], '?showaccouting')){$sql = "SELECT * FROM Onlineqest WHERE Tema='Бухучет' ORDER BY ID DESC";}
   elseif (strpos($_SERVER['REQUEST_URI'], '?showliquidation')){$sql = "SELECT * FROM Onlineqest WHERE Tema='Ликвидация' ORDER BY ID DESC";}
   elseif (strpos($_SERVER['REQUEST_URI'], '?showtaxes')){$sql = "SELECT * FROM Onlineqest WHERE Tema='Налоги' ORDER BY ID DESC";}
   elseif (strpos($_SERVER['REQUEST_URI'], '?showsalary')){$sql = "SELECT * FROM Onlineqest WHERE Tema='Зарплата' ORDER BY ID DESC";}
   elseif (strpos($_SERVER['REQUEST_URI'], '?showdocumentation')){$sql = "SELECT * FROM Onlineqest WHERE Tema='Документы' ORDER BY ID DESC";}
   elseif (strpos($_SERVER['REQUEST_URI'], '?showelse')){$sql = "SELECT * FROM Onlineqest WHERE Tema='Другое' ORDER BY ID DESC";}
   elseif (strpos($_SERVER['REQUEST_URI'], '?showopen')){$sql = "SELECT * FROM Onlineqest WHERE Status='Открыто' ORDER BY ID DESC";}
   elseif (strpos($_SERVER['REQUEST_URI'], '?showlicense')){$sql = "SELECT * FROM Onlineqest WHERE Tema='Лицензирование' ORDER BY ID DESC";}
   elseif (strpos($_SERVER['REQUEST_URI'], '?showeval')){$sql = "SELECT * FROM Onlineqest WHERE Tema='Оценка' ORDER BY ID DESC";}
   else {$sql = "SELECT * FROM Onlineqest ORDER BY ID DESC";}
   return $sql;
}

function send_like($conn, $like_id)
{
    if (isset($_SESSION['Likes']["'$like_id'"]))
    {
        if ($_SESSION['Likes']["'$like_id'"] == 1)
        {
            $result = mysqli_query($conn, "SELECT `Likes` FROM onlineqest WHERE `ID`='$like_id'");
            $count_likes = mysqli_fetch_array($result)[0] - 1;
            $result = mysqli_query($conn, "UPDATE onlineqest SET `Likes`=$count_likes WHERE `ID`='$like_id'");
            $_SESSION['Likes']["'$like_id'"] = 0;
        } 
        else 
        {
            $_SESSION['Likes']["'$like_id'"] = 1;
            $result = mysqli_query($conn, "SELECT `Likes` FROM onlineqest WHERE `ID`='$like_id'");
            $count_likes = mysqli_fetch_array($result)[0] + 1;
            $result = mysqli_query($conn, "UPDATE onlineqest SET `Likes`=$count_likes WHERE `ID`='$like_id'");
        }
    }
    else
    {
        $_SESSION['Likes']["'$like_id'"] = 1;
        $result = mysqli_query($conn, "SELECT `Likes` FROM onlineqest WHERE `ID`='$like_id'");
        $count_likes = mysqli_fetch_array($result)[0] + 1;
        $result = mysqli_query($conn, "UPDATE onlineqest SET `Likes`=$count_likes WHERE `ID`='$like_id'");
    }
}
    
function send_dislike($conn, $dislike_id)
{
    if (isset($_SESSION['Dislikes']["'$dislike_id'"]))
    {
        if ($_SESSION['Dislikes']["'$dislike_id'"] == 1)
        {
            $_SESSION['Dislikes']["'$dislike_id'"] = 0;
            $result = mysqli_query($conn, "SELECT `Dislikes` FROM onlineqest WHERE `ID`='$dislike_id'");
            $count_dislikes = mysqli_fetch_array($result)[0] - 1;
            $result = mysqli_query($conn, "UPDATE onlineqest SET `Dislikes`=$count_dislikes WHERE `ID`='$dislike_id'");
        } 
        else 
        {
            $_SESSION['Dislikes']["'$dislike_id'"] = 1;
            $result = mysqli_query($conn, "SELECT `Dislikes` FROM onlineqest WHERE `ID`='$dislike_id'");
            $count_dislikes = mysqli_fetch_array($result)[0] + 1;
            $result = mysqli_query($conn, "UPDATE onlineqest SET `Dislikes`=$count_dislikes WHERE `ID`='$dislike_id'");
        }
    } 
    else 
    {
        $_SESSION['Dislikes']["'$dislike_id'"] = 1;
        $result = mysqli_query($conn, "SELECT `Dislikes` FROM onlineqest WHERE `ID`='$dislike_id'");
        $count_dislikes = mysqli_fetch_array($result)[0] + 1;
        $result = mysqli_query($conn, "UPDATE onlineqest SET `Dislikes`=$count_dislikes WHERE `ID`='$dislike_id'");
    }
}

function filters_count_cli($conn)
{
    $result = mysqli_query($conn, "SELECT count(*) FROM onlineqest");
    $count_questions = mysqli_fetch_array($result)[0];
    $result = mysqli_query($conn, "SELECT count(*) FROM klienty");
    $count_all = mysqli_fetch_array($result)[0];
    $result = mysqli_query($conn, "SELECT count(*) FROM klienty WHERE Status='Пользователь'");
    $count_users = mysqli_fetch_array($result)[0];
    $result = mysqli_query($conn, "SELECT count(*) FROM klienty WHERE Status='Гость'");
    $count_guests = mysqli_fetch_array($result)[0];
    $result = mysqli_query($conn, "SELECT count(*) FROM klienty WHERE Status='Менеджер'");
    $count_managers = mysqli_fetch_array($result)[0];
    $result = mysqli_query($conn, "SELECT count(*) FROM klienty WHERE Status='Администратор'");
    $count_administrators = mysqli_fetch_array($result)[0];
    return [$count_questions, $count_all, $count_users, $count_guests, $count_managers, $count_administrators];
}

function get_filter_cli()
{
    if (isset($_GET['showusers'])){$sql = "SELECT * FROM klienty WHERE Status='Пользователь' ORDER BY ID DESC";}
    elseif (isset($_GET['showmanagers'])){$sql = "SELECT * FROM klienty WHERE Status='Менеджер' ORDER BY ID DESC";}
    elseif (isset($_GET['showadmins'])){$sql = "SELECT * FROM klienty WHERE Status='Администратор' ORDER BY ID DESC";} 
    elseif (isset($_GET['showguests'])){$sql = "SELECT * FROM klienty WHERE Status='Гость' ORDER BY ID DESC";}
    else {$sql = "SELECT * FROM klienty ORDER BY ID DESC";}
   return $sql;
}

function filters_count_logs($conn)
{
   $result = mysqli_query($conn, "SELECT count(*) FROM onlineqest");
   $count_questions = mysqli_fetch_array($result)[0];
   $result = mysqli_query($conn, "SELECT count(*) FROM klienty");
   $count_clients = mysqli_fetch_array($result)[0];
   $result = mysqli_query($conn, "SELECT count(*) FROM logs");
   $count_all = mysqli_fetch_array($result)[0];
   $result = mysqli_query($conn, "SELECT count(*) FROM logs WHERE Status='Пользователь'");
   $count_users = mysqli_fetch_array($result)[0];
   $result = mysqli_query($conn, "SELECT count(*) FROM logs WHERE Status='Гость'");
   $count_guests = mysqli_fetch_array($result)[0];
   $result = mysqli_query($conn, "SELECT count(*) FROM logs WHERE Status='Менеджер'");
   $count_managers = mysqli_fetch_array($result)[0];
   $result = mysqli_query($conn, "SELECT count(*) FROM logs WHERE Status='Администратор'");
   $count_administrators = mysqli_fetch_array($result)[0];
   return [$count_questions, $count_clients, $count_all, $count_users, $count_guests, $count_managers, $count_administrators];
}
function get_filter_logs()
{
   if (isset($_GET['showusers'])) {$sql = "SELECT * FROM logs WHERE Status='Пользователь' ORDER BY ID DESC";} 
   else if (isset($_GET['showmanagers'])) {$sql = "SELECT * FROM logs WHERE Status='Менеджер' ORDER BY ID DESC";} 
   else if (isset($_GET['showadmins'])) {$sql = "SELECT * FROM logs WHERE Status='Администратор' ORDER BY ID DESC";} 
   else if (isset($_GET['showguests'])) { $sql = "SELECT * FROM logs WHERE Status='Гость' ORDER BY ID DESC";} 
   else {$sql = "SELECT * FROM logs ORDER BY ID DESC";}
   return $sql;
}
?>