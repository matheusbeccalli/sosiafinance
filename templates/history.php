<ul class="nav nav-pills">
    <li><a href="charts.php">Charts</a></li>
    <li><a href="portfolio.php">Portfolio</a></li>
  <li><a href="logout.php"><strong>Log Out</strong></a></li>
</ul>
<table class="table table-striped">
  <thead>
    <tr>
      <th>Name</th>
      <th>Operation</th>
      <th>Quantity</th>
      <th>Price</th>
      <th>TOTAL</th>
      <th>Date/Time</th>
    </tr>
  </thead>
  <tbody>

    <?php foreach($transactions as $transaction): ?>
      <tr>
        <td><?= $transaction["asset_name"] ?></td>
        <td><?php if($transaction["asset_quantity"] > 0): ?>Purchase<?php else: ?>Sale<?php endif ?></td>
        <td><?= number_format($transaction["asset_quantity"],2) ?></td>
        <td>$<?= number_format($transaction["asset_price"],2) ?></td>
        <td>$<?= number_format($transaction["asset_quantity"]*$transaction["asset_price"],2) ?></td>
        <td><?= $transaction["transaction_date"] ?></td>
      </tr>
    <?php endforeach ?>

  </tbody>
</table>
