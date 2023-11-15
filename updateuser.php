<?php
include "api.php";
session_start();
if ($_SESSION['Status'] != 'Администратор'){header('Location: /online.php');}
date_default_timezone_set("Asia/Ashgabat");
$conn = connect_to_db();
$id = $_GET['id'];
$sql = "SELECT * FROM `Table` WHERE ID='$id'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);
$Fio = $row['Fio']; $Phone = $row['Phone']; $Activity = $row['Activity']; $Email = $row['Email']; $Login = $row['Login']; $Password = $row['Password']; $Status = $row['Status']; $End_date = $row['End_date'];  

//----------------------------------------------

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $Fio = $Phone = $Activity = $Email = $Login = $Password = $Status = '';
    // Validation 1
    $id = $_POST['ID'];
    // Fioname
    if (empty($_POST['Fio'])) {$Fio_Err = 'Укажите ФИО или Название компании';} 
    else {$Fio = test_input($_POST['Fio']);}
    // Phone
    if (empty($_POST['Phone'])) {$Phone_Err = 'Укажите телефон';} 
    else {$Phone = test_input($_POST['Phone']);}
    // Activity
    if (empty($_POST['Activity'])) {$Activity_Err = 'Укажите значение';} 
    else {$Activity = test_input($_POST['Activity']);}
    // Email
    if (empty($_POST['Email'])) {$Email_Err = 'Укажите Email';} 
    else {$Email = test_input($_POST['Email']);}
    // Login
    if (empty($_POST['Login'])) {$Login_Err = 'Придумайте логин';} 
    elseif (!preg_match("/^[a-zA-Z-'0-9_ ]*$/",$_POST['Login'])) {$Login_Err = 'Только латинские буквы, цифры, апострофы и пробелы';}
    else {$Login = test_input($_POST['Login']);}

    $Password = test_input($_POST['Password']);
    $Status = test_input($_POST['Status']);
    $End_date = $_POST['End_date'];
            
    $var_array = [$Fio,$Phone,$Activity,$Email,$Login,$Status];
    // Validation 2
    $Error = '';
    foreach ($var_array as $i)
    {
        if (empty($i) and $i!='0')
        {
            $Error = 'Error! '.$i;
            break;
        }
    }
    if (empty($Error))
    {
        $Fio = mysqli_real_escape_string($conn, $Fio);
        $Phone = mysqli_real_escape_string($conn, $Phone);
        $Activity = mysqli_real_escape_string($conn, $Activity);
        $Email = mysqli_real_escape_string($conn, $Email);
        $Login = mysqli_real_escape_string($conn, $Login);
        $Status = mysqli_real_escape_string($conn, $Status);
        if ($Password){
            $Password = md5($Password);
            $sql = "UPDATE `Table` SET Fio = '$Fio', Phone = '$Phone', Activity = '$Activity', Email = '$Email', Status = '$Status', Login = '$Login', Password = '$Password', End_date = '$End_date' WHERE ID='$id'";
        } else {$sql = "UPDATE `Table` SET Fio = '$Fio', Phone = '$Phone', Activity = '$Activity', Email = '$Email', Status = '$Status', Login = '$Login', End_date = '$End_date' WHERE ID='$id'";}

        $result = mysqli_query($conn, $sql);
        mysqli_close($conn);
        header('Location: clients.php');
    }//empty(Error)
}
?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Update</title>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap" rel="stylesheet">
<link href="css/Millaudit.css" rel="stylesheet">
<link href="css/updateuser.css" rel="stylesheet">
</head>
<body>
<?php //foreach($_POST as $val){echo $val.'<br>';} echo $Error.'<br>'; ?>
   <form name="Sign_Up" method="post" accept-charset="UTF-8" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" enctype="multipart/form-data" id="Sign_Up" style="width:50%;margin:auto;margin-top:2%;">
               
      <div class="col">
         <input type="hidden" id="ID" name="ID" value="<?php echo $id; ?>" readonly> 
                  
         <div class="div-input" style="text-align:center;">
            <h1>ID <?php echo $id; ?></h1>
         </div>

         <div id="div-Fio" class="div-input">
            <span>ФИО/Наименование компании</span>
            <input type="text" name="Fio" id="Fio" placeholder="<?php if(isset($Fio_Err)){echo $Fio_Err;}?>" value="<?php echo $Fio;?>">
         </div>

                        
         <div id="div-Status" class="div-input">
            <span>Статус</span>
            <select name="Status" size="1" id="Status" style="display:inline-block;">
               <option value="<?php echo $Status;?>" selected><?php echo $Status;?></option>
               <option value="Гость">Гость</option>
               <option value="Пользователь">Пользователь</option>
               <option value="Менеджер">Менеджер</option>
               <option value="Администратор">Администратор</option>
            </select>
         </div>

         <div id="div-Phone" class="div-input">
            <span>Телефон</span>
            <input type="text" name="Phone" id="Phone" placeholder="<?php if(isset($Phone_Err)){echo $Phone_Err;}?>" value="<?php echo $Phone;?>">
         </div>

         <div id="div-Activity" class="div-input">
            <span>Вид деятельности</span>
            <select name="Activity" size="1" id="Activity" style="display:inline-block;">
               <option value="<?php echo $Activity;?>" selected><?php echo $Activity;?></option>
               <option value="Физ.лицо">Физ.лицо</option>
               <option value="Частный предприниматель">Частный предприниматель</option>
               <option value="Директор предприятия">Директор предприятия</option>
               <option value="Бухгалтер">Бухгалтер</option>
            </select>
         </div>

         <div id="div-Email" class="div-input">
            <span>E-mail</span>
            <input type="text" name="Email" id="Email" placeholder="<?php if(isset($Email_Err)){echo $Email_Err;}?>" value="<?php echo $Email;?>">
         </div>
                        
         <div id="div-Login" class="div-input">
            <span>Логин</span>
            <input type="text" name="Login" id="Login" placeholder="<?php if(isset($Login_Err)){echo $Login_Err;}?>" value="<?php echo $Login;?>">
         </div>
         <div id="div-Password" class="div-input">
            <span>Пароль</span>
            <input type="password" name="Password" id="Password" placeholder="<?php if(isset($Password_Err)){echo $Password_Err;}?>" minlength="4">
         </div>
         <div id="div-EndDate" class="div-input">
            <span>Дата окончания</span>
            <input type="date" name="End_date" id="End_date" value="<?php echo $End_date;?>">
         </div>

         <div id="div-Confirm" class="div-input" style="text-align:center;">
            <input id="Confirm" type="submit" name="Confirm" value="Отправить">
            <a href="clients.php" id="Close">Вернуться</a>
         </div>
      </div><!-- col-1 -->
   </form>
</body>
</html>
