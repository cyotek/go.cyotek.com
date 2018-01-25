<?php require_once __DIR__ . '/includes/functions.php'; ?>
<?php
  if(!is_valid_apikey())
  {
    write_bad_request('Errors', 'Invalid API key.');
  }
  else
  {
    $db = get_database_connection();
    $result = $db->query('SELECT [Url], [Timestamp], [IpAddress], [StatusCode] FROM [Error] ORDER BY [Timestamp] DESC LIMIT 1000');
?>

<?php write_header_html('Errors') ?>
<div class="wrapper wrapper-90">
  <h1>Errors</h1>
  <table>
    <thead>
      <tr>
        <th>Status Code</th>
        <th>Url</th>
        <th>Timestamp</th>
        <th>Client</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($result as $row) { ?>
      <tr>
        <td><?php echo($row['StatusCode']) ?></td>
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
