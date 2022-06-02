<?php

/* NOTE: Only used once to create physical database schema */

// Connection req. fields
$servername = "localhost";
$username = "root";
$password = "";
$db_name ="eimvs_db";

// Create connection
$mysqli = new mysqli($servername, $username, $password, $db_name);

// Check connection
if ($mysqli->connect_errno) {
	die("Connection failed: " . mysqli_connect_error());
}
else {
	echo "Connected Successfully!\n";
}

// Run SQL DDL Commands
$sql ="
-- Normal Entity Tables
CREATE TABLE seller (
	cnic BIGINT(13),
	first_name VARCHAR(25) NOT NULL,
	last_name VARCHAR(25) NOT NULL,
	email VARCHAR(50),
	cell_no BIGINT(11) NOT NULL,
	CONSTRAINT sel_pk PRIMARY KEY(cnic)
);

CREATE TABLE customer (
	cust_id INT AUTO_INCREMENT,
	email VARCHAR(50) NOT NULL,
	first_name VARCHAR(25) NOT NULL,
	last_name VARCHAR(25) NOT NULL,
	password VARCHAR(100) NOT NULL,
	CONSTRAINT cust_pk PRIMARY KEY(cust_id),
	CONSTRAINT cust_eml_un UNIQUE(email)
);

CREATE TABLE product (
	prod_id INT AUTO_INCREMENT,
	model_no VARCHAR(30) NOT NULL,
	category VARCHAR(30) NOT NULL,
	company_name VARCHAR(30) NOT NULL,
	price INT NOT NULL,
	CONSTRAINT prod_pk PRIMARY KEY(prod_id),
	CONSTRAINT prod_mdl_ctgry_un UNIQUE(model_no, category)
);

CREATE TABLE shop (
	shop_id INT AUTO_INCREMENT,
	shop_no VARCHAR(5) NOT NULL,
	floor VARCHAR(2) NOT NULL,
	name VARCHAR(30) NOT NULL,
	seller_id BIGINT(13) NOT NULL,
	CONSTRAINT sh_pk PRIMARY KEY(shop_id),
	CONSTRAINT sel_fk FOREIGN KEY(seller_id) REFERENCES seller(cnic) ON DELETE CASCADE,
	CONSTRAINT sh_sn_flr_un UNIQUE(shop_no, floor)
);

-- Conjunction Entity Tables
CREATE TABLE views (
	cust_id INT,
	shop_id INT,
	prod_id INT,
	CONSTRAINT vw_pk PRIMARY KEY(cust_id, shop_id, prod_id),
	CONSTRAINT vw_cust_fk FOREIGN KEY(cust_id) REFERENCES CUSTOMER(cust_id) ON DELETE CASCADE,
	CONSTRAINT vw_sh_fk FOREIGN KEY(shop_id) REFERENCES SHOP(shop_id) ON DELETE CASCADE,
	CONSTRAINT vw_prod_fk FOREIGN KEY(prod_id) REFERENCES PRODUCT(prod_id) ON DELETE CASCADE
);

CREATE TABLE contains (
	shop_id INT,
	prod_id INT,
	quantity INT NOT NULL,
	CONSTRAINT cn_pk PRIMARY KEY(shop_id, prod_id),
	CONSTRAINT cn_sh_fk FOREIGN KEY(shop_id) REFERENCES SHOP(shop_id) ON DELETE CASCADE,
	CONSTRAINT cn_prod_fk FOREIGN KEY(prod_id) REFERENCES PRODUCT(prod_id) ON DELETE CASCADE
);
";

/* use it for dropping all tables in proper sequence */
//$sql = "DROP TABLE views, contains; DROP TABLE shop; DROP TABLE customer, seller, product;";

// Check query
if ($mysqli->multi_query($sql)) {
	echo "Tables created successfully";
}
else {
	echo "Error!: " . $mysqli->error;
}

$mysqli->close();
?>