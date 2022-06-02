<?php
// for using session variables
session_start();

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'eimvs_db');/* servername, username, password, database_name */

//check connection
if (!$db) {
  die("Connection failed: " . mysqli_connect_error());
}

// initializing global variables
$errors = array();
$sellers = array();
$shops = array();
$categories = array();

//fetching data globally to keep some req. variables consistent
$temp_query = mysqli_query($db, "SELECT cnic FROM seller");// collecting seller's cnics
while($row = $temp_query->fetch_assoc()){
	array_push($sellers, $row);
}

$temp_query = mysqli_query($db, "SELECT shop_id, shop_no, floor FROM shop");// collecting shop objs
while($row = $temp_query->fetch_assoc()){
	array_push($shops, $row);
}

$temp_query = mysqli_query($db, "SELECT category FROM product GROUP BY category");// finding product categories
while($row = $temp_query->fetch_assoc()){
	array_push($categories, $row);
}

// SELLER
if (isset($_POST['mng_seller'])) {
  // receive all input values from the form
  $cnic = $_POST['sel_cnic'];
  $first_name = mysqli_real_escape_string($db, $_POST['sel_first_name']);
  $last_name = mysqli_real_escape_string($db, $_POST['sel_last_name']);
  $email = mysqli_real_escape_string($db, $_POST['sel_email']);
  $cell_no = $_POST['sel_cell_no'];
  
  /* form validation: ensure that the form is correctly filled ...
     by adding (array_push()) corresponding error into $errors array */
  if (empty($cnic)) { array_push($errors, "Cnic is required"); }
  if (empty($first_name)) { array_push($errors, "First Name is required"); }
  if (empty($last_name)) { array_push($errors, "Last Name is required"); }
  if (empty($cell_no)) { array_push($errors, "Cell_no is required"); }
  
  // first check the database to make sure 
  // a seller does not already exist with the same cnic
  $seller_check_query = "SELECT * FROM seller WHERE cnic='$cnic' LIMIT 1";
  $result = mysqli_query($db, $seller_check_query);
  $seller = mysqli_fetch_assoc($result);
  
  if ($seller) { // if seller exists
      array_push($errors, "cnic already exists");
  }

  // Finally, insert seller if there are no errors in the form
  if (count($errors) == 0) {
  	$query = "INSERT INTO seller (cnic, first_name, last_name, email, cell_no) 
  			  VALUES('$cnic', '$first_name', '$last_name', '$email', '$cell_no')";
  	mysqli_query($db, $query);
	array_push($sellers, $cnic);
	$_SESSION['mng_success'] = "Seller Successfully Inserted!";
  	header('location: manage.php');
  }
}

// PRODUCT
if (isset($_POST['mng_product'])) {   
  // receive all input values from the form
  $category = mysqli_real_escape_string($db, $_POST['prod_category']);
  $company_name = mysqli_real_escape_string($db, $_POST['prod_company_name']);
  $model_no = mysqli_real_escape_string($db, $_POST['prod_model_no']);
  $price = $_POST['prod_price'];
  
  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error into $errors array
  if (empty($category)) { array_push($errors, "Category is required"); }
  if (empty($company_name)) { array_push($errors, "Company Name is required"); }
  if (empty($model_no)) { array_push($errors, "Model No is required"); }
  if (empty($price)) { array_push($errors, "Price is required"); }
  
  // first check the database to make sure 
  // a product does not already exist with the same cnic
  $product_check_query = "SELECT * FROM product WHERE category='$category' AND model_no='$model_no' LIMIT 1";
  $result = mysqli_query($db, $product_check_query);
  $product = mysqli_fetch_assoc($result);
  
  if ($product) { // if product exists
      array_push($errors, "product with same model no. already exists in mentioned category");
  }

  // Finally, insert seller if there are no errors in the form
  if (count($errors) == 0) {
  	$query = "INSERT INTO product (model_no, category, company_name, price) 
  			  VALUES('$model_no', '$category', '$company_name', '$price')";
  	mysqli_query($db, $query);
  	$_SESSION['mng_success'] = "Product Successfully Inserted!";
  	header('location: manage.php');
  }
}

