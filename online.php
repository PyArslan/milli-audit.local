<?php 
include "api.php";
// Требуем наличие этих файлов для отправки почты напрямую (если только на один - два email'а, если больше то через оставление файла на сервере)
use PHPMailer\PHPMailer\PHPMailer;
require __DIR__.'/mailer/PHPMailer.php';
require __DIR__.'/mailer/SMTP.php';


// Начало сессии
session_start();
// Если пользователь нажмёт на Выход то начинается очищение его сессии
if (strpos($_SERVER['REQUEST_URI'], '?exit')){session_start(); session_unset(); session_destroy(); $_SESSION['Status'] = 'Гость'; header('Location: /online.php');}
date_default_timezone_set('Asia/Ashgabat');
// Подключение к БД

$conn = connect_to_db();

// Проверка токена
check_token($conn);

// Запись Логов о входе на станицу
save_logs($conn, "online.php");

// Взятие количества вопросов по фильтрам
list($count_Acc, $count_Liq, $count_Tax, $count_Sal, $count_Doc, $count_Lic, $count_Eval, $count_Else, $count_all, $count_open, $count_users) = filters_count($conn);
// --------------------- Регистрация ---------------------- \\


if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['Form']) && $_POST['Form'] == 'Sign_Up_Form'))
{
    $Error = '';
    $Fio_Err=$Phone_Err=$Activity_Err=$Email_Err='';
    $Fio=$Phone=$Activity=$Email=$Login=$Password='';
    // Validation 1
    // Fioname
    if (empty($_POST['Fio'])) {$Fio_Err = 'Укажите ФИО или Название компании'; $Error = "Error";} 
    else {$Fio = test_input($_POST['Fio']);}
    // Phone
    if (empty($_POST['Phone'])) {$Phone_Err = 'Укажите телефон';  $Error = "Error";} 
    else {$Phone = test_input($_POST['Phone']);}
    // Activity
    if (empty($_POST['Activity'])) {$Activity_Err = 'Укажите вид деятельности';  $Error = "Error";} 
    else {$Activity = test_input($_POST['Activity']);}
    // Email
    if (empty($_POST['Email'])) {$Email_Err = 'Укажите Email';  $Error = "Error";} 
    elseif(!filter_var($_POST['Email'], FILTER_VALIDATE_EMAIL)) {$Email_Err = 'Некорректный Email';  $Error = "Error";}
    else {$Email = test_input($_POST['Email']);}
    // Login
    $Login = 'empty';
    // Password	
    $Password = 'empty';
    $Status = test_input($_POST['Status']);
    $Datestamp = test_input($_POST['Datestamp']);
    
    // Email unique validation
    if (empty($Email_Err))
    {
        $check = mysqli_query($conn, "SELECT Login FROM `klienty` WHERE Email='$Email'");
        if (mysqli_num_rows($check) > 0){$Email_Err = 'Email уже зарегистрирован в базе';  $Error = "Error";}
    }
    
    $var_array = [$Fio,$Phone,$Activity,$Email,$Login,$Password,$Status,$Datestamp];
    // Validation 2
    
    foreach ($var_array as $i)
    {
        if (empty($i) and $i!='0')
        {
            $Error = 'Error!';
            break;
        }
    }
    if (empty($Error))
    {
        $Fio = mysqli_real_escape_string($conn, $Fio);
        $Phone = mysqli_real_escape_string($conn, $Phone);
        $Activity = mysqli_real_escape_string($conn, $Activity);
        $Email = mysqli_real_escape_string($conn, $Email);
        $sql = "INSERT `klienty` (`Fio`, `Phone`, `Activity`, `Email`, `Login`, `Password`, `Datestamp`, `Status`) VALUES ('$Fio', '$Phone', '$Activity', '$Email', '$Login', '$Password', '$Datestamp', '$Status')";
        
        $result = mysqli_query($conn, $sql);
		
        // ------------------- mail ----------------- \\
        foreach (['example1@gmail.com', 'example2@mail.ru'] as $email)
        {
            $mailfrom = 'MAIL';
            $subject = 'Новый пользователь Milli-Audit';
            $message = "Фио: $Fio";
            $message .= "\n\nРод деятельности: $Activity";
            $message .= "\n\nE-mail: $Email";
            $message .= "\n\nТелефон: $Phone";
            $message .= "\n\nДата: $Datestamp";
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->CharSet = "utf-8";
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;
            $mail->SMTPAuth = true;
            $mail->Username = 'MAIL';
            $mail->Password = 'PASSWORD';
            $mail->SMTPSecure = 'tsl';
            $mail->From = $mailfrom;
            $mail->FromName = $mailfrom;
            $mail->AddAddress($newemail, "");
            $mail->AddAddress($email, "");
            $mail->AddReplyTo($mailfrom);
            $mail->Body = stripslashes($message);
            $mail->Subject = stripslashes($subject);
            $mail->WordWrap = 80;
            if (!$mail->Send()) {die('PHPMailer error: ' . $mail->ErrorInfo);}  
        } header('Location: /success.html');
    } else {header("Location: /error.php?Fio=$Fio_Err&Phone=$Phone_Err&Activity=$Activity_Err&Email=$Email_Err");}
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['formid']) && $_POST['formid'] == 'layerqest')
{
    $Wopros = mysqli_real_escape_string($conn, test_input($_POST['Wopros']));
    $Tema = mysqli_real_escape_string($conn, test_input($_POST['Tema']));
    $Datetime = mysqli_real_escape_string($conn, test_input($_POST['Datetime']));
    $DATESTAMP = date('Y-m-d');
    $FORMID = mysqli_real_escape_string($conn, test_input($_POST['formid']));
    $BROWSER = mysqli_real_escape_string($conn, test_input($_SERVER['HTTP_USER_AGENT']));
    $Time = date("G:i:s");
    $IP = mysqli_real_escape_string($conn,test_input($_SERVER['REMOTE_ADDR']));
    
    $Error = '';
    if (empty(trim($Tema))){$Err_Tema = 'Выберите тему'; $Error = 'Error';}
    if (empty(trim($Wopros))){$Err_Wopros = 'Вопрос не должен быть пустым'; $Error = 'Error';}
    
    if (empty($Error))
    {
        $sql = "INSERT INTO onlineqest (`Wopros`, `Tema`, `Datetime`, `DATESTAMP`, `FORMID`, `Browser`, `Time`, `IP`, `Status`) VALUES ('$Wopros', '$Tema', '$Datetime', '$DATESTAMP', '$FORMID', '$BROWSER', '$Time', '$IP', 'Открыто')";
        $result = mysqli_query($conn, $sql);
        
        // Взятие последнего id после отправки вопроса
        $id = mysqli_insert_id($conn);
        // Формирование текста
        $text = "$id|";
        $text .= "Дата: $Datetime|";
        $text .= "Тема: $Tema|";
        $text .= "Вопрос: $Wopros|";
        $text .= "http://milli-audit.com/online.php|";
        $text .= "С уважением, ХО Milli-Audit|";
        $text .= "+99365 56 82 39\n+99312 94 50 64|";
        $text .= "Туркменистан, Ашхабад ул. Андалиб  дом 182 (2 этаж) Бизнес-Центр 'Diamond international'|";
        $text .= "milli_audit@mail.ru\nmilli.audit2015@gmail.com\nwww.milli-audit.com|";
        // Запись в файл для рассылки
        $file = fopen("mailer/new_question_".date('Y-m-d')."_".date('G-i-s')."_.txt","w") or die("Unable to open file!");
        fwrite($file, iconv("UTF-8", "WINDOWS-1251", $text));
        $datetime = date("Y-m-d G:i:s");
        //Запись логов
        save_logs($conn, "Задал вопрос № $id");
        header("Location: online.php");
    } else {header("Location: /error.php?Tema=$Err_Tema&Wopros=$Err_Wopros");}
}

