<?php
use geertw\IpAnonymizer\IpAnonymizer;
require 'vendor/autoload.php';

function should_anonomise_ip_address()
{
  return filter_var(get_config_value('AnonymizeAddresses'), FILTER_VALIDATE_BOOLEAN);
}

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

function write_forbidden($title, $reason)
{
  $html = get_html_document($title, $reason);

  write_error(403);

  header('HTTP/1.1 403 Forbidden');
  header('Content-Type: text/html');
  header('Content-Length: ' . strlen($html));

  echo $html;
}

function write_bad_request($title, $reason)
{
  $html = get_html_document($title, $reason);

  write_error(400);

  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: text/html');
  header('Content-Length: ' . strlen($html));

  echo $html;
}

function write_not_found()
{
  $html = get_html_document('Not found', 'The requested resource cannot be found.');

  write_error(404);

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
    $db = get_database_connection();
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
    $db = get_database_connection();
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
    $db = get_database_connection();

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

  if(should_anonomise_ip_address())
  {
    $address = IpAnonymizer::anonymizeIp($address);
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

function get_config_value($name)
{
  try
  {
    $db = get_database_connection();

    $result = null;

    $sql = $db->prepare('SELECT [Value] FROM [Config] WHERE [Name] = ?');

    if($sql->execute(array($name)))
    {
      $row = $sql->fetch();
      
      if($row)
      {
        $result = $row['Value'];
      }
    }
    
    $db = null;
  }
  catch(Exception $e)
  {
    $result = null;
  }

  return $result;
}

function is_valid_apikey()
{
  $key = get_api_key();

  if(isset($key) && $key !== '')
  {
    $result = get_config_value('ApiKey') === $key;
  }
  else
  {
    $result = false;
  }

  return $result;
}

function get_title()
{
  return get_config_value('Title');
}

function get_home_page_url()
{
  return get_config_value('HomePage');
}

function get_system_version()
{
  return get_config_value('SystemVersion');
}

function get_schema_version()
{
  try
  {
    $db = get_database_connection();
    
    $version = null;
    $result = $db->query('SELECT [SchemaVersion] FROM [SystemVersion];');

    if($result)
    {
      $row = $result->fetch();

      if($row)
      {
        $version = $row['SchemaVersion'];
      }
    }

    $db = null;
  }
  catch(Exception $e)
  {
    $version = null;
  }

  return $version;}

function get_database_connection()
{
  $connection = new PDO('sqlite:data/redirects.db');

  $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

  return $connection;
}

function get_html_document($title, $html)
{
  return get_header_html($title) . '<div class="wrapper">' . $html . '</div>' . get_footer_html();
}

function write_error($code)
{
  try
  {
    $db = get_database_connection();

    $url = $_SERVER['REQUEST_URI'];
    $timestamp = time();
    $address = get_client_ip_address();

    $sql = $db->prepare('INSERT INTO [Error] ([Timestamp], [IpAddress], [Url], [StatusCode]) VALUES (?, ?, ?, ?)');

    $sql->execute(array($timestamp, $address, $url, $code));

    $db = null;
  }
  catch(Exception $e)
  {
    // TODO: What now
  }
}

function get_footer_html()
{
  return '<footer>
  <p>Copyright &copy; 2018 Cyotek Ltd. All Rights Reserved.</p>
  <p>System Version ' . get_system_version() . ' / Schema Version ' . get_schema_version() . '</p>
</footer></body></html>';
}

function get_header_html($title)
{
  return '<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>' . $title . '</title>
    <link rel="stylesheet" type="text/css" href="assets/styles.css" />
  </head>
  <body>';
}

function write_header_html($title)
{
  echo(get_header_html($title));
}

function write_footer_html()
{
  echo(get_footer_html());
}
?>