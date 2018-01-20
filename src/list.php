<?php require_once __DIR__ . '/includes/functions.php'; ?>
<?php
  if(!is_valid_apikey())
  {
    write_bad_request('Redirects Summary', 'Invalid API key.');
  }
  else
  {
    $db = create_database();
    $result = $db->query('SELECT [Id], [Enabled], [Title], [Slug], [Url], [LastHit], [HitCount] FROM [UrlSummary] ORDER BY [Title]');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
  <title>Redirects Summary</title>
  <link rel="stylesheet" type="text/css" href="assets/styles.css" />
</head>
<body>
<div class="wrapper wrapper-90">
  <h1>Redirects Summary</h1>
  <table>
    <thead>
      <tr>
        <th>Enabled?</th>
        <th>Title</th>
        <th>ID</th>
        <th>Slug</th>
        <th>Url</th>
        <th>Hits</th>
        <th>Last Hit</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($result as $row) { ?>
      <tr>
        <td><?php echo($row['Enabled'] ? 'Yes' : 'No') ?></td>
        <td><?php echo($row['Title']) ?></td>
        <td class="right"><?php echo($row['Id']) ?></td>
        <td><?php echo($row['Slug']) ?></td>
        <td><?php echo($row['Url']) ?></td>
        <td class="right"><?php echo($row['HitCount']) ?></td>
        <td><?php echo($row['LastHit'] != null ? date('Y-m-d H:i', $row['LastHit']) : 'Never') ?></td>
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
