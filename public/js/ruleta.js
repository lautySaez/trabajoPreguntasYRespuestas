const canvas = document.getElementById("ruleta");
const ctx = canvas.getContext("2d");
const botonGirar = document.getElementById("boton-girar");
const resultado = document.getElementById("resultado");
const categoriaTexto = document.getElementById("categoria-elegida");
const btnIniciar = document.getElementById("btn-iniciar");
const audioRuleta = document.getElementById("sonidoRuleta");
const categorias = ["Deporte", "Entretenimiento", "Historia", "Ciencia", "Arte", "Geografía"];
const colores = ["#e63946", "#f1c40f", "#2ecc71", "#3498db", "#9b59b6", "#e67e22"];
const total = categorias.length;
const anguloPorSector = (2 * Math.PI) / total;

let anguloActual = 0;
let girando = false;

function dibujarRuleta() {
    const radio = canvas.width / 2;
    const centro = radio;

    ctx.clearRect(0, 0, canvas.width, canvas.height);

    const gradiente = ctx.createRadialGradient(centro, centro, 50, centro, centro, 200);
    gradiente.addColorStop(0, "#333");
    gradiente.addColorStop(1, "#111");
    ctx.fillStyle = gradiente;
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    for (let i = 0; i < total; i++) {
        const start = anguloActual + i * anguloPorSector;
        const end = start + anguloPorSector;

        ctx.beginPath();
        ctx.moveTo(centro, centro);
        ctx.arc(centro, centro, radio - 5, start, end);
        ctx.fillStyle = colores[i];
        ctx.fill();

        ctx.save();
        ctx.translate(centro, centro);
        ctx.rotate(start + anguloPorSector / 2);
        ctx.textAlign = "right";
        ctx.fillStyle = "#fff";
        ctx.font = "bold 20px Poppins";
        ctx.fillText(categorias[i], radio - 25, 10);
        ctx.restore();
    }

    ctx.beginPath();
    ctx.arc(centro, centro, 40, 0, 2 * Math.PI);
    ctx.fillStyle = "#111";
    ctx.fill();
    ctx.lineWidth = 3;
    ctx.strokeStyle = "#fff";
    ctx.stroke();
}

function girarRuleta() {
    if (girando) return;
    girando = true;
    botonGirar.disabled = true;

    resultado.classList.remove("resultado-activo");
    resultado.style.display = "none";

    audioRuleta.loop = true;
    audioRuleta.currentTime = 0;
    audioRuleta.play().catch(e => {
        console.warn("Reproducción de audio bloqueada.", e);
    });

    const giros = 360 * 5 + Math.random() * 360;
    const duracion = 5000;
    const inicio = performance.now();

    function animar(now) {
        const progreso = Math.min((now - inicio) / duracion, 1);
        const easing = 1 - Math.pow(1 - progreso, 3);
        const angulo = (giros * easing * Math.PI) / 180;

        anguloActual = angulo;
        dibujarRuleta();

        if (progreso < 1) {
            requestAnimationFrame(animar);
        } else {
            mostrarResultado(giros % 360);
        }
    }

    requestAnimationFrame(animar);
}

function mostrarResultado(gradosFinal) {
    audioRuleta.pause();
    audioRuleta.loop = false;

    const gradosAjustados = (360 - ((gradosFinal + 90) % 360)) % 360;
    const index = Math.floor(gradosAjustados / (360 / total));
    const categoria = categorias[index];

    categoriaTexto.textContent = `Categoría elegida: ${categoria}`;
    btnIniciar.href = `index.php?controller=partida&method=iniciarPartida&categoria=${encodeURIComponent(categoria)}`;

    resultado.style.display = "flex";
    resultado.classList.add("resultado-activo");

    girando = false;
}

dibujarRuleta();
botonGirar.addEventListener("click", girarRuleta);