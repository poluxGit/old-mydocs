<?php

/**
 * Script de test des events et Process
 *
 * Script Description
 *
 * @package Process
 * @subpackage Process
 * @author polux@poluxfr.org
 *
 */

// Ouverture session HTTP pour stockage des process en cours
session_start();

if(!array_key_exists('PID',$_SESSION) || (array_key_exists('PID',$_SESSION) && empty($_SESSION['PID'])) )
{
  // duplication du processus -> un fils et un père
  $pid = pcntl_fork();
  $_SESSION['PID'] = $pid;

  //Err
  if($pid == -1)
  {
    echo "Erreur duplication processus";
  }
  else if ($pid) // <> 0
  {
    # Father
    $_SESSION['PID'] = $pid;
    echo "Init father :".$pid;
  }
  else {
    # Child

    for($i=0;$i<10;$i++)
    {
      // 10 secondes
      sleep(1);
    }
    $_SESSION['PID'] = null;
    return 0;
  }
}
else {
  $lStatus = '';
  echo "Processus fils non fini! PID:".$_SESSION['PID'];
  pcntl_waitpid($_SESSION['PID'],$lStatus);
  echo "Retour fils : ".$lStatus;
}

?>
