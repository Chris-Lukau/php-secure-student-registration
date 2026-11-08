<?php
require_once 'connetion.php';

// verificar se recebemos um post com o ID do usuário a ser deletado

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        echo "ID Inválido.";
        exit();
    }

    // Preparando o comando SQL para deletar o usuário pelo ID
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);

    // executar esse comando SQL e o que acontece se executar
    if ($stmt->execute()) {
        echo "Usuário deletado com sucesso!";
        header("Location: listar.php");
    } else {
        echo "Erro ao deletar!";
        header("Location: listar.php");;
    }
} else {
    header("Location: listar.php");
}