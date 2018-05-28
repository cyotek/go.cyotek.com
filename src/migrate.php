<?php require_once __DIR__ . '/includes/functions.php'; ?>
<?php
use geertw\IpAnonymizer\IpAnonymizer;

function anonymise_table($tableName)
{
  $db = get_database_connection();
  $result = $db->query('SELECT [Id], [IpAddress] FROM [' . $tableName . '];');
  $total = 0;

  $sql = 'UPDATE [' . $tableName . '] '
                . 'SET [IpAddress] = :address '
                . 'WHERE [Id] = :id';

  $stmt = $db->prepare($sql);

  foreach($result as $row)
  {
    $id = $row['Id'];
    $address =  $row['IpAddress'];
    $newAddress =  IpAnonymizer::anonymizeIp($address);
    
    if($address != $newAddress)
    {
      $stmt->bindValue(':id', $id);
      $stmt->bindValue(':address', $newAddress);
      $stmt->execute();
      $total += $stmt->rowCount();
    }    
  }

  $db = null; 

  return $total;
}

if(!is_valid_apikey())
  {
    write_forbidden('Migrate Data', 'Invalid API key.');
  }
  else
  {
?>

<?php write_header_html('Migrate Data') ?>
<div class='wrapper wrapper-90'>
  <h1>Data Migration</h1>

<?php 

if(should_anonymise_ip_address())
{
  $total = anonymise_table('Activity');
  $total += anonymise_table('Error');

  echo '<p>' . $total . ' records updated.</p>';
}
else
{
  echo '<p>No migration performed.</p>';
}
 ?>

</div>
<?php write_footer_html() ?>

<?php
    }
?>
