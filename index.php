<?php
//Decide what file to include here, include later
$page = null;
// $fileToInclude = "./views/pages/loading.html";
// $title = "Loading";
if(isset($_GET["page"])){
  $page = $_GET["page"];
}
if(!$page){
  $title = "Index";
  $fileToInclude = "./views/pages/index.html";
}
else{
    $success = file_exists("./views/pages/".$page);
    if(!$success){
      $fileToInclude = ("./views/pages/"."index.html");
    }
    else{
      $fileToInclude = ("./views/pages/".$page);
    }
    $title = ucfirst(explode(".",$page)[0]);
}
?>
<!DOCTYPE html>
<html lang="en">

<?php 
      include("./views/fixed/head.php");
      echoHead($title);
?>

<body>

    <!--Navbar-->
    <?php include("./views/fixed/nav.html")?>
      <!--Content-->
      <div id="router-view">
        <?php
          include($fileToInclude);
        ?>
      </div>
      <div id="error-holder" class="d-flex flex-column"></div>
      
      <?php include("./views/fixed/footer.html")?>
      <?php include("./views/fixed/endOfBodyScripts.html")?>
</body>
</html>