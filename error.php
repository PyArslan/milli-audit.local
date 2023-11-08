<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Ошибка</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap" rel="stylesheet">
<link href="css/Millaudit.css" rel="stylesheet">
<link href="css/error.css" rel="stylesheet">
</head>
<body>
   <div id="wb_LayoutGrid1">
      <div id="LayoutGrid1">
         <div class="row">
            <div class="col-1">
               <div id="wb_Heading1" style="display:inline-block;width:100%;z-index:0;">
                  <h1 id="Heading1">Ошибка!</h1>
               </div>
               <div id="Html1" style="display:inline-block;width:527px;height:145px;z-index:1">
<?php
if (isset($_GET)){
   echo "<ul style='font-size:20px;color:red;margin-right:20px;'>";
   foreach($_GET as $value)
   {
      if(!empty($value)){echo "<li>".$value."</li>";}
   }
   echo "</ul>";
}
?></div>
            </div>
         </div>
      </div>
   </div>
   <div id="wb_LayoutGrid2">
      <div id="LayoutGrid2">
         <div class="row">
            <div class="col-1">
               <a id="Button1" href="./online.php" style="display:inline-block;width:149px;height:42px;z-index:2;">Вернутся</a>
            </div>
         </div>
      </div>
   </div>
</body>
</html>