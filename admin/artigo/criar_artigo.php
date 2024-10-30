<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redireciona para a página de login ou exibe uma mensagem de erro
    header('Location: login.php?message=Acesso negado');
    exit();
}

require '../../database/database.php';

$db = new Database();
$conn = $db->getConnection();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Artigo</title>
    <script src="https://cdn.tiny.cloud/1/ubxb4pw8cwy003ierxq8ist4mzsq2e3l0qci31jxgj9d2wza/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#conteudo',
            plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
            toolbar_mode: 'floating',
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save(); // Salva as mudanças no textarea
                });
            }
        });

        function atualizarDataHora() {
            var agora = new Date();
            var ano = agora.getFullYear();
            var mes = String(agora.getMonth() + 1).padStart(2, '0');
            var dia = String(agora.getDate()).padStart(2, '0');
            var hora = String(agora.getHours()).padStart(2, '0');
            var minuto = String(agora.getMinutes()).padStart(2, '0');
            var segundo = String(agora.getSeconds()).padStart(2, '0');
            var dataHora = ano + '-' + mes + '-' + dia + 'T' + hora + ':' + minuto + ':' + segundo;

            document.getElementById('data_hora').value = dataHora;
        }

        function validarFormulario(event) {
            var novaCategoria = document.getElementById('nova_categoria').value.trim();
            var categoriaSelecionada = document.getElementById('categoria').value;

            // Verifica se o TinyMCE está vazio
            if (tinymce.get('conteudo').getContent().trim() === '') {
                event.preventDefault(); // Impede o envio do formulário se o conteúdo estiver vazio
                alert('O campo de conteúdo não pode estar vazio.');
                return false;
            }

            // Verifica se uma nova categoria foi inserida sem selecionar uma existente
            if (novaCategoria === '' && categoriaSelecionada === '') {
                event.preventDefault(); // Impede o envio do formulário se nenhuma categoria for selecionada ou inserida
                alert('Escolha uma categoria ou insira uma nova.');
                return false;
            }
        }
    </script>
</head>

<body onload="atualizarDataHora()">
    <h1>Criar Artigo</h1>
    <form action="salvar_artigo.php" method="post" enctype="multipart/form-data" onsubmit="validarFormulario(event)">
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" required><br><br>

        <label for="conteudo">Conteúdo:</label>
        <textarea id="conteudo" name="conteudo" rows="10" cols="30" required></textarea><br><br>

        <label for="data_hora">Data e Hora:</label>
        <input type="datetime-local" id="data_hora" name="data_hora" readonly required><br><br>

        <label for="imagem">Imagem:</label>
        <input type="file" id="imagem" name="imagem"><br><br>



        <label for="categoria">Categoria:</label>
        <select id="categoria" name="categoria_id" onchange="buscarSubcategorias(this.value)">
            <option value="">Escolha uma categoria</option>
            <?php
            require_once '../../database/database.php';

            // Cria uma instância da classe Database
            $db = new Database();
            $pdo = $db->getConnection(); // Obtém a conexão PDO

            // Carregar categorias
            try {
                $stmt = $pdo->query("SELECT id, nome FROM categorias");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nome']) . "</option>";
                }
            } catch (PDOException $e) {
                echo "<option value=''>Erro ao carregar categorias</option>";
            }
            ?>
        </select><br><br>

        <label for="subcategoria">Subcategoria:</label>
        <select id="subcategoria" name="subcategoria_id">
            <option value="">Escolha uma subcategoria</option>
        </select><br><br>



        <input type="submit" value="Criar Artigo"><br><br>
        <a href="../categoria/criar_categoria.php">Criar Categoria</a>
    </form>
</body>
<script>
    function buscarSubcategorias(categoriaId) {
        // Limpar o select de subcategorias
        const subcategoriaSelect = document.getElementById('subcategoria');
        subcategoriaSelect.innerHTML = '<option value="">Escolha uma subcategoria</option>';

        // Verifica se foi selecionada uma categoria
        if (categoriaId !== "") {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "../subCategoria/buscar_subcategorias.php?categoria_id=" + categoriaId, true);
            xhr.onload = function() {
                if (this.status === 200) {
                    const subcategorias = JSON.parse(this.responseText);
                    subcategorias.forEach(function(subcategoria) {
                        const option = document.createElement("option");
                        option.value = subcategoria.id;
                        option.textContent = subcategoria.nome;
                        subcategoriaSelect.appendChild(option);
                    });
                }
            };
            xhr.send();
        }
    }
</script>


</html>