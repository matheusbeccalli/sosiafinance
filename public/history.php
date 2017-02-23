<?php

    // configuration
    require("../includes/config.php"); 

    // query database for user history
    $transactions = query("SELECT t1.asset_id, asset_name, transaction_date, asset_quantity, asset_price, custodian_name 
                   FROM dbinvestments.transaction t1 JOIN dbinvestments.custodian t2 ON t1.custodian_id = t2.custodian_id JOIN dbinvestments.asset t3 ON t1.asset_id = t3.asset_id
                   WHERE user_id = ?
                   ORDER BY transaction_date", $_SESSION["id"]);
                    
     // render user's history
     render("history.php", ["transactions" => $transactions, "title" => "History"]);
     
 ?>
