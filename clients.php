<?php
include "api.php";
session_start();
if (!in_array($_SESSION['Status'],['Администратор','Менеджер'])){header('Location: /online.php');}
$conn = connect_to_db();
list($count_questions, $count_all, $count_users, $count_guests, $count_managers, $count_administrators) = filters_count_cli($conn);

if (isset($_GET['udel']))
{
    $id = $_GET['udel'];
    $sql = "DELETE FROM klienty WHERE ID='$id'";
    mysqli_query($conn, $sql);
    $id = '';
    header('Location: clients.php');
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Клиенты</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="ICO.png" rel="icon" type="image/png">
<link href="css/font-awesome.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap" rel="stylesheet">
<link href="css/Millaudit.css" rel="stylesheet">
<link href="css/clients.css" rel="stylesheet">
<script src="jquery-3.6.0.min.js"></script>
<script src="popper.min.js"></script>
<script src="util.min.js"></script>
<script src="collapse.min.js"></script>
<script src="dropdown.min.js"></script>
<script src="skrollr.min.js"></script>
<script src="clients.js"></script>
</head>
<body>
   <div id="wb_header" style="overflow:hidden">
      <div id="header">
         <div class="col-1">
            <a href="?showall=1">Всего (<?php echo $count_all;?>)</a>
            <a href="?showusers=1">Пользователи (<?php echo $count_users;?>)</a>
            <a href="?showmanagers=1">Менеджеры (<?php echo $count_managers;?>)</a>
            <a href="?showadmins=1">Администраторы (<?php echo $count_administrators;?>)</a>
            <a href="?showguests=1">Гости (<?php echo $count_guests;?>)</a>
         </div>
         <div class="col-2">
            <nav id="wb_headerMenu" style="display:inline-block;width:100%;z-index:1;" data--800-top="padding-top:4px;padding-bottom:4px;" data-top="padding-top:16px;padding-bottom:16px;">
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
                              <a href="./admin.php" class="nav-link">Вопросы (<?php echo $count_questions;?>)</a>
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
   <div id="wb_LayoutGrid5">
      <div id="LayoutGrid5">
         <div class="row">
            <div class="col-1">
<!-- Список клиентов и пользователей системы -->
<?php
$sql = get_filter_cli();
$result = mysqli_query($conn,$sql);
$date_today = date('Y-m-d');
// Создание названия столбцов (как угодно)
echo '
<table class="sqltable" border="1" style="border-color:black;border-collapse:collapse;">
    <tr>
        <th>ID</th>
        <th>Дата регистрации</th>
        <th>ФИО</th>
        <th>Телефон</th>
        <th>Вид деятельности</th>
        <th>Э-почта</th>
        <th>Логин</th>
        <th>Пароль</th>
        <th>Статус пользователя</th>
        <th>Дата окончания</th>
        <th>Токен</th>
        <th colspan="2">Действия</th>
    </tr>';

while($row = mysqli_fetch_assoc($result))
{
    $overdue = "";
    if ($row['End_date'] <= $date_today && $row['Status'] == 'Пользователь'){$overdue = "style='color: red;'>";}
    // подключение строк из таблицы ( $row['здесь название нужного столбца из базы'] )
    echo "
    <tr>
        <td style='text-align:center;'>" . $row['ID'] . "</td>
        <td>" . $row['Datestamp'] . "</td>
        <td>" . $row['Fio'] . "</td>
        <td>" . $row['Phone'] . "</td>
        <td>" . $row['Activity'] . "</td>
        <td>" . $row['Email'] . "</td>
        <td>" . $row['Login'] . "</td>
        <td>" . $row['Password'] . "</td>
        <td>" . $row['Status'] . "</td>
        <td ". $overdue .">" . $row['End_date'] . "</td>
        <td>" . $row['Token'] . "</td>
        <td style='background: green;'> <a href='/updateuser.php?id=".$row['ID']."'>Редактировать</a> </td>
        <td style='background: red;'> <a href='?udel=".$row['ID']."'>Удалить</a> </td>
    </tr>";
    
}
echo "</table>";
?>

            </div>
         </div>
      </div>
   </div>
</body>
</html><?php mysqli_close($conn); ?>