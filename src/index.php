<?php require_once __DIR__ . '/includes/functions.php'; ?>
<?php
  if(isset($_GET['id']))
  {
    $id = get_redirect_id();

    if($id != -1)
    {
      $url = get_redirect_uri($id);

      if(isset($url))
      {
        increment_hit_count($id, $url);

        $test = is_test_mode();
        write_redirect($url, !is_test_mode());
      }
      else
      {
        write_not_found();
      }
    }
    else
    {
      write_bad_request('Failed to redirect', 'URL ID not specified.');
    }  
  }
  else
  {
    $title = get_title();
    $url = get_home_page_url();
    echo(get_html_document($title, '<h1>' . $title . '</h1><p>Click <a href=\'' . $url . '\'>here</a> to visit the home page.</p>'));
  }
?>
