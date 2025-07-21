<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo "<p>Inicia sesión para usar el chat.</p>";
    exit();
}
?>
<div id="chat">
    <h3>Chat con administrador</h3>
    <div id="mensajes" style="border:1px solid #ccc; height:200px; overflow-y:scroll;"></div>
    <input type="text" id="mensaje" placeholder="Escribe tu mensaje...">
    <button onclick="enviarMensaje()">Enviar</button>
</div>

<script>
function cargarMensajes() {
    fetch("cargar_chat.php")
    .then(res => res.text())
    .then(data => {
        document.getElementById("mensajes").innerHTML = data;
    });
}
setInterval(cargarMensajes, 2000);

function enviarMensaje() {
    var mensaje = document.getElementById("mensaje").value;
    fetch("gmensaje.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "mensaje=" + encodeURIComponent(mensaje)
    }).then(() => {
        document.getElementById("mensaje").value = "";
        cargarMensajes();
    });
}
</script>
