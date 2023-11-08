<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__.'/mailer/Exception.php';
require __DIR__.'/mailer/PHPMailer.php';
require __DIR__.'/mailer/SMTP.php';
function ValidateEmail($email)
{
   $pattern = '/^([0-9a-z]([-.\w]*[0-9a-z])*@(([0-9a-z])+([-\w]*[0-9a-z])*\.)+[a-z]{2,6})$/i';
   return preg_match($pattern, $email);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['formid']) && $_POST['formid'] == 'layoutgrid4')
{
   $mailto = 'example1@gmail.com';
   $mailfrom = isset($_POST['email']) ? $_POST['email'] : $mailto;
   $mailcc = 'example2@mail.ru';
   $subject = 'Тарифы';
   $message = 'Запрос на подписку - сервис Milli-Audit';
   $success_url = './online.php';
   $error_url = './error.php';
   $eol = "\n";
   $error = '';
   $internalfields = array ("submit", "reset", "send", "filesize", "formid", "captcha", "recaptcha_challenge_field", "recaptcha_response_field", "g-recaptcha-response", "h-captcha-response");
   $mail = new PHPMailer(true);
   try
   {
      $mail->IsSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->Port = 587;
      $mail->SMTPAuth = true;
      $mail->Username = 'MAIL';
      $mail->Password = 'PASSWORD';
      $mail->SMTPSecure = 'tls';
      $mail->Subject = stripslashes($subject);
      $mail->From = $mailfrom;
      $mail->FromName = $mailfrom;
      $mailto_array = explode(",", $mailto);
      $mailcc_array = explode(",", $mailcc);
      for ($i = 0; $i < count($mailto_array); $i++)
      {
         if(trim($mailto_array[$i]) != "")
         {
            $mail->AddAddress($mailto_array[$i], "");
         }
      }
      for ($i = 0; $i < count($mailcc_array); $i++)
      {
         if(trim($mailcc_array[$i]) != "")
         {
            $mail->AddCC($mailcc_array[$i], "");
         }
      }
      if (!ValidateEmail($mailfrom))
      {
         $error .= "The specified email address (" . $mailfrom . ") is invalid!\n<br>";
         throw new Exception($error);
      }
      $mail->AddReplyTo($mailfrom);
      $message .= $eol;
      $message .= "IP Address : ";
      $message .= $_SERVER['REMOTE_ADDR'];
      $message .= $eol;
      foreach ($_POST as $key => $value)
      {
         if (!in_array(strtolower($key), $internalfields))
         {
            if (is_array($value))
            {
               $message .= ucwords(str_replace("_", " ", $key)) . " : " . implode(",", $value) . $eol;
            }
            else
            {
               $message .= ucwords(str_replace("_", " ", $key)) . " : " . $value . $eol;
            }
         }
      }
      $mail->CharSet = 'UTF-8';
      if (!empty($_FILES))
      {
         foreach ($_FILES as $key => $value)
         {
            if (is_array($_FILES[$key]['name']))
            {
               $count = count($_FILES[$key]['name']);
               for ($file = 0; $file < $count; $file++)
               {
                  if ($_FILES[$key]['error'][$file] == 0)
                  {
                     $mail->AddAttachment($_FILES[$key]['tmp_name'][$file], $_FILES[$key]['name'][$file]);
                  }
               }
            }
            else
            {
               if ($_FILES[$key]['error'] == 0)
               {
                  $mail->AddAttachment($_FILES[$key]['tmp_name'], $_FILES[$key]['name']);
               }
            }
         }
      }
      $mail->WordWrap = 80;
      $mail->Body = $message;
      $mail->Send();
      header('Location: '.$success_url);
   }
   catch (Exception $e)
   {
      $errorcode = file_get_contents($error_url);
      $replace = "##error##";
      $errorcode = str_replace($replace, $e->getMessage(), $errorcode);
      echo $errorcode;
   }
   exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Тарифы</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="malogo.jpg" rel="shortcut icon" type="image/x-icon">
<link href="css/font-awesome.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap" rel="stylesheet">
<link href="css/Millaudit.css" rel="stylesheet">
<link href="css/tarifs.css" rel="stylesheet">
<script src="jquery-3.6.0.min.js"></script>
<script src="popper.min.js"></script>
<script src="util.min.js"></script>
<script src="collapse.min.js"></script>
<script src="dropdown.min.js"></script>
<script src="skrollr.min.js"></script>
<script src="tarifs.js"></script>
</head>
<body>
   <div id="wb_header" style="overflow:hidden">
      <div id="header">
         <div class="col-1">
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
                              <a href="./online.php" class="nav-link"><i class="fa fa-home"></i>Обратно</a>
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
               <label for="" id="Label1" style="display:block;width:100%;line-height:24px;z-index:1;">Стоимость подписки</label>
            </div>
         </div>
      </div>
   </div>
   <div id="wb_LayoutGrid4">
      <form name="tarifs" method="post" action="<?php echo basename(__FILE__); ?>" enctype="multipart/form-data" id="LayoutGrid4">
         <input type="hidden" name="formid" value="layoutgrid4">
         <div class="col-1">
         </div>
         <div class="col-2">
            <div id="wb_LayoutGrid6">
               <div id="LayoutGrid6">
                  <div class="row">
                     <div class="col-1">
                        <label for="RadioButton1" id="Label2" style="display:block;width:100%;line-height:22px;z-index:2;">1 месяц - 100 тмт</label>
                     </div>
                     <div class="col-2">
                        <div id="wb_RadioButton1" style="display:inline-block;width:20px;height:20px;z-index:3;">
                           <input type="radio" id="RadioButton1" name="NewGroup" value="on" style="display:inline-block;"><label for="RadioButton1"></label>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div id="wb_LayoutGrid7">
               <div id="LayoutGrid7">
                  <div class="row">
                     <div class="col-1">
                        <label for="RadioButton2" id="Label3" style="display:block;width:100%;line-height:22px;z-index:4;">2 месяца - 95 тмт</label>
                     </div>
                     <div class="col-2">
                        <div id="wb_RadioButton2" style="display:inline-block;width:20px;height:20px;z-index:5;">
                           <input type="radio" id="RadioButton2" name="NewGroup" value="on" style="display:inline-block;"><label for="RadioButton2"></label>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div id="wb_LayoutGrid8">
               <div id="LayoutGrid8">
                  <div class="row">
                     <div class="col-1">
                        <label for="RadioButton3" id="Label4" style="display:block;width:100%;line-height:22px;z-index:6;">3 месяца - 90 тмт</label>
                     </div>
                     <div class="col-2">
                        <div id="wb_RadioButton3" style="display:inline-block;width:20px;height:20px;z-index:7;">
                           <input type="radio" id="RadioButton3" name="NewGroup" value="on" style="display:inline-block;"><label for="RadioButton3"></label>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div id="wb_LayoutGrid9">
               <div id="LayoutGrid9">
                  <div class="row">
                     <div class="col-1">
                        <label for="RadioButton4" id="Label5" style="display:block;width:100%;line-height:22px;z-index:8;">4 месяца - 85 тмт</label>
                     </div>
                     <div class="col-2">
                        <div id="wb_RadioButton4" style="display:inline-block;width:20px;height:20px;z-index:9;">
                           <input type="radio" id="RadioButton4" name="NewGroup" value="on" style="display:inline-block;"><label for="RadioButton4"></label>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div id="wb_LayoutGrid3">
               <div id="LayoutGrid3">
                  <div class="row">
                     <div class="col-1">
                        <label for="RadioButton5" id="Label8" style="display:block;width:100%;line-height:22px;z-index:10;">5 месяцев - 80 тмт</label>
                     </div>
                     <div class="col-2">
                        <div id="wb_RadioButton5" style="display:inline-block;width:20px;height:20px;z-index:11;">
                           <input type="radio" id="RadioButton5" name="NewGroup" value="on" style="display:inline-block;"><label for="RadioButton5"></label>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div id="wb_LayoutGrid10">
               <div id="LayoutGrid10">
                  <div class="row">
                     <div class="col-1">
                        <label for="RadioButton6" id="Label9" style="display:block;width:100%;line-height:22px;z-index:12;">6 месяцев - 75 тмт</label>
                     </div>
                     <div class="col-2">
                        <div id="wb_RadioButton6" style="display:inline-block;width:20px;height:20px;z-index:13;">
                           <input type="radio" id="RadioButton6" name="NewGroup" value="on" style="display:inline-block;"><label for="RadioButton6"></label>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-3">
            <label for="" id="Label6" style="display:block;width:100%;line-height:19px;z-index:20;">Итого:</label>
            <input type="text" id="Editbox1" style="display:block;width:100%;height:26px;z-index:21;" name="Сумма" value="" readonly spellcheck="false">
            <label for="" id="Label7" style="display:block;width:100%;line-height:19px;z-index:22;">тмт</label>
            <input type="text" id="Editbox2" style="display:block;width:100%;height:26px;z-index:23;" name="Телефон" value="" spellcheck="false" placeholder="Телефон">
            <input type="text" id="Editbox3" style="display:block;width:100%;height:26px;z-index:24;" name="Имя" value="" spellcheck="false" placeholder="Имя ">
            <input type="email" id="Editbox4" style="display:block;width:100%;height:26px;z-index:25;" name="Имя" value="" spellcheck="false" placeholder="Email">
            <label for="" id="Label10" style="display:block;width:100%;line-height:16px;z-index:26;">Условия оплаты</label>
            <select name="Usloviya" size="1" id="Combobox1" style="display:block;width:100%;height:28px;z-index:27;">
               <option selected value="Наличными">Наличными</option>
               <option value="На телефон">На телефон</option>
               <option value="Перечислением">Перечислением</option>
            </select>
            <input type="submit" id="Button1" name="" value="Отправить" style="display:inline-block;width:131px;height:42px;z-index:28;">
         </div>
         <div class="col-4">
         </div>
      </form>
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
</body>
</html>