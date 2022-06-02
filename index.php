<?php 
  session_start(); 

  if (!isset($_SESSION['success'])) {
  	header('location: login.php');
  }
  if (isset($_GET['logout'])) {
  	session_destroy();
  	unset($_SESSION['success']);
  	header("location: login.php");
  }
?>
<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

<div class="header">
	<h2>Home Page</h2>
</div>
<div class="content">
  	<?php if (isset($_SESSION['success'])) : ?>
	<!-- notification message -->
      <div class="error success" >
      	<h3> <?php echo $_SESSION['success']; ?> </h3>
      </div>
    	<!-- logged in user information -->
    	<p>Welcome <strong><?php echo $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?></strong></p>
      <?php 
		/* destroying session/global variables */
		unset($_SESSION['success']);
		unset($_SESSION['first_name']);
		unset($_SESSION['last_name']);
	?>
    	<p> <a href="index.php?logout='1'" style="color: red;">logout</a> </p>
    <?php endif ?>
</div>

</body>
</html>