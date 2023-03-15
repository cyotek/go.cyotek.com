<?php require_once __DIR__ . '/includes/functions.php'; ?>
<?php
  if(!is_valid_apikey())
  {
    write_forbidden('Redirects Summary', 'Invalid API key.');
  }
  else
  {
    $title='';
    $slug = '';
    $url = '';

    if(isset($_POST['submit']))
    {
      $title = $_POST['title'];
      $slug = $_POST['slug'];
      $url = $_POST['url'];

      if(!is_null_or_empty($title) && !is_null_or_empty($slug) && !is_null_or_empty($url))
      {
        $id = $_POST['id'];

        if(is_numeric($id))
        {
          update_redirect($id, $_POST['enabled'] === 'on' ? 1 : 0, $title, $slug, $url);

          $title='';
          $slug = '';
          $url = '';
        }
        else
        {
          add_redirect($title, $slug, $url);
        }
      }
    }

    $db = get_database_connection();
    $result = $db->query('SELECT [Id], [Enabled], [Title], [Slug], [Url], [LastHit], [HitCount] FROM [UrlSummary] ORDER BY [Title] COLLATE NOCASE ASC');
?>

<?php write_header_html('Redirects Summary') ?>
<div class="wrapper wrapper-90">
  <h1>Redirects Summary</h1>

  <div class="table">
    <div>
      <div>ID</div>
      <div>Enabled?</div>
      <div>Title</div>
      <div>Slug</div>
      <div>Url</div>
      <div>Hits</div>
      <div>Last Hit</div>
      <div>&nbsp;</div>
    </div>
    <form action="list.php?apikey=<?php echo get_api_key() ?>"
      method="post">
      <div>&nbsp;</div>
      <div>&nbsp;</div>
      <div>
        <input id="title" name="title" type="text" maxlength="255"
          value="<?php echo $title ?>" class="title-editor" />
      </div>
      <div>
        <input id="slug" name="slug" type="text" maxlength="255"
          value="<?php echo $slug ?>" class="slug-editor" />
      </div>
      <div>
        <input id="url" name="url" type="text" maxlength="255"
          value="<?php echo $url ?>" class="url-editor" />
      </div>
      <div>&nbsp;</div>
      <div>&nbsp;</div>
      <div>
        <button name="submit" type="submit">Add</button>
      </div>
    </form>
    <?php foreach($result as $row) { ?>
    <form action="list.php?apikey=<?php echo get_api_key() ?>"
      method="post">
      <div class="right"><input id="id" name="id" type="hidden"
          value="<?php echo($row['Id']) ?>" /><?php echo($row['Id']) ?>
      </div>
      <div><input id="enabled" name="enabled" type="checkbox"
          <?php echo($row['Enabled'] ? 'checked' : '') ?> /></div>
      <div>
        <input id="title" name="title" type="text" maxlength="255"
          value="<?php echo($row['Title']) ?>" class="title-editor" />
      </div>
      <div>
        <input id="slug" name="slug" type="text" maxlength="255"
          value="<?php echo($row['Slug']) ?>" class="slug-editor" />
      </div>
      <div>
        <input id="url" name="url" type="text" maxlength="255"
          value="<?php echo $row['Url'] ?>" class="url-editor" />
        <a href="<?php echo $row['Url'] ?>" title="Visit <?php echo $row['Url'] ?>" target="_blank">&#11179;</a>
      </div>
      <div class="right"><?php echo($row['HitCount']) ?></div>
      <div>
        <?php echo($row['LastHit'] != null ? date('Y-m-d H:i', $row['LastHit']) : 'Never') ?>
      </div>
      <div><button name="submit" type="submit">Update</button></div>
    </form>
    <?php } ?>
  </div>
</div>
<?php write_footer_html() ?>

<?php
   $db = null;
    }
?>
