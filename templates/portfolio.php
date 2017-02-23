<ul class="nav nav-pills">
  <li><a href="charts.php">Charts</a></li>
  <li><a href="history.php">History</a></li>
  <li><a href="logout.php"><strong>Log Out</strong></a></li>
</ul>
<table class="table table-striped">
  <thead>
    <tr>
      <th>Name</th>
      <th>Asset Type</th>
      <th>Currency</th>
      <th>Quantity</th>
      <th>Price</th>
      <th>TOTAL</th>
      <th>IRR</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($positions as $position): ?>
      <?php if ($position["asset_quantity"] > 0): ?>
      <tr>
        <td><?= $position["asset_name"] ?></td>
        <td><?= $position["asset_type"] ?></td>
        <td><?= $position["asset_currency"] ?></td>
        <td><?= number_format($position["asset_quantity"],2) ?></td>
        <td>$<?= number_format($position["asset_price"],2) ?></td>
        <td>$<?= number_format($position["asset_quantity"]*$position["asset_price"],2) ?></td>
        <td><?= number_format($position["asset_irr"] * 100) ?>%</td>
      </tr>
      <?php endif ?>
    <?php endforeach ?>
      <tr>
        <td><strong>Total</strong></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><strong>$<?= number_format(calculate_portfolio_total($positions),2) ?></strong></td>
        <td><strong><?= number_format($portfolio_irr[0]["get_portfolio_irr()"] * 100) ?>%</strong></td>
      </tr>
  </tbody>
</table>