// ----------------------- Like System ------------------------ \\
if (isset($_GET['like']))
{
    $like_id = $_GET['like'];
    send_like($conn, $like_id);
    header("Location: online.php");
}
if (isset($_GET['dislike']))
{
    $dislike_id = $_GET['dislike'];
    send_dislike($conn, $dislike_id);
    header("Location: online.php");
}
?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Milli Audit - Online</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="malogo.jpg" rel="shortcut icon" type="image/x-icon">
<link href="css/font-awesome.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap" rel="stylesheet">
<link href="css/Millaudit.css" rel="stylesheet">
<link href="css/online.css" rel="stylesheet">
<script src="jquery-1.12.4.min.js"></script>
<script src="popper.min.js"></script>
<script src="util.min.js"></script>
<script src="collapse.min.js"></script>
<script src="dropdown.min.js"></script>
<script src="skrollr.min.js"></script>
<script src="wb.stickylayer.min.js"></script>
<script src="wwb17.min.js"></script>
<!-- Yandex.Metrika counter -->
      <noscript><div><img src="https://mc.yandex.ru/watch/95123413" style="position:absolute; left:-9999px;" alt=""/></div></noscript>
<!-- /Yandex.Metrika counter -->
<script src="online.js"></script>
</head>
<body>
   <form name="Layer1" method="post" action="<?php echo basename(__FILE__); ?>" enctype="multipart/form-data" accept-charset="UTF-8" id="Layerqest" style="position:absolute;text-align:center;visibility:hidden;left:970px;top:653px;width:236px;height:330px;z-index:31;">
      <input type="hidden" name="formid" value="layerqest" id="formid">
      <div id="Layerqest_Container" style="width:236px;position:relative;margin-left:auto;margin-right:auto;text-align:left;">
         <textarea name="Wopros" id="TextArea1" style="position:absolute;left:10px;top:90px;width:200px;height:142px;z-index:0;" rows="9" cols="22" spellcheck="false" placeholder="Напишите в свободной форме Ваш вопрос или опишите проблему"></textarea>
         <input type="submit" id="Button1" name="" value="Отправить" style="position:absolute;left:10px;top:260px;width:100px;height:30px;z-index:1;">
         <input type="text" id="Datetime" style="position:absolute;left:10px;top:300px;width:216px;visibility:hidden;height:16px;z-index:2;" name="Datetime" value="<?php echo trim(date('Y-m-d H:i:s')) ?>" spellcheck="false">
         <div id="wb_Text3" style="position:absolute;left:20px;top:10px;width:190px;height:19px;text-align:center;z-index:3;">
            <span style="color:#004A99;font-family:Arial;font-size:17px;"><strong>Отправить вопрос!</strong></span></div>
         <select name="Tema" size="1" id="Combobox1" style="position:absolute;left:10px;top:50px;width:210px;height:28px;z-index:4;">
            <option value="">Категория вопроса:</option>
            <option value="Бухучет">Бухучет</option>
            <option value="Ликвидация">Ликвидация</option>
            <option value="Налоги">Налоги</option>
            <option value="Зарплата">Зарплата</option>
            <option value="Документация">Документация</option>
            <option value="Лицензирование">Лицензирование</option>
            <option value="Оценка">Оценка</option>
            <option value="Другое">Другое</option>
         </select>
         <input type="submit" id="Button2" onclick="ShowObject('Layerqest', 0);return false;" name="" value="Отмена" style="position:absolute;left:120px;top:260px;width:100px;height:30px;z-index:5;">
      </div>
   </form>
   <div id="wb_header">
      <div id="header">
         <div class="col-1">
            <nav id="wb_headerMenu" style="display:inline-block;width:100%;z-index:6;" data--800-top="padding-top:4px;padding-bottom:4px;" data-top="padding-top:16px;padding-bottom:16px;">
               <div id="headerMenu" class="headerMenu" style="width:100%;height:auto !important;">
                  <div class="container">
                     <div class="navbar-header">
                        <button title="Hamburger Menu" type="button" class="navbar-toggle" data-toggle="collapse" data-target=".headerMenu-navbar-collapse">
                           <span class="icon-bar"></span>
                           <span class="icon-bar"></span>
                           <span class="icon-bar"></span>
                        </button>
                     </div>
                     <div class="headerMenu-navbar-collapse collapse">
                        <ul class="nav navbar-nav">
                           <li class="nav-item">
                              <a href="./main.html" title="Перехлд на главную страницу сайта" class="nav-link"><i class="fa fa-home"></i>На главную</a>
                           </li>
                           <li class="nav-item">
                              <a href="https://turkmenistan.gov.tm/ru/razdel/zakonodatelstvo" target="_blank" title="Законы Туркменистана публикуеме в официальных источниках" class="nav-link"><i class="fa fa-file-text"></i>Законы</a>
                           </li>
                           <li class="nav-item">
                              <a href="https://docs.google.com/spreadsheets/d/e/2PACX-1vRXS4REsmOKb_KLU6hOO4Kt0z1lCii0yFGDzt8CMAHXXps7vUv8utrBabodPxjYUy3HMDQ1mCY9zbOh/pubhtml?gid=241406301&single=true" target="_blank" title="Наборы документов положений и законов необходимых для осуществления деятельности" class="nav-link"><i class="fa fa-book"></i>Справочник</a>
                           </li>
                           <li class="nav-item">
                              <a href="./tarifs.php" title="Скидка c учетом продожительности подписки" class="nav-link"><i class="fa fa-calculator"></i>Тариф</a>
                           </li>
                           <li class="nav-item">
                              <a href="javascript:popupwnd('','no','no','no','no','no','no','500','300','500','800')" target="_self" title="Вы можете задать вопрос в свободной форме, ответы Вы получите на указанную Вами почту в течении дня." class="nav-link" onclick="ShowObject('Layerqest', 1);return false;"><i class="fa fa-hand-paper-o"></i>Задать вопрос (<?php echo $count_all;?> вопр.)</a>
                           </li>
                           <li class="nav-item dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user-circle-o"></i>Логин<b class="caret"></b></a>
                              <ul class="dropdown-menu">
                                 <li class="nav-item dropdown-item">
                                    <a href="javascript:popupwnd('','no','no','no','no','no','no','700','700','500','800')" target="_self" title="Регистрация позволит Вам получить доступ к всем функциям сервиса бесплатно в течении 7 дней. " class="nav-link" onclick="ShowObject('Layer1', 1);return false;"><i class="fa fa-user-plus"></i>Регистрация<?php echo " ($count_users чел.)";?></a>
                                 </li>
                                 <li class="nav-item dropdown-item">
                                    <a href="./login.php" class="nav-link"><i class="fa fa-user-circle"></i><?php if(isset($_SESSION['Username'])){ echo $_SESSION['Username'];} else {echo 'Вход';}?></a>
                                 </li>
                                 <li class="nav-item dropdown-item">
                                    <a href="?exit" class="nav-link"><i class="fa fa-sign-out"></i>Выход</a>
                                 </li>
                                 <li class="nav-item dropdown-item">
                                    <a href="./admin.php" title="Администрирование вопросов" class="nav-link"><i class="fa fa-expeditedssl"></i><?php if(isset($_SESSION['Status']) && in_array($_SESSION['Status'],['Администратор','Менеджер'])){echo 'Admin';}else{echo 'Недоступно';}?></a>
                                 </li>
                              </ul>
                           </li>
                        </ul>
                     </div>
                  </div>
               </div>
            </nav>
         </div>
      </div>
   </div>
   <div id="wb_LayoutGrid3">
      <div id="LayoutGrid3">
         <div class="row">
            <div class="col-1">
               <div id="wb_Heading1" style="display:inline-block;width:100%;z-index:7;">
                  <h1 id="Heading1">Вопросы и ответы!</h1>
               </div>
               <div id="wb_Text5">
                  <span style="color:#16409F;font-family:Arial;font-size:12px;">Услуга осуществляются на основании лицензии № 1-17-29-59 от 15 июля 2022 г. выданном министерством экономики и финансов Туркменистана</span>
               </div>
               <div id="wb_Text6">
                  <span style="color:#16409F;font-family:Arial;font-size:11px;">Тех.поддержка: +993 64 93 04 67</span>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div id="wb_LayoutGrid4">
      <div id="LayoutGrid4">
         <div class="row">
            <div class="col-1">
               <a id="Acc" href="?showaccouting" style="display:inline-block;width:109px;height:35px;z-index:10;margin-right:5px;">Бухчёт (<?php echo $count_Acc; ?>)</a>
               <a id="Liq" href="?showliquidation" style="display:inline-block;width:109px;height:35px;z-index:11;margin-right:5px;">Ликвидация (<?php echo $count_Liq; ?>)</a>
               <a id="Tax" href="?showtaxes" style="display:inline-block;width:109px;height:35px;z-index:12;margin-right:5px;">Налоги (<?php echo $count_Tax; ?>)</a>
               <a id="Sal" href="?showsalary" style="display:inline-block;width:109px;height:35px;z-index:13;margin-right:5px;">Зарплата (<?php echo $count_Sal; ?>)</a>
               <a id="Doc" href="?showdocumentation" style="display:inline-block;width:109px;height:35px;z-index:14;margin-right:5px;">Документы (<?php echo $count_Doc; ?>)</a>
               <a id="Lic" href="?showlicense" style="display:inline-block;width:130px;height:35px;z-index:15;margin-right:5px;">Лицензирование (<?php echo $count_Lic; ?>)</a>
               <a id="Eval" href="?showeval" style="display:inline-block;width:109px;height:35px;z-index:16;margin-right:5px;">Оценка (<?php echo $count_Eval; ?>)</a>
               <a id="Else" href="?showelse" style="display:inline-block;width:109px;height:35px;z-index:17;margin-right:5px;">Другое (<?php echo $count_Else; ?>)</a>
               <a id="All" href="?showall" style="display:inline-block;width:114px;height:35px;z-index:18;">Все (<?php echo $count_all; ?>)</a>
               <a id="Open" href="?showopen" style="display:inline-block;width:109px;height:35px;z-index:19;">Открытые (<?php echo $count_open; ?>)</a>
            </div>
         </div>
      </div>
   </div>
   <div id="wb_LayoutGrid1">
      <div id="LayoutGrid1">
         <div class="row">
            <div class="col-1">
