<?php
    include "api.php";
    session_start();
    if (!in_array($_SESSION['Status'],['Администратор','Менеджер','Пользователь'])){header('Location: /online.php');}
    $conn = connect_to_db();
    $id = $_GET['id'];
    $sql = "SELECT * FROM onlineqest WHERE `ID`='$id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    
    $Theme = $row['Tema']; $Question = $row['Wopros']; $Answer = $row['Otwet']; $Recomendation = $row['Rekomend']; $Link = $row['Webssylka']; $Datetime = DateTime::createFromFormat('Y-m-d H:i:s.u', $row['Datetime'])->format('d.m.Y H:i:s');
function encode64($file){
      $extension = explode(".", $file);
      $extension = end($extension);
      $binary = fread(fopen($file, "r"), filesize($file));
      return '<img src="data:image/'.$extension.';base64,'.base64_encode($binary).'" width="300" height="200"/>';
   }
    
?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Печать</title>
<link href="ICO.png" rel="icon" type="image/png">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap" rel="stylesheet">
<link href="css/Millaudit.css" rel="stylesheet">
<link href="css/for-print.css" rel="stylesheet">
<script src="for-print.js"></script>
</head>
<body>
   <div style="width:80%; margin:auto; padding:8px; font-size:14px; font-family:-webkit-body;">
      <div>
         <div style="display:inline-block; text-align:left; width:40%;"><img src="ICO.png" width="100" height="60"></div>
         <div style="display:inline-block; text-align:right; width:59%;"><span>ХО &#171;Милли-Аудит&#187;<br><?php echo $Datetime; ?><br>№ лицензии 1-17-29-59 от 15 июля 2022 г.<sup>*</sup><br>Вопрос № <?php echo $id; ?><br>http://milli-audit.com/online.php</span></div>
      </div>
      <div style="text-align:center; width:100%;"><h1 style="font-family:-webkit-body; font-size:21px;"><?php echo $Theme; ?></h1></div>
        
      <p style="text-align:justify;"><b>На Ваш вопрос:</b> <?php echo $Question; ?></p>
      <p style="text-align:justify;"><b>Даём следующий ответ:</b> <?php echo $Answer; ?></p>
      <p><b>Рекомендация: </b><?php echo $Recomendation; ?></p>
      <p><b>Ссылка на законодательные акты:</b> <a><?php echo $Link; ?></a></p>
      <p>С уважением, <br>ХО Милли-Аудит</p>
      <?php echo encode64("images/Печать_и_подпись.jpg");?>
      
      
      <p style="font-style:italic; text-align:center; color:grey; font-size:13px;"><sup>*</sup>Данный документ обладает всей юридической силой как официальное заключение на Ваш вопрос!</p>  
      <div style="text-align:center;">
         <div style="display:inline-block; width:30%; vertical-align:top; text-align:center; padding:5px;"><p>+99365 56 82 39<br>+99312 94 50 64</p></div>
         <div style="display:inline-block; width:30%; vertical-align:top; text-align:center; padding:5px;"><p>Туркменистан, Ашхабад ул. Андалиб  дом 182 (2 этаж) Бизнес-Центр "Diamond international"</p></div>
         <div style="display:inline-block; width:30%; vertical-align:top; text-align:center; padding:5px;"><p>milli_audit@mail.ru<br>milli.audit2015@gmail.com<br>www.milli-audit.com</p></div>
      </div>
   </div>
   </body>
</html>