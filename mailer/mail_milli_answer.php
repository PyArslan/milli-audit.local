<?php

/* настройка cron. Нужно вести такую команду в панели управления cron на хостинге:

/usr/local/bin/wget -q http://oblakotm.com/mailer/mail.php

wget - браузер, с его помощью можно автономно открывать php страницы запуская их код 

Расшифровка команды:

/usr/local/bin/путь_до_программы_исполнителя -метод(-q это тихий) http(s)://домен/путь_до_исполяемого_файла

Указать время в cron:
* - каждый, например день: * означает запуск каждый день
*(слеш без ковычек)n - например минуты: означает запуск каждые n минут

*/

// Настройка параметров скрипта: Максимальное время выполнения и Текущие время и дата
ini_set('max_execution_time', '3600');
date_default_timezone_set('Asia/Ashgabat');

// Подключение нужных для скрипта файлов которые лежат с ним в одной папке
use PHPMailer\PHPMailer\PHPMailer;
require __DIR__.'/PHPMailer.php';
require __DIR__.'/SMTP.php';


$check = fopen("check.txt","r") or die(0);

// если в проверочном  файле значение OFF то начинается след. проверка
if(fread($check, filesize("check.txt")) == "OFF"){
	fclose($check);

	$files_array = array();

	// сканирование директории и поиск файлов с заявкой для рассылки
	$dir = scandir(getcwd());
	foreach ($dir as $file) {
		if(strpos($file, "ew_answer")){
			array_push($files_array, $file);
			break;
		}
	}


	// если массив в файлами не пустой то начинается выполнение рассылки
	if (!empty($files_array)){
		
		$check = fopen("check.txt","w");
		fwrite($check, "ON");
		fclose($check);
		
		$date_today = date('d.m.Y G:i:s');

		// Запись в лог файл сведения о начале рассылки
		$log = fopen("log_answers.txt","a");
		fwrite($log, iconv("UTF-8", "WINDOWS-1251", "\n\n---------".$date_today."--------\n\nНачало отправки рассылки"));
		fclose($log);

	    // ----------- SQL -------------- \\
		
	    $servername = "IP";
	    $username = "user";
	    $password = "password";
	    $dbname = "db";

	    $emails_array = array();

	    $conn = new mysqli($servername, $username, $password, $dbname);

	    if ($conn->connect_error) {
	    	$log = fopen("log_answers.txt","a");
			fwrite($log, iconv("UTF-8", "WINDOWS-1251", "\n\nConnection failed: ".$conn->connect_error));
			fclose($log);
	      	die("Connection failed: " . $conn->connect_error);
	    }

	    $sql = "SELECT Email FROM klienty ORDER BY ID";

	    $result = $conn->query($sql);
	    if ($result->num_rows > 0) {
	     
	        while($row = $result->fetch_assoc()) {
	            array_push($emails_array, $row['Email']);
	        }
	    }
		

	    // ------------- Sending ------------- \\
	    
	    foreach ($files_array as $file_name){
	    	// чтение файла с заявкой
	        $file = fopen($file_name, "r") or die("Unable to open file!");
	        $file = fread($file,filesize($file_name));

	        // преобразование строки в массив через символ | -> A|B|C = ['A','B','C']
	        $file = explode("|", $file);

	        // проходится по email
	        foreach ($emails_array as $email){

	            $mailfrom = 'MAIL';

	            $subject = "Получен ответ на вопрос № ".$file[0];

	            // Формируется текст письма
	            $message = "";
	            foreach ($file as $k => $value) {
					if($k < 1) continue;
	                $message .= mb_convert_encoding($value, "UTF-8", "WINDOWS-1251")."\n\n";
	            }
	            
	            // Настройка PHPMailer и SMTP
	            $mail = new PHPMailer();
	            $mail->IsSMTP();
	            $mail->CharSet = "utf-8";
	            $mail->Host = 'SERVER';
	            $mail->Port = 'PORT';
	            $mail->SMTPAuth = true;
	            $mail->Username = 'MAIL';
	            $mail->Password = 'PASSWORD';
	            $mail->SMTPSecure = 'tsl';

	            $mail->From = $mailfrom;
	            $mail->FromName = $mailfrom;
	            $mail->AddAddress($newemail, "");

	            // Указывается Email здесь, на данный момент берёт значение $email из массива со всеми E-mail'ами
	            $mail->AddAddress(strtolower(trim($email)), "");

	            $mail->AddReplyTo($mailfrom);

	            $mail->Body = stripslashes($message);
	            $mail->Subject = stripslashes($subject);
	            $mail->WordWrap = 80;

	            if (!$mail->Send())
	            {
	            	// Запись в лог файл сведения об ошибке
	            	$log = fopen("log_answers.txt","a");
					fwrite($log, iconv("UTF-8", "WINDOWS-1251", "\n\nОшибка при отправке $email,  $message,\n\n".$mail->ErrorInfo));
					fclose($log);
	                //die('PHPMailer error: ' . $mail->ErrorInfo);
					continue;
	            }
	            
	        }// for each emails
	 

	        unlink($file_name);

	    }// for each files

	    $check = fopen("check.txt","w");
		fwrite($check, "OFF");
		fclose($check);

		// Запись в лог файл сведения об успехе
		$log = fopen("log_answers.txt","a");
		fwrite($log, iconv("UTF-8", "WINDOWS-1251", "\n\nРассылка успешно завершена"));
		fclose($log);
	}
	
} else {exit();}

?>
