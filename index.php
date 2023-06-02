<?php
$page = null;
$fileToInclude = "./views/pages/loading.html";
$title = "Loading";
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