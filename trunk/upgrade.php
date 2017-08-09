<?php
require_once('includes/connect.php');
mysql_select_db($database, $databaseConnect);
// delete products table
$sql = "DROP TABLE IF EXISTS affiliSt_products1";
mysql_query($sql, $databaseConnect) or die(mysql_error());
// create products table
$sql = "CREATE TABLE affiliSt_products1 (
        prodID INT(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
        merchant VARCHAR(150),
        merchantProdID VARCHAR(255),
        prodCategory VARCHAR(255),
        prodName VARCHAR(255),
        prodBrand VARCHAR(255),
        prodDescription TEXT,
        prodPromoText TEXT,
        prodLink TEXT,
        prodImageURL TEXT,
        prodImageSmall TEXT,
        prodPrice DECIMAL(10,2),
        prodCurrency VARCHAR(20),
        prodPopularity INT(10) NOT NULL default 0,
        prodDB INT(10) NOT NULL default 0,
        extraFieldA TEXT,
        extraFieldB TEXT,
        extraFieldC TEXT,
        extraFieldD TEXT,
        extraFieldE TEXT,
        dbProdID INT(10),
        PRIMARY KEY (prodID),
		FULLTEXT (prodName,prodDescription,prodBrand,prodCategory)
       )";
mysql_query($sql, $databaseConnect) or die(mysql_error());
echo "upgrade done";
?>