<!-- Вопросы и Ответы! -->
               <div id="Html2" style="display:inline-block;width:100%;height:878px;overflow:scroll;z-index:20">
<?php
// После SELECT указываем нужные столбцы и после FROM Таблицу WHERE Столбец='значение' ORDER BY Столбец DESC(С новых, без DESC со старых) 
$sql = get_filter();
      
$result = mysqli_query($conn, $sql);
$status_array = ['Администратор','Менеджер','Пользователь'];

while($row = mysqli_fetch_array($result))
{
    // подключение строк из таблицы ( $row['здесь название нужной строки из базы'] )
    echo  '   <div style="background:AliceBlue;border: 3px solid DarkBlue; border-radius: 4px; max-width: 80%; margin:auto; margin-bottom: 15px;">
    <div style="font-size: 14px;text-align:left;margin-left:10px;padding-top:10px;color:grey;">'.DateTime::createFromFormat('Y-m-d H:i:s.u', $row['Datetime'])->format('d.m.Y H:i:s').'</div>
        <div style="font-size: 17px;padding-top:10px;padding-bottom:10px;text-align:left;margin-left:10px;"><b>№ '.$row['ID'].'</b></div>
        
        <div style="font-size:17px; text-align:left; padding-bottom:10px; margin-left:10px;"><b>Тема:</b> '.$row['Tema'].'</div>
        <hr style="background:DarkBlue;height:2px;">
        
        <div style="font-size:17px; text-align:left; padding-bottom:10px; padding-top:10px; margin-left:10px;"><b>Вопрос:</b> '.$row['Wopros'].'</div>      
        <hr style="background:DarkBlue;height:2px;">';
        
    if (in_array($_SESSION['Status'], $status_array))
    {
        echo '<div style="font-size:17px; text-align:left; padding-bottom:10px; padding-top:10px; margin-left:10px;"><b>Ответ:</b> '.$row['Otwet'].'</div>     
        <hr style="background:DarkBlue;height:2px;">
        
        <div style="font-size:17px; text-align:left; padding-bottom:10px; padding-top:10px; margin-left:10px;"><b>Рекомендация:</b> '.$row['Rekomend'].'</div>
        <hr style="background:DarkBlue;height:2px;">
        
        <div style="font-size:17px; text-align:left; padding-bottom:10px; padding-top:10px; margin-left:10px;"><b>Ссылка на законодательные акты:</b> '.$row['Webssylka'].'</div>
        <hr style="background:DarkBlue;height:2px;">
        
        <div style="font-size:17px; text-align:left; padding-bottom:15px; padding-top:15px; margin-left:10px;">
                    <div style="display: inline-block; text-align: left; width: 49%;"><a href="for-print.php?id='.$row['ID'].'" target="_blank" style="margin-left: 10px; text-decoration: none; color: black; font-weight: bold; border: 3px solid DarkBlue; border-radius: 4px; padding: 5px;">Печать</a></div>
                    <div style="display: inline-block; text-align: right; width: 46%;">
                        <a href="?like='.$row['ID'].'" style="text-decoration: none;">&#128077</a> <span style="color: mediumseagreen;">+'.$row['Likes'].'</span>
                        <a href="?dislike='.$row['ID'].'" style="text-decoration: none;">&#128078</a> <span style="color: red;">-'.$row['Dislikes'].'</span>
                    </div>
        </div>';
    } 
    else 
    {
        echo '<div style="font-size:17px; text-align:left; padding-bottom:10px; padding-top:10px; margin-left:10px; color: #4169E1;"><b>Ответ:</b> Для отображения ответа необходима регистрация </div>';
    }
    echo '</div>';
}   

