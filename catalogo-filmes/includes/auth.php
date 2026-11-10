<?php
require_once dirname(__DIR__) . '/config/funcoes.php';

if (empty($_SESSION['usuario_id'])) {
    mensagem('erro', 'Faça login para acessar o sistema.');
    redirecionar('login.php');
}
