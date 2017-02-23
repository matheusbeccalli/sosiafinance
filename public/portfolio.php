<?php

  // configuration
  require("../includes/config.php");

  $date = get_latest_pricing_date();

  // retrieve user's portfolio from database
  $positions = get_portfolio($date);

  // render user's portfolio
  render("portfolio.php", ["positions" => $positions, "title" => "Portfolio"]);

 ?>
