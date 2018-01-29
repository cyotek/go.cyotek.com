<?php require_once __DIR__ . '/includes/functions.php'; ?>
<?php
  if(!is_valid_apikey())
  {
    write_forbidden('Activity', 'Invalid API key.');
  }
  else
  {
    $db = get_database_connection();
    $result = $db->query('SELECT [Title], [Url], [Timestamp], [IpAddress] FROM [ActivitySummary] ORDER BY [Timestamp] DESC LIMIT 1000');
?>

<?php write_header_html('Activity') ?>
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
<?php write_footer_html() ?>

<?php
   $db = null;
    }
?>