?></div>
            </div>
         </div>
      </div>
   </div>
   <div id="wb_LayoutGrid2">
      <div id="LayoutGrid2">
         <div class="col-1">
            <div id="wb_Text1">
               <span style="color:#FFFFFF;font-family:Arial;font-size:19px;">+99365 56 82 39<br>+99312 94 50 64</span>
            </div>
         </div>
         <div class="col-2">
            <div id="wb_Text2">
               <span style="color:#FFFFFF;font-family:Arial;font-size:19px;">Туркменистан, Ашхабад<br>ул. Андалиб&nbsp; дом 182 (2 этаж) Бизнес-Центр &quot;Diamond international&quot;</span>
            </div>
         </div>
         <div class="col-3">
            <div id="wb_Text4">
               <span style="color:#FFFFFF;font-family:Arial;font-size:19px;">milli_audit@mail.ru <br>milli.audit2015@gmail.com<br>www.milli-audit.com</span>
            </div>
         </div>
      </div>
   </div>
   <div id="Layer1" style="position:absolute;text-align:center;visibility:hidden;left:970px;top:260px;width:250px;height:373px;z-index:37;">
      <div id="Layer1_Container" style="width:250px;position:relative;margin-left:auto;margin-right:auto;text-align:left;">
<!-- Sign Up -->
         <button onclick="ShowObject('Layer1', 0);return false;" class="close-layer">X</button>
         <form name="Sign_Up" method="post" accept-charset="UTF-8" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" enctype="multipart/form-data" id="Sign_Up">
            <div class="col">
               <div class="div-input" style="text-align:center;">
                  <h2>Регистрация</h2>
               </div>
               <input type="hidden" id="Status" name="Status" value="Гость" readonly spellcheck="false">
               <input type="hidden" id="Form" name="Form" value="Sign_Up_Form" readonly spellcheck="false">
               <input type="hidden" id="Datestamp" name="Datestamp" value="<?php echo date('Y-m-d H:i:s') ?>" readonly spellcheck="false">

               <div id="div-Fio" class="div-input">
                  <input type="text" name="Fio" placeholder="<?php if(!isset($Fio_Err) || empty($Fio_Err)){echo 'ФИО/Наименование компании';}else{echo $Fio_Err;}?>" value="">
               </div>

               <div id="div-Phone" class="div-input">
                  <input type="text" name="Phone" placeholder="<?php if(!isset($Phone_Err) || empty($Phone_Err)){echo 'Телефон';}else{echo $Phone_Err;}?>" value="">
               </div>

               <div id="div-Activity" class="div-input">
                  <select name="Activity" size="1" id="Activity" style="display:inline-block;">
                     <option value="" disabled selected>Вид деятельности</option>
                     <option value="Физ.лицо">Физ.лицо</option>
                     <option value="Частный предприниматель">Частный предприниматель</option>
                     <option value="Директор предприятия">Директор предприятия</option>
                     <option value="Бухгалтер">Бухгалтер</option>
                  </select>
               </div>

               <div id="div-Email" class="div-input">
                  <input type="text" name="Email" placeholder="<?php if(!isset($Email_Err) || empty($Email_Err)){echo 'E-mail';}else{echo $Email_Err;}?>" value="">
               </div>

               <div id="div-Confirm" class="div-input" style="text-align:center;">
                  <input id="Confirm" type="submit" name="Confirm" value="Отправить">
               </div>
            </div><!-- col-1 -->
         </form>
      </div>
   </div>
   <div id="Audit" style="position:absolute;text-align:left;left:980px;top:1010px;width:220px;height:113px;z-index:38;">
      <div id="wb_Image1" style="position:absolute;left:10px;top:10px;width:199px;height:93px;z-index:25;">
         <img src="images/audit.jpg" id="Image1" alt="" width="199" height="93"></div>
      <div id="wb_IconFont1" style="position:absolute;left:10px;top:10px;width:30px;height:30px;text-align:center;z-index:26;">
         <a href="#" onclick="ShowObject('Audtext', 1);return false;"><div id="IconFont1"><i class="fa fa-question"></i></div></a></div>
      <div id="wb_IconFont3" style="position:absolute;left:190px;top:0px;width:30px;height:30px;text-align:center;z-index:27;">
         <a href="#" onclick="ShowObject('Audit', 0);return false;"><div id="IconFont3"><i class="fa fa-angle-up"></i></div></a></div>
   </div>
   <div id="Audtext" style="position:absolute;text-align:left;visibility:hidden;left:1230px;top:510px;width:520px;height:520px;z-index:39;" onclick="ShowObject('Audtext', 0);return false;">
      <div id="wb_Text7" style="position:absolute;left:10px;top:10px;width:490px;height:502px;text-align:justify;z-index:28;">
         <span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Уважаемые Партнеры и Клиенты!</strong><br><br></span><span style="color:#000000;font-family:Arial;font-size:12px;">ХО «Милли Аудит», действующее на основании лицензии №1-17-29-59 от 15.07.2022г., предлагает Вам заключить договор на аудиторское сопровождение и проведение обязательного аудита за 2023 год. <br><br><em>Напоминаем Вам, что обязательному аудиту не подлежат ХО, АО общий доход (в т.ч от релизации ОС, НМА. списание КТЗ и пр. доход) которых не превышает 1 млн. манат</em><br><br>По результатам проверки Вы получаете:<br><br>Аудиторское заключение – официальный документ, подтверждающий достоверность бухгалтерской отчётности, для предоставления в налоговую, банки, инвесторам и другим заинтересованным лицам.<br><br>Письменную информацию (отчет) – документ для внутреннего служебного пользования с подробным описанием выявленных в ходе проверки нарушений в бухгалтерском и налоговом учете, налоговых рисков, </span><span style="color:#004A99;font-family:Arial;font-size:12px;"><strong>практических рекомендаций </strong></span><span style="color:#000000;font-family:Arial;font-size:12px;">по их устранению.<br><br>Консультационную поддержку –&nbsp; в ходе проверки, устные телефонные консультации на протяжении всего срока действия договора на аудит.<br><br>Аудит проводится на принципах </span><span style="color:#004A99;font-family:Arial;font-size:12px;"><strong>профессионализма, честности, объективности</strong></span><span style="color:#000000;font-family:Arial;font-size:12px;">, качества, ответственности, независимости и безусловной конфиденциальности.<br><br>В ходе аудита обеспечивается </span><span style="color:#004A99;font-family:Arial;font-size:12px;"><strong>максимальный учет Ваших пожеланий </strong></span><span style="color:#000000;font-family:Arial;font-size:12px;">и интересов, индивидуальных подход к решению поставленных задач в зависимости от целей и отраслевой специфики предприятия<br><br>Мы даем возможность </span><span style="color:#004A99;font-family:Arial;font-size:12px;"><strong>вносить исправления </strong></span><span style="color:#000000;font-family:Arial;font-size:12px;">в ходе проверки и после ее проведения.<br><br>Дата начала и окончания проверки согласовывается с Вами, что позволяет организовать работу в удобные для Вас сроки.<br><br>После оказания услуг </span><span style="color:#004A99;font-family:Arial;font-size:12px;"><strong>мы несем </strong></span><span style="color:#000000;font-family:Arial;font-size:12px;">ответственность за результаты нашей работы!</span></div>
      <div id="wb_IconFont2" style="position:absolute;left:480px;top:0px;width:30px;height:30px;text-align:center;z-index:29;">
         <a href="#" onclick="ShowObject('Audtext', 0);return false;" title="Закрыть"><div id="IconFont2"><i class="fa fa-window-close-o"></i></div></a></div>
   </div>
</body>
</html><?php mysqli_close($conn); ?>