// SHOP
if (isset($_POST['mng_shop'])) {
  // receive all input values from the form
  $name = mysqli_real_escape_string($db, $_POST['sh_name']);
  $shop_no = mysqli_real_escape_string($db, $_POST['sh_no']);
  $floor = mysqli_real_escape_string($db, $_POST['sh_floor_no']);
  $seller_id = $_POST['sh_ref_seller'];
  
  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error into $errors array
  if (empty($name)) { array_push($errors, "Name is required"); }
  if (empty($shop_no)) { array_push($errors, "Shop No. is required"); }
  if (empty($floor)) { array_push($errors, "Floor No. is required"); }
  if (empty($seller_id)) { array_push($errors, "Seller not selected"); }
  
  // first check the database to make sure 
  // a shop does not already exist with the same address (i.e. shop_no & floor)
  $shop_check_query = "SELECT * FROM shop WHERE shop_no='$shop_no' AND floor='$floor' LIMIT 1";
  $result = mysqli_query($db, $shop_check_query);
  $shop = mysqli_fetch_assoc($result);
  
  if ($shop) { // if shop exists
      array_push($errors, "shop with same address already exists");
  }

  // Finally, insert shop if there are no errors in the form
  if (count($errors) == 0) {
  	$query = "INSERT INTO shop (shop_no, floor, name, seller_id) 
  			  VALUES('$shop_no', '$floor', '$name', '$seller_id')";
  	mysqli_query($db, $query);
  	$_SESSION['mng_success'] = "Shop Successfully Inserted!";
  	header('location: manage.php');
  }
}

// CONTAINS Product Category
if (isset($_POST['mng_cn_prod_cat'])) {
	if (empty($_POST['cn_prod_cat'])) { 
		array_push($errors, "Product Category not selected");
		unset($_POST['mng_cn_prod_cat']);
	}
}

