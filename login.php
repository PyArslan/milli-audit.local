<?php
include "api.php";
// Функция для генерация токена
function gen_token() {
	$token = sprintf(
		'%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		mt_rand(0, 0xffff),
		mt_rand(0, 0xffff),
		mt_rand(0, 0xffff),
		mt_rand(0, 0x0fff) | 0x4000,
		mt_rand(0, 0x3fff) | 0x8000,
		mt_rand(0, 0xffff),
		mt_rand(0, 0xffff),
		mt_rand(0, 0xffff)
	);
 
	return $token;
}
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $Login_Err=$Password_Err='';
    $Login = test_input($_POST['Login']);
    $Password = md5($_POST['Password']);
    $conn = connect_to_db();
    $sql = "SELECT Login, Password, Status, Email FROM klienty WHERE Login = '$Login'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0)
    {
        $row = mysqli_fetch_array($result);
        if ($Password == $row['Password'])
        {
            $new_token = gen_token();
            session_start();
            $_SESSION["Username"] = $row['Login'];
            $_SESSION["Status"] = $row['Status'];
            $_SESSION["Email"] = $row['Email'];
            $_SESSION['Likes'] = array();
            $_SESSION['Dislikes'] = array();
            $_SESSION["Token"] = $new_token;
            $sql = "UPDATE klienty SET Token = '$new_token' WHERE Login = '$Login'";
            mysqli_query($conn, $sql);
            header('Location: /online.php');
        } else {$Password_Err = 'Пароль неверный!';}
    } else {$Login_Err = 'Пользователь не найден';}

    mysqli_close($conn);
}//If server
?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Login</title>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap" rel="stylesheet">
<link href="css/Millaudit.css" rel="stylesheet">
<link href="css/login.css" rel="stylesheet">
</head>
<body>
   <form name="Login_Form" method="post" accept-charset="UTF-8" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" enctype="multipart/form-data" id="Login_Form">
      <div class="col">
         <div class="div-input" style="text-align:center;">
            <h1>Войти</h1>
         </div>

         <div id="div-Login" class="div-input">
            <input type="text" name="Login" id="Login" placeholder="<?php if(!isset($Login_Err) || empty($Login_Err)){echo 'Логин';}else{echo $Login_Err;}?>">
         </div>
			
         <div id="div-Password" class="div-input">
            <input type="password" name="Password" id="Password" placeholder="<?php if(!isset($Password_Err) || empty($Password_Err)){echo 'Пароль';}else{echo $Password_Err;}?>">
         </div>

         <div id="div-Confirm" class="div-input" style="text-align:center;">
            <input id="Confirm" type="submit" name="Confirm" value="Войти">
            <a href="online.php" id="Close">Вернуться</a>
         </div>
      </div><!-- col-1 -->
   </form>
</body>
</html>