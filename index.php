<?php

    require_once 'functions.php';

    // Variáveis Globais
    $editing = false;
    $error = null;
    $msg = null;
    $reset = null;
    $value = 'Cadastrar';

    if (isset($_GET['event'])){

        // Setando o valor do evento na variável
        $event = $_GET['event'];
        

        if ($event == 'editar'){

            // Seta o valor da variável para True, o que ocasiona na criação de um novo input no formulário
            $editing = true;

            // Buscando o valor do ID do corretor
            $idCorretor = $_REQUEST['id'];

            // Setando o valor da variável que altera o nome do botão ao entrar no modo de edição
            $value = "Salvar";

            // Buscando os valores dos corretores no banco de dados
            $sql = "SELECT * FROM corretores";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {

                // Mapeando os valores e setando em novas variáveis
                while ($row = $result->fetch_assoc()) {
                    if ($idCorretor === $row['id']){
                        $corretor_id = $row['id'];
                        $corretor_name = $row['nome'];            
                        $corretor_cpf = $row['cpf'];
                        $corretor_creci = $row['creci'];
                    }
                }

                // Setando os valores nos campos do formulário via JAVASCRIPT
                $edit = "<script>const nome = document.querySelector('#nome'); const cpf = document.querySelector('#cpf'); const creci = document.querySelector('#creci'); nome.value = `$corretor_name`; cpf.value = `$corretor_cpf`; creci.value = `$corretor_creci` </script>";
            }

        } else if ($event == 'excluir') {

            // Pegando o id do corretor
            $id = $_GET['id'];

            if (excluirCorretor($id)){ // Excluindo os dados do corretor
                
                // Redirecionando para a página index.php
                $msg  = "<script> const msg = document.querySelector('#message'); msg.innerHTML = 'Exclusão realizada com sucesso!'; msg.style.opacity = 1;
                </script>";
                // Redireciona para página index.php
                $reset ="<script>setTimeout(()=>{window.location.href = 'index.php'}, 2000)</script>";
            }
        }
    }

    if($_SERVER["REQUEST_METHOD"] == 'POST'){

        // Coletando os valores das requisições
        if (isset($id)){
            $id = $_POST['id'];
        }

        // Pergando os valores da requisição
        $nome = $_POST['nome'];
        $cpf = $_POST['cpf'];
        $creci = $_POST['creci'];

        if (isset($_REQUEST['edited'])){ // Verificação se o formulário esta em modo de edição
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $id = $_REQUEST['id'];
                $nome = $_REQUEST['nome'];
                $cpf = $_REQUEST['cpf'];
                $creci = $_REQUEST['creci'];
        
                if (editarCorretor($id, $nome, $cpf, $creci)) {
                    $msg  = "<script> const msg = document.querySelector('#message'); msg.innerHTML = 'Alteração realizada com sucesso!'; msg.style.opacity = 1;
                    </script>";
                    // Redireciona para página index.php
                    $reset ="<script>setTimeout(()=>{window.location.href = 'index.php'}, 2000)</script>";
                }
            }
        } else {

            // Validação dos campos de NOME e CRECI
            if (strlen($nome) < 2 || strlen($creci) < 2) {
                $msg = "<script> const msg = document.querySelector('#message'); msg.innerHTML = 'Os campos de Nome e creci precisam ter mais que um caracter'; msg.style.opacity = 1;
                msg.classList.add('error');
                    </script>";

                $reset ="<script>setTimeout(()=>{window.location.href = 'index.php'}, 2000)</script>";

            } else if (strlen($cpf) != 11) { // Validação do campo de CPF
                $msg = "<script> const msg = document.querySelector('#message'); msg.innerHTML = 'O campos de CPF precisa ter 11 caracteres'; msg.style.opacity = 1; msg.classList.add('error');
                console.log('CPF');
                    </script>";

                $reset ="<script>setTimeout(()=>{window.location.href = 'index.php'}, 2000)</script>";
            } else {

                // Inserindo os valores no banco de dados
                if (inserirCorretor($nome, $cpf, $creci)) {
    
                    // Setando a mensagem no top do formulário
                    $msg  = "<script> const msg = document.querySelector('#message'); msg.innerHTML = 'Cadastro realizado com sucesso!'; msg.style.opacity = 1;
                    </script>";
    
                    // Redireciona para a mesma página
                    $reset ="<script>setTimeout(()=>{window.location.href = 'index.php'}, 2000)</script>";
                }
            }       
        }
    }

    // Consulta os dados dos corretores
    try {
        $sql = "SELECT * FROM corretores";

        if (!$sql){
            throw new Exception("Não foi possível receber os dados do banco",1);

        } else {
            $result = $conn->query($sql);
        }

    } catch (\Exception $e) {
        print_r($e);
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Cadastro de Corretores</title>
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <main>
        <h3 id="message">texto qualquer</h3>
        <div id="cadastro-corretores">
            <h2>Cadastro de Corretores</h2>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="fields">
                    <input type="text" id="cpf" name="cpf" maxlength="11" class="small"  placeholder="Digite o seu CPF">
                    <input type="text" id="creci" name="creci" maxlength="6" class="medium" placeholder="Digite o número do CRECI" >
                    <input type="text" id="nome" name="nome" class="large"   placeholder="Digite seu nome completo" >
                </div>
                <input type="submit" id='btn' value="<?php echo $value ?>">
                <?php
                    if($editing){
                        echo "<input type='hidden' name='edited' value=`edited`>";
                        echo "<input type='hidden' name='id' value='$corretor_id'>";
                    }
                ?>
            </form>
        </div>
        <div id="lista-corretores">
            <h2>Lista de Corretores</h2>
            <table>
                <tr id='thead'>
                    <td class="small">ID</td>
                    <td class="large">NOME</td>
                    <td class="medium">CPF</td>
                    <td class="medium">CRECI</td>
                    <td class="large">Ações</td>
                </tr>
                <?php
                    if (!$result){
                        echo "";
                    } else {
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr id='tbody'>";
                                echo "<td class='small'>" . $row["id"] . "</td>";
                                echo "<td class='large'>" . $row["nome"] . "</td>";
                                echo "<td class='medium'>" . $row["cpf"] . "</td>";
                                echo "<td class='medium'>" . $row["creci"] . "</td>";
                                echo "<td class='large'>
                                        <a href='index.php?id=".$row["id"]."&event=editar' id='editar'>Editar</a>
                                        <a href='index.php?id=".$row["id"]."&event=excluir' id='excluir'>Excluir</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>Nenhum corretor cadastrado.</td></tr>";
                        }
                    }
                ?>
            </table>
        </div>
    </main>
    <?php
        echo $msg; 
        echo $reset; 
        if(isset($edit)){echo $edit;}
    ?> 
</body>

</html>