<ul class="nav nav-pills">
  <li><a href="history.php">History</a></li>
  <li><a href="portfolio.php">Portfolio</a></li>
  <li><a href="logout.php"><strong>Log Out</strong></a></li>
</ul>

<!--Formats data to be plotted in charts-->
<?php
  foreach($positions_by_asset_type as $position):
    $pie_data_asset_type[] = array($position['asset_type'], $position['type_total']);
  endforeach;

  foreach($positions_by_currency as $position):
    $pie_data_currency[] = array($position['asset_currency'], $position['currency_total']);
  endforeach;
?>

  <!--Load the AJAX API-->
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">

   // Load the Visualization API and the corechart package.
   google.charts.load('current', {'packages':['corechart']});

   // Set a callback to run when the Google Visualization API is loaded.
   google.charts.setOnLoadCallback(drawChartByType);
   google.charts.setOnLoadCallback(drawChartByCurrency);

   // Callback that creates and populates a data table,
   // instantiates the pie chart, passes in the data and
   // draws it.
   function drawChartByType() {

    // Create the data table.
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Asset Type');
    data.addColumn('number', 'Value');
    data.addRows(<?php echo json_encode($pie_data_asset_type, JSON_NUMERIC_CHECK); ?>);

    // Set chart options
    var options = {'title':'By asset type',
            'width':400,
            'height':300};

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.PieChart(document.getElementById('chart_div_by_type'));
    chart.draw(data, options);
   }

   function drawChartByCurrency () {

    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Currency');
    data.addColumn('number', 'Value');
    data.addRows(<?php echo json_encode($pie_data_currency, JSON_NUMERIC_CHECK); ?>);

    var options = {'title':'By currency',
            'width':400,
            'height':300};

    var chart = new google.visualization.PieChart(document.getElementById('chart_div_by_currency'));
    chart.draw(data, options);
   }

  </script>

  <!--Divs that will hold the pie chart-->
  <table class="table">
   <tr>
    <td><div id="chart_div_by_type"></div></td>
    <td><div id="chart_div_by_currency"></div></td>
   </tr>
  </table>

