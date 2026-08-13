<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Câmera + Noupe</title>
  <style>
    video {
      width: 300px;
      border-radius: 10px;
      border: 2px solid #444;
    }
    button {
      margin-top: 10px;
      padding: 10px 16px;
      font-size: 16px;
      cursor: pointer;
    }
  </style>
</head>
<body>

  <!-- Script externo -->
  <script src="https://www.noupe.com/embed/019c1f3c8294716498b8dd58d2621fb0c78b.js"></script>

  <h2>Câmera</h2>
  <video id="camera" autoplay playsinline></video><br>
  <button onclick="tirarFoto()">📸 Tirar foto</button>

  <canvas id="foto" style="display:none;"></canvas>

  <p id="resultado"></p>

  <script>
    const video = document.getElementById("camera");
    const canvas = document.getElementById("foto");
    const resultado = document.getElementById("resultado");

    navigator.mediaDevices.getUserMedia({ video: true })
      .then(stream => video.srcObject = stream)
      .catch(() => alert("Erro ao acessar a câmera"));

    function tirarFoto() {
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      canvas.getContext("2d").drawImage(video, 0, 0);

      resultado.innerText = "📸 Foto capturada com sucesso!";
    }
  </script>

</body>
</html>