// CONTAINS
if (isset($_POST['mng_contains'])) {  
  // receive all input values from the form
  $shop_id = $_POST['cn_ref_shop'];
  $prod_id = $_POST['cn_ref_product'];  
  $quantity = $_POST['cn_quantity'];
  
  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error into $errors array
  if (empty($shop_id)) { array_push($errors, "Shop not selected"); }
  if (empty($prod_id)) { array_push($errors, "Product not selected"); }
  if (empty($quantity)) { array_push($errors, "Quantity is required"); }
  
  // first check the database to make sure 
  // a shop does not already exist with the same address (i.e. shop_no & floor)
  $shop_check_query = "SELECT * FROM contains WHERE shop_id='$shop_id' AND prod_id='$prod_id' LIMIT 1";
  $result = mysqli_query($db, $shop_check_query);
  $shop = mysqli_fetch_assoc($result);
  
  if ($shop) { // if shop exists
      array_push($errors, "product in mentioned shop already exists");
  }

  // Finally, insert shop if there are no errors in the form
  if (count($errors) == 0) {
  	$query = "INSERT INTO contains (shop_id, prod_id, quantity) 
  			  VALUES('$shop_id', '$prod_id', '$quantity')";
  	mysqli_query($db, $query);
  	$_SESSION['mng_success'] = "Entry in Contains Table Successfully Inserted!";
  	header('location: manage.php');
  }
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Data Manager</title>
	<link rel="stylesheet" type="text/css" href="../css/style.css">
	<link rel="stylesheet" type="text/css" href="../css/accordion.css">
</head>
<body>

<div class="header">
	<h2>Data Manager</h2>
</div>
  <h2>Select Options: </h2>
  <button class="accordion"><b>1. Insert Entry in Seller Table</b></button>
  <div class="panel">
    <form method="post" action="manage.php">
  	<?php
	if (isset($_POST['mng_seller'])){
		include('../errors.php');
	}
	?>
  	<div class="input-group">
  		<label>Cnic</label>
  		<input type="number" name="sel_cnic">
  		<label>First Name</label>
  		<input type="text" name="sel_first_name">
		<label>Last Name</label>
  		<input type="text" name="sel_last_name">
		<label>Email</label>
  		<input type="email" name="sel_email">
		<label>Cell No</label>
  		<input type="number" name="sel_cell_no">
		<br><br>
		<button type="submit" class="btn" name="mng_seller">submit</button>
  	</div>
    </form>
  </div>
  
  <button class="accordion"><b>2. Insert Entry in Product Table</b></button>
  <div class="panel">
    <form method="post" action="manage.php">
  	<?php
	if (isset($_POST['mng_product'])){
		include('../errors.php');
	}
	?>
  	<div class="input-group">
  		<label>Category</label>
  		<input type="text" name="prod_category">
		<label>Company Name</label>
  		<input type="text" name="prod_company_name">
		<label>Model No</label>
  		<input type="text" name="prod_model_no">
		<label>Price</label>
  		<input type="number" name="prod_price">
		<br><br>
		<button type="submit" class="btn" name="mng_product">submit</button>
  	</div>
    </form>
  </div>
  
  <button class="accordion"><b>3. Insert Entry in Shop Table</b></button>
  <div class="panel">
    <form method="post" action="manage.php">
  	<?php
	if (isset($_POST['mng_shop'])){
		include('../errors.php');
	}
	?>
  	<div class="input-group">
  		<label>Shop Name</label>
  		<input type="text" name="sh_name">
		<label>Shop No</label>
  		<input type="text" name="sh_no">
		<label>Floor No (G/1/2/...)</label>
  		<input type="text" name="sh_floor_no" value="G">
		<label>Seller's Cnic:</label>
		<select name="sh_ref_seller">
			<option value=''>--- select cnic ---</option>
			<?php 
    				foreach($sellers as $sel){
					echo '<option>' . $sel['cnic'] . '</option>';
				}
			?>
		</select>
		<br><br>
		<button type="submit" class="btn" name="mng_shop">submit</button>
  	</div>
    </form>
  </div>

  <button class="accordion"><b>4. Insert Entry in Contains Table</b></button>
  <div class="panel">
    <form method="post" action="manage.php">
	<?php
		if ( !(isset($_POST['mng_seller']) || isset($_POST['mng_product']) || isset($_POST['mng_shop'])) ){
			include('../errors.php');
		}
	?>
	<div class="input-group">
		<label>Product Category:</label>
		<select name="cn_prod_cat">
			<option value=''>-- select category --</option>
			<?php 
    				foreach($categories as $cat){
					echo '<option>' . $cat['category'] . '</option>';
				}
			?>
		</select>
		<button type="submit" class="btn" name="mng_cn_prod_cat">submit</button>
	</div>
    </form>
    
    <?php  if (isset($_POST['mng_cn_prod_cat'])) : ?>
    <form method="post" action="manage.php">
  	<div class="input-group">
		<label><?php echo mysqli_real_escape_string($db, $_POST['cn_prod_cat']); ?>'s Model No:</label>
		<select name="cn_ref_product">
			<option value=''>-- select model no --</option>
			<?php 
				$products = array();
				$temp_query = mysqli_query($db, "SELECT prod_id, company_name, model_no FROM product WHERE category = '" .
									  mysqli_real_escape_string($db, $_POST['cn_prod_cat']) . "'");// collecting filtered product objs
				while($row = $temp_query->fetch_assoc()){
					array_push($products, $row);
				}
				
				foreach($products as $pr){
					echo '<option value =' . $pr['prod_id'] . '>' . $pr['company_name'] . ' - ' . $pr['model_no'] . '</option>';
				}
			?>
		</select>
		<br><br>
		<label>Shop's Address:</label>
		<select name="cn_ref_shop">
			<option value=''>--- select address ---</option>
			<?php 
				foreach($shops as $sh){
					echo '<option value =' . $sh['shop_id'] . '>' . $sh['shop_no'] . ' - ' . $sh['floor'] . '</option>';
				}
			?>
		</select>
		<br><br>
		<label>Product Quantity</label>
  		<input type="number" name="cn_quantity">
		<br><br>
		<button type="submit" class="btn" name="mng_contains">submit</button>
  	</div>
    </form>
    <?php  endif ?>
  </div>
  <?php if (isset($_SESSION['mng_success'])) : ?>
  	<!-- notification message -->
  	<div class="success" >
  	   <h3> <?php echo $_SESSION['mng_success']; ?> </h3>
  	</div>
  <?php endif ?>

  <script src="../scripts/accordion.js"></script>
</body>
</html>