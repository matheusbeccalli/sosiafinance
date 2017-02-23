<?php

  // configuration
  require("../includes/config.php");

  $date = get_latest_pricing_date();

  // retrieve user's portfolio from database
  $positions = get_portfolio($date);
  $portfolio_irr = get_portfolio_irr();

  // render user's portfolio
  render("portfolio.php", ["positions" => $positions, "title" => "Portfolio", "portfolio_irr" => $portfolio_irr]);

 ?>
