<?php
include "api.php";
session_start();
if (!in_array($_SESSION['Status'],['Администратор','Менеджер'])){header('Location: /online.php');}
date_default_timezone_set("Asia/Ashgabat");
$conn = connect_to_db();

$id = $_GET['id'];
$sql = "SELECT * FROM Onlineqest WHERE ID='$id'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);
$theme = $row['Tema']; $answer = $row['Otwet']; $rec = $row['Rekomend']; $question = $row['Wopros']; $link = $row['Webssylka']; $status = $row['Status'];   

//----------------------------------------------

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
   $theme=$status=$answer=$rec=$link='';
   $id = $_POST['ID'];
   $theme = mysqli_real_escape_string($conn, test_input($_POST['Tema']));
   $status = mysqli_real_escape_string($conn, test_input($_POST['Status']));
   $answer = mysqli_real_escape_string($conn, test_input($_POST['Otwet']));
   $rec = mysqli_real_escape_string($conn, test_input($_POST['Rekomend']));
   $link = mysqli_real_escape_string($conn, test_input($_POST['Webssylka']));
   $sql = "UPDATE `onlineqest` SET Tema = '$theme', Otwet = '$answer', Rekomend = '$rec', Webssylka = '$link', Status = '$status' WHERE ID='$id'";
   $result = mysqli_query($conn, $sql);
   mysqli_close($conn);
   if ($status == "Закрыто")
   {
      $Datetime = date("d.m.Y G:i:s");
      $Question = $_POST['Wopros'];
      $text = "$id|";
      $text .= "Дата: $Datetime|";
      $text .= "Тема: $theme|";
      $text .= "Вопрос: $Question|";
      $text .= "Ответ: http://milli-audit.com/online.php|";
      $text .= "С уважением, ХО Milli-Audit|";
      $text .= "+99365 56 82 39\n+99312 94 50 64|";
      $text .= "Туркменистан, Ашхабад ул. Андалиб  дом 182 (2 этаж) Бизнес-Центр 'Diamond international'|";
      $text .= "milli_audit@mail.ru\nmilli.audit2015@gmail.com\nwww.milli-audit.com|";
      $file = fopen("mailer/new_answer_".date('Y-m-d')."_".date('G-i-s')."_.txt","w") or die("Unable to open file!");
      fwrite($file, iconv("UTF-8", "WINDOWS-1251", $text));
   }
   header('Location: admin.php');
}
?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Редактирование вопроса</title>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap" rel="stylesheet">
<link href="css/Millaudit.css" rel="stylesheet">
<link href="css/updatequestion.css" rel="stylesheet">
</head>
<body>
   <div style="text-align:center; padding-top:15px; color:rgb(0,0,200);"><h1>№ <?php echo $id;?></h1></div>
   <form name="Update_form" method="post" accept-charset="UTF-8" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" enctype="multipart/form-data" id="Update_form" style="width:50%;margin:auto;">
      <input type="hidden" id="ID" name="ID" value="<?php echo $id; ?>">
      <div>
         <span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Тема</strong></span>
      </div>
      <select name="Tema" size="1" id="Tema" style="display:block;width:100%;height:28px;z-index:88;">
         <option value="<?php echo $theme; ?>" selected="selected"><?php echo $theme; ?></option>
         <option value="Бухучёт">Бухучёт</option>
         <option value="Ликвидация">Ликвидация</option>
         <option value="Налоги">Налоги</option>
         <option value="Зарплата">Зарплата</option>
         <option value="Документация">Документация</option>
         <option value="Лицензирование">Лицензирование</option>
         <option value="Оценка">Оценка</option>
         <option value="Другое">Другое</option>
      </select>
      <div>
         <span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Вопрос</strong></span>
      </div>
      <textarea type="text" cols="5" rows="4" id="Wopros" style="display:block;width:100%;height:120px;z-index:112;" name="Wopros" spellcheck="false"><?php echo $question; ?></textarea>

      <div>
         <span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Ответ</strong></span>
      </div>
      <textarea type="text" cols="5" rows="4" id="Otwet" style="display:block;width:100%;height:500px;z-index:112;" name="Otwet" spellcheck="false"><?php echo $answer; ?></textarea>

      <div>
         <span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Рекомендация</strong></span>
      </div>
      <textarea type="text" cols="5" rows="4" id="Rekomend" style="display:block;width:100%;height:500px;z-index:112;" name="Rekomend" spellcheck="false"><?php echo $rec; ?></textarea>

      <div>
         <span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Ссылка</strong></span>
      </div>
      <input type="text" id="Webssylka" style="display:block;width:100%;height:26px;z-index:116;" name="Webssylka" value="<?php echo $link; ?>" spellcheck="false">

      <div>
         <span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Статус</strong></span>
      </div>
      <select name="Status" size="1" id="Status" style="display:block;width:100%;height:28px;z-index:88;">
         <option selected="selected"><?php echo $status; ?></option>
         <option value="Открыто">Открыто</option>
         <option value="Закрыто">Закрыто</option>
      </select>
      <div style="text-align:center;">
         <input type="submit" id="Confirm" name="" value="Сохранить" style="display:inline-block;width:150px;height:29px;z-index:119;margin-top:15px">
         <a href="admin.php" id="Close">Вернуться</a>
      </div>
   </form>
</body>
</html>