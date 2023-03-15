<?php require_once __DIR__ . '/includes/functions.php'; ?>
<?php
  if(!is_valid_apikey())
  {
    write_forbidden('Redirects Summary', 'Invalid API key.');
  }
  else
  {
    if(isset($_POST['submit']))
    {
      $title = $_POST['title'];
      $slug = $_POST['slug'];
      $url = $_POST['url'];

      if(!is_null_or_empty($title) && !is_null_or_empty($slug) && !is_null_or_empty($url))
      {
        add_redirect($title, $slug, $url);
      }
    }
    else
    {
      $title='';
      $slug = '';
      $url = '';
    }

    $db = get_database_connection();
    $result = $db->query('SELECT [Id], [Enabled], [Title], [Slug], [Url], [LastHit], [HitCount] FROM [UrlSummary] ORDER BY [Title] COLLATE NOCASE ASC');
?>

<?php write_header_html('Redirects Summary') ?>
<div class="wrapper wrapper-90">
  <h1>Redirects Summary</h1>

  <form action="list.php?apikey=<?php echo get_api_key() ?>" method="post">
    <label for="title">Title:</label>
    <input id="title" name="title" type="text" maxlength="255" value='<?php echo $title ?>' />
    <label for="slug">Slug:</label>
    <input id="slug" name="slug" type="text" maxlength="255" value='<?php echo $slug ?>' />
    <label for="url">URL:</label>
    <input id="url" name="url" type="text" maxlength="255" value='<?php echo $url ?>' />
    <button name="submit" type="submit">Add</button>
  </form>

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
<?php write_footer_html() ?>

<?php
   $db = null;
    }
?>
