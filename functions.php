<?php
    require_once 'config.php';

    function inserirCorretor($nome, $cpf, $creci) {
        global $conn;

        // Validação básica dos dados (adicione mais validações conforme necessário)
        if (empty($nome) || empty($cpf) || empty($creci)) {
            return false;
        }

        $stmt = $conn->prepare("INSERT INTO corretores (nome, cpf, creci) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nome, $cpf, $creci);

        if ($stmt->execute()) {
            return true;
        } else {
            echo "Erro ao inserir corretor: " . $stmt->error;
            return false;
        }
    }

    function editarCorretor($id, $nome, $cpf, $creci) {
        global $conn;

        // Validação básica dos dados (adicione mais validações conforme necessário)
        if (empty($id) || empty($nome) || empty($cpf) || empty($creci)) {
            return false;
        }

        $stmt = $conn->prepare("UPDATE corretores SET nome = ?, cpf = ?, creci = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nome, $cpf, $creci, $id);

        if ($stmt->execute()) {
            return true;
        } else {
            echo "Erro ao atualizar corretor: " . $stmt->error;
            return false;
        }
    }

    function excluirCorretor($id) {
        global $conn;

        $stmt = $conn->prepare("DELETE FROM corretores WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return true;
        } else {
            echo "Erro ao excluir corretor: " . $stmt->error;
            return false;
        }
    }