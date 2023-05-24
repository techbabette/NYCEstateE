<!DOCTYPE html>
<html lang="en">
<?php include("./views/fixed/head.html")?>
<body>
    <!--Navbar-->
    <?php include("./views/fixed/nav.html")?>
      <!--Content-->
      
      <?php
      $page = null;
      if(isset($_GET["page"])){
        $page = $_GET["page"];
      }
      if(!$page){
        include("./views/pages/index.html");
      }
      else{
          $success = include("./views/pages/".$page);
          if(!$success){
            header("Location: index.php");
          }
      }
      ?>
      <?php include("./views/fixed/footer.html")?>
      <?php include("./views/fixed/endOfBodyScripts.html")?>
</body>
</html>