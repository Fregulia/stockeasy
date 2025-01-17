// FUNÇÃO PARA CARREGAR O CABEÇALHO
function cabecalho() {
  // CHAMA (REQUISIÇÃO) O 'header.php' E ADICIONA ELE AO ELEMENTO DE ID 'header-container'
  fetch('preload/header.php')
    .then(response => response.text()) // CONVERTE A RESPOSTA PARA TEXTO
    .then(data => {
      document.getElementById('header-container').innerHTML = data; // INSERE O CONTEÚDO NO ELEMENTO
    });
}

// ASSIM QUE O DOM FOR CARREGADO, EXECUTA A FUNÇÃO CABEÇALHO
document.addEventListener('DOMContentLoaded', cabecalho);

// FUNÇÃO PARA CARREGAR O RODAPÉ
function rodape() {
  // CHAMA O 'footer.html' E ADICIONA ELE AO ELEMENTO DE ID 'footer-container'
  fetch('preload/footer.html')
    .then(response => response.text()) // CONVERTE A RESPOSTA PARA TEXTO
    .then(data => {
      document.getElementById('footer-container').innerHTML = data; // INSERE O CONTEÚDO NO ELEMENTO
    });
}

// ASSIM QUE O DOM FOR CARREGADO, EXECUTA A FUNÇÃO RODAPÉ
document.addEventListener('DOMContentLoaded', rodape);
