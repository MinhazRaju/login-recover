<?php include "inc/header.php"; ?>
<?php include "inc/navigation.php"; ?>


  
<div class="container">


  <div class="jumbotron">
    <h1 class="text-center">



    <?php if(logged_in()){

    	echo "Looged in";
    }else if (!isset($_SESSION['username'])){

    	redirect("logout.php");
    } 

    ?>

    </h1>
  </div>

  
</div> <!--Container-->

<?php include "inc/footer.php"; ?>




  
