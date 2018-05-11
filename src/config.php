<?php require_once __DIR__ . '/includes/functions.php'; ?>
<?php
  if(!is_valid_apikey())
  {
    write_forbidden('Configuration', 'Invalid API key.');
  }
  else
  {
    $db = get_database_connection();
    $result = $db->query('SELECT [Name], [Value] FROM [Config] ORDER BY [Name];');
?>

<?php write_header_html('Configuration') ?>
<div class="wrapper wrapper-90">
  <h1>Configuration</h1>
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Value</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($result as $row) { ?>
      <tr>
        <td><?php echo(htmlentities($row['Name'])) ?></td>
        <td><?php echo(htmlentities($row['Value'])) ?></td>
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
