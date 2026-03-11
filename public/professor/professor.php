
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>OlwSchool — Área do Professor</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

  <!-- Navbar -->
  <?php include __DIR__ . '/navbar.php'; ?>

  <!-- ============================== -->
  <!-- Conteúdo principal -->
  <!-- ============================== -->
  <div class="flex-grow-1" style="margin-left: 220px;">
    <main class="container py-4">

      <h1 class="h4 mb-2">Bem-vindo, <span id="userName"></span>!</h1>
      <p class="mb-4 text-muted">Aqui estão suas informações.</p>

      <!-- Card inicial (opcional, já no padrão das outras telas) -->
      <div class="card shadow-sm border-0">
        <div class="card-body py-5 text-center">
          <h2 class="h6 fw-bold mb-2">Painel do Professor</h2>
          <p class="mb-0 text-muted">Use o menu à esquerda para acessar tarefas, provas, chamadas e comunicados.</p>
        </div>
      </div>

    </main>
  </div>

  <!-- ============================== -->
  <!-- Scripts -->
  <!-- ============================== -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script src="/owl-school/public/assets/js/api/logout.js"></script>

  <script src="/owl-school/public/assets/js/api/authme.js"></script>

  <script src="/owl-school/public/assets/js/api/get_name.js"></script>

</body>
</html>
