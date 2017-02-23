<?php

    // configuration
    require("../includes/config.php");

    $date = get_latest_pricing_date();

  // gets the different breakdowns of the portfolio
    $positions_by_asset_type = get_portfolio_by_asset_type($date);
    $positions_by_currency = get_portfolio_by_currency($date);

    // render charts
    render("charts.php", ["positions_by_asset_type" => $positions_by_asset_type,
                          "positions_by_currency" => $positions_by_currency]);

 ?>
