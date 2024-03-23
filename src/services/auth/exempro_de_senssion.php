<?php
session_start();


if (
    !isset ($_SESSION['email_session']) ||
    !isset ($_SESSION['senha_session']) ||
    ($_SESSION['tipo_usuario'] !== 'empresa')
) {
    exit;
} elseif (
    !isset ($_SESSION['google_session']) ||
    !isset ($_SESSION['token_session']) ||
    ($_SESSION['google_usuario'] !== 'empresa')
) {
    header("Location: ../Login/login.html");
    exit;
}
?>