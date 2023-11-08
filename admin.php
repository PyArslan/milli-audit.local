<?php
include "api.php";
session_start();
if (!in_array($_SESSION['Status'],['Администратор','Менеджер'])){header('Location: /online.php');}
$conn = connect_to_db();
// Проверка токена
check_token($conn);
// Взятие количества вопросов по фильтрам
list($count_Acc, $count_Liq, $count_Tax, $count_Sal, $count_Doc, $count_Lic, $count_Eval, $count_Else, $count_all, $count_open, $count_users) = filters_count($conn);
// ------------------------------------------------------------ \\
if (isset($_GET['qdel']))
{
    $id = $_GET['qdel'];
    $sql = "DELETE FROM onlineqest WHERE ID='$id'";
    mysqli_query($conn, $sql);
    $id = '';
    header('Location: admin.php');
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="malogo.jpg" rel="shortcut icon" type="image/x-icon">
<link href="css/font-awesome.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap" rel="stylesheet">
<link href="css/Millaudit.css" rel="stylesheet">
<link href="css/admin.css" rel="stylesheet">
<script src="jquery-3.6.0.min.js"></script>
<script src="popper.min.js"></script>
<script src="util.min.js"></script>
<script src="collapse.min.js"></script>
<script src="dropdown.min.js"></script>
<script src="skrollr.min.js"></script>
<script src="admin.js"></script>
</head>
<body>
   <div id="wb_header" style="overflow:hidden">
      <div id="header">
         <div class="col-1">
         </div>
         <div class="col-2">
         </div>
         <div class="col-3">
            <nav id="wb_headerMenu" style="display:inline-block;width:100%;z-index:0;" data--800-top="padding-top:4px;padding-bottom:4px;" data-top="padding-top:16px;padding-bottom:16px;">
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
                              <a href="./online.php" class="nav-link">Обратно</a>
                           </li>
                           <li class="nav-item">
                              <a href="./clients.php" class="nav-link">Клиенты (<?php echo $count_users;?>)</a>
                           </li>
                           <li class="nav-item">
                              <a href="./logs.php" class="nav-link">Логи</a>
                           </li>
                        </ul>
                     </div>
                  </div>
               </div>
            </nav>
         </div>
      </div>
   </div>
   <div id="wb_LayoutGrid4">
      <div id="LayoutGrid4">
         <div class="row">
            <div class="col-1">
               <a id="Acc" href="?showaccouting" style="display:inline-block;width:109px;height:35px;z-index:1;margin-right:5px;">Бухчёт (<?php echo $count_Acc; ?>)</a>
               <a id="Liq" href="?showliquidation" style="display:inline-block;width:109px;height:35px;z-index:2;margin-right:5px;">Ликвидация (<?php echo $count_Liq; ?>)</a>
               <a id="Tax" href="?showtaxes" style="display:inline-block;width:109px;height:35px;z-index:3;margin-right:5px;">Налоги (<?php echo $count_Tax; ?>)</a>
               <a id="Sal" href="?showsalary" style="display:inline-block;width:109px;height:35px;z-index:4;margin-right:5px;">Зарплата (<?php echo $count_Sal; ?>)</a>
               <a id="Doc" href="?showdocumentation" style="display:inline-block;width:109px;height:35px;z-index:5;margin-right:5px;">Документы (<?php echo $count_Doc; ?>)</a>
               <a id="Lic" href="?showlicense" style="display:inline-block;width:130px;height:35px;z-index:6;margin-right:5px;">Лицензирование (<?php echo $count_Lic; ?>)</a>
               <a id="Eval" href="?showeval" style="display:inline-block;width:109px;height:35px;z-index:7;margin-right:5px;">Оценка (<?php echo $count_Eval; ?>)</a>
               <a id="Else" href="?showelse" style="display:inline-block;width:109px;height:35px;z-index:8;margin-right:5px;">Другое (<?php echo $count_Else; ?>)</a>
               <a id="All" href="?showall" style="display:inline-block;width:109px;height:35px;z-index:9;margin-right:5px;">Все (<?php echo $count_all; ?>)</a>
               <a id="Open" href="?showopen" style="display:inline-block;width:109px;height:35px;z-index:10;">Открытые (<?php echo $count_open; ?>)</a>
            </div>
         </div>
      </div>
   </div>
   <div id="wb_LayoutGrid1">
      <div id="LayoutGrid1">
         <div class="row">
            <div class="col-1">
<!-- Вопросы и Ответы! -->
<?php

$sql = get_filter();
// После SELECT указываем нужные столбцы и после FROM Таблицу
$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_array($result))
{
        // подключение строк из таблицы ( $row['здесь название нужной строки из базы'] )
        echo '<div style="background:AliceBlue;border: solid DarkBlue; border-radius: 10px; max-width: 80%; margin:auto; margin-bottom: 15px;">
            <div style="font-size: 14px;text-align:left;margin-left:10px;padding-top:10px;color:grey;">'.DateTime::createFromFormat('Y-m-d H:i:s.u', $row['Datetime'])->format('d.m.Y H:i:s').'</div>
            <div style="font-size: 17px;padding-top:10px;padding-bottom:10px;text-align:left;margin-left:10px;"><b>№ '.$row['ID'].'</b></div>
            
            <div style="font-size:17px; text-align:left; padding-bottom:10px; margin-left:10px;"><b>Тема:</b> '.$row['Tema'].'</div>
            <hr style="background:DarkBlue;height:2px;">
            
            <div style="font-size:17px; text-align:left; padding-bottom:10px; padding-top:10px; margin-left:10px;"><b>Вопрос:</b> '.$row['Wopros'].'</div>      
            <hr style="background:DarkBlue;height:2px;">
            
            <div style="font-size:17px; text-align:left; padding-bottom:10px; padding-top:10px; margin-left:10px;"><b>Ответ:</b> '.$row['Otwet'].'</div>     
            <hr style="background:DarkBlue;height:2px;">
            
            <div style="font-size:17px; text-align:left; padding-bottom:10px; padding-top:10px; margin-left:10px;"><b>Рекомендация:</b> '.$row['Rekomend'].'</div>
            <hr style="background:DarkBlue;height:2px;">
            
            <div style="font-size:17px; text-align:left; padding-bottom:10px; padding-top:10px; margin-left:10px;"><b>Ссылка на законодательные акты:</b> '.$row['Webssylka'].'</div>
            <hr style="background:DarkBlue;height:2px;">
            <div style="font-size:17px; text-align:center; padding-bottom:20px; padding-top:10px; margin-left:10px;"><button class="edit-ques"><a href="updatequestion.php?id='.$row['ID'].'">Редактировать</a></button> <button class="del-ques"><a href="?qdel='.$row['ID'].'">Удалить</a></button></div>
        </div>';
}
?>
            </div>
         </div>
      </div>
   </div>
</body>
</html><?php mysqli_close($conn); ?>