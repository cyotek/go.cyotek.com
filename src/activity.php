<?php require_once __DIR__ . '/includes/functions.php'; ?>
<?php
  if(!is_valid_apikey())
  {
    write_bad_request('Activity', 'Invalid API key.');
  }
  else
  {
    $db = create_database();
    $result = $db->query('SELECT [Title], [Url], [Timestamp], [IpAddress] FROM [ActivitySummary] ORDER BY [Timestamp] DESC LIMIT 1000');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
  <title>Activity</title>
  <link rel="stylesheet" type="text/css" href="assets/styles.css" />
</head>
<body>
<div class="wrapper wrapper-90">
  <h1>Activity</h1>
  <table>
    <thead>
      <tr>
        <th>Title</th>
        <th>Url</th>
        <th>Timestamp</th>
        <th>Client</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($result as $row) { ?>
      <tr>
        <td><?php echo($row['Title']) ?></td>
        <td><?php echo($row['Url']) ?></td>
        <td><?php echo(date('Y-m-d H:i:s', $row['Timestamp'])) ?></td>
        <td><?php echo($row['IpAddress']) ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
  </div>
</body>
</html>

<?php
   $db = null;
    }
?>
