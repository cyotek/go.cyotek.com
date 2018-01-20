<?php require_once __DIR__ . '/includes/functions.php'; ?>
<?php

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
?>
