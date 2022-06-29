<?php
if($_SERVER['REQUEST_METHOD']=='GET'){
  require('connect.php');
  $pass_hash=array();
  try{
    $get=$db->prepare("select pass from users where login=?");
    $get->execute(array('admin'));
    $pass_hash=$get->fetchAll()[0][0];
  }
  catch(PDOException $e){
    print('Error: '.$e->getMessage());
  }
  //аутентификация
  if (empty($_SERVER['PHP_AUTH_USER']) ||
      empty($_SERVER['PHP_AUTH_PW']) ||
      $_SERVER['PHP_AUTH_USER'] != 'admin' ||
      md5($_SERVER['PHP_AUTH_PW']) != $pass_hash) {
    header('HTTP/1.1 401 Unanthorized');
    header('WWW-Authenticate: Basic realm="My site"');
    print('<h1>401 Требуется авторизация</h1>');
    exit();
  }
  if(!empty($_COOKIE['del'])){
    echo 'Пользователь '.$_COOKIE['del_user'].' был успешно удалён <br>';
    setcookie('del','',100000);
    setcookie('del_user','',100000);
  }
  print('Вы успешно авторизовались и видите защищенные паролем данные.');
  $users=array();
  $powers=array();
  $power_array=array('Бессмертие','Телепортация','Полет');
  $powers_count=array();
  try{
    $app=$db->prepare("select * from application");
    $app->execute();
    $users=$app->fetchALL();
    $power=$db->prepare("select * from superpwrs");
    $power->execute();
    $powers=$power->fetchALL();
    $count=$db->prepare("select count(*) from superpwrs where power_name=?");
    foreach($power_array as $pwr){
      $count->execute(array($pwr));
      $powers_count[]=$count->fetchAll()[0][0];
    }
  }
  catch(PDOException $e){
    print('Error: '.$e->getMessage());
    exit();
  }
  include('table.php');
}
else{
  header('Location: admin.php');
}

