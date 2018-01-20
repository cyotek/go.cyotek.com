<?php
function get_redirect_id()
{
  if(isset($_GET['id']))
  {
    $id = $_GET['id'];
  }
  //else if(isset($_SERVER['QUERY_STRING']))
  //{
  //  $id = $_SERVER['QUERY_STRING'];
  //}
  else
  {
    $id = -1;
  }

  if(!is_numeric($id))
  {
    $id = get_id_from_slug($id);
  }

  return $id;
}

function is_test_mode()
{
  if(isset($_GET['test']))
  {
    $value = (int)$_GET['test'];
    
    $result = $value != 0;
  }
  else
  {
    $result = false;
  }

  return $result;
}

function write_bad_request($title, $reason)
{
  $html = get_html_document($title, $reason);

  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: text/html');
  header('Content-Length: ' . strlen($html));

  echo $html;
}

function write_not_found()
{
  $html = get_html_document('Not found', 'The requested resource cannot be found.');

  header('HTTP/1.1 404 Not Found');
  header('Content-Type: text/html');
  header('Content-Length: ' . strlen($html));

  echo $html;
}

function write_redirect($url, $includeLocationHeader)
{
  $html = get_html_document('Found', '<p>The requested resource can be found <a href="' . $url . '">here</a>.</p>');

  header('HTTP/1.1 302 Found');
  if($includeLocationHeader)
  {
    header('Location: ' . $url);
  }
  header('Content-Type: text/html');
  header('Content-Length: ' . strlen($html));

  echo $html;
}

function get_id_from_slug($slug)
{
  try
  {
    $db = create_database();
    $id = -1;

    $sql = $db->prepare('SELECT [Id] FROM [Url] WHERE [Slug] = ?');

    if($sql->execute(array($slug)))
    {
      $row = $sql->fetch();
      
      if($row)
      {
        $id = $row['Id'];
      }
    }
    
    $db = null;
  }
  catch(Exception $e)
  {
    $id = -1;
  }

  return $id;
}

function get_redirect_uri($id)
{
  try
  {
    $db = create_database();
    $uri = null;

    $sql = $db->prepare('SELECT [Enabled], [Url] FROM [Url] WHERE [Id] = ?');

    if($sql->execute(array($id)))
    {
      $row = $sql->fetch();
      
      if($row && $row['Enabled'] != 0)
      {
        $uri = $row['Url'];

        if(strpos($uri, '{') >= 0)
        {
          $uri = replace_parameters($uri);
        }
      }
    }
    
    $db = null;
  }
  catch(Exception $e)
  {
    $uri = null;
  }

  return $uri;
}

function replace_parameters($value)
{
  return preg_replace_callback('|\{(.*?)\}|',
    function ($matches) 
    {
      $param = $matches[1];

      if(isset($_GET[$param]))
      {
        $result = $_GET[$param];
      }
      else
      {
        $result = '';
      }
    
      return $result;
    },
    $value);
}

function increment_hit_count($id, $url)
{
  try
  {
    $db = create_database();
    $timestamp = time();
    $address = get_client_ip_address();

    $sql = $db->prepare('INSERT INTO [Activity] ([UrlId], [Timestamp], [IpAddress], [Url]) VALUES (?, ?, ?, ?)');
    $sql->execute(array($id, $timestamp, $address, $url));

    $db = null;
  }
  catch(Exception $e)
  {
    // TODO: Do something
  }
}

function get_client_ip_address()
{
  if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
  {
    $address = $_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  else 
  {
    $address = $_SERVER['REMOTE_ADDR'];
  }

  return $address;
}

function get_api_key()
{
  if(isset($_SERVER['HTTP_APIKEY']))
  {
    $key = $_SERVER['HTTP_APIKEY'];
  }
  elseif(isset($_GET['apikey']))
  {
    $key = $_GET['apikey'];
  }
  else 
  {
    $key = null;
  }

  return $key;
}

function is_valid_apikey()
{
  try
  {
    $result = false;
    $key = get_api_key();

    if(isset($key) && $key !== '')
    {
      $db = create_database();

      $sql = $db->prepare('SELECT [Value] FROM [Config] WHERE [Name] = ?');

      if($sql->execute(array('ApiKey')))
      {
        $row = $sql->fetch();
        
        if($row)
        {
          $result = $row['Value'] == $key;
        }
      }
      
      $db = null;
    }
  }
  catch(Exception $e)
  {
    $result = false;
  }

  return $result;
}

function create_database()
{
  return new PDO('sqlite:data/redirects.db');
}

function get_html_document($title, $html)
{
  return '<!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>' . $title . '</title>
    <link rel="stylesheet" type="text/css" href="assets/styles.css" />
  </head>
  <body>
  <div class="wrapper">
    ' . $html . '
  </div>
  </body>
  </html>
  ';
}
?>