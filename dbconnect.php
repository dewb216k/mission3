<?php
function connect(){
$dsn = 'mysql:dbname='�f�[�^�x�[�X��';host='�z�X�g';charset=utf8';
$username = '���[�U�[��';
$password = '�p�X���[�h';

$options = array(
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
);
return new PDO($dsn, $username, $password, $options);
}
?>