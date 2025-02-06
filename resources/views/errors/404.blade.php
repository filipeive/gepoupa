{{-- resources/views/errors/404.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Não Encontrada - 404</title>
    <style>
        /* Estilo geral da página */
body {
    margin: 0;
    padding: 0;
    background-color: #f4f4f9;
    font-family: 'Arial', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    overflow: hidden;
}

/* Estilo da página de erro */
.error-page {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}

/* Estilo do container de erro */
.error-content {
    text-align: center;
    padding: 2rem 1.5rem;
    max-width: 500px;
    background: #ffffff;
    border-radius: 1rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

/* Ícone de erro */
.error-icon {
    font-size: 6rem;
    color: #007bff;
    margin-bottom: 1rem;
}

/* Título de erro */
.error-title {
    font-size: 3rem;
    font-weight: 700;
    color: #343a40;
    margin-bottom: 1rem;
}

/* Mensagem de erro */
.error-message {
    font-size: 1.1rem;
    color: #6c757d;
    margin-bottom: 2rem;
    line-height: 1.5;
}

/* Botões de ação */
.btn-return {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    font-weight: 600;
    color: #ffffff;
    background-color: #007bff;
    border: none;
    border-radius: 0.375rem;
    text-transform: uppercase;
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-return:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
}

.btn-return:active {
    background-color: #003d80;
    transform: translateY(0);
}

/* Responsividade */
@media (max-width: 576px) {
    .error-title {
        font-size: 2.5rem;
    }

    .error-message {
        font-size: 1rem;
    }

    .btn-return {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
}

/* Estilo das partículas */
.particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
}

        
        @keyframes bounce {
            0%, 100% {
                transform: translateY(-5%);
                animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
            }
            50% {
                transform: translateY(0);
                animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
            }
        }
    </style>
</head>
<body>
    <div class="row">
    <div class="error-page">
        <div class="container">
            <div class="error-content">
                <div class="animate-bounce">
                    <i class="mdi mdi-cloud-question error-icon"></i>
                </div>
                
                <h1 class="error-title">404</h1>
                <p class="error-message">
                    Oops! Parece que você se perdeu.
                    <br>
                    A página que você está procurando não foi encontrada.
                </p>
                
                <div class="error-actions">
                    <a href="javascript:history.back()" class="btn btn-outline-primary btn-return">
                        <i class="mdi mdi-arrow-left me-1"></i>
                        Voltar à Página Anterior
                    </a>
                </div>
            </div>
        </div>
        <canvas id="particles" class="particles"></canvas>
    </div>
</div>
    
    <!-- Scripts -->
    <script src="{{ asset('vendors/js/vendor.bundle.base.js') }}"></script>
    <script>
        // Animação de partículas no background
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('particles');
            const ctx = canvas.getContext('2d');
            
            // Configuração do canvas
            function resizeCanvas() {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            }
            
            window.addEventListener('resize', resizeCanvas);
            resizeCanvas();
            
            // Configuração das partículas
            const particles = [];
            const particleCount = 50;
            
            class Particle {
                constructor() {
                    this.x = Math.random() * canvas.width;
                    this.y = Math.random() * canvas.height;
                    this.speedX = Math.random() * 2 - 1;
                    this.speedY = Math.random() * 2 - 1;
                    this.size = Math.random() * 3 + 1;
                }
                
                update() {
                    this.x += this.speedX;
                    this.y += this.speedY;
                    
                    if (this.x > canvas.width) this.x = 0;
                    if (this.x < 0) this.x = canvas.width;
                    if (this.y > canvas.height) this.y = 0;
                    if (this.y < 0) this.y = canvas.height;
                }
                
                draw() {
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    ctx.fillStyle = 'rgba(101, 113, 255, 0.3)';
                    ctx.fill();
                }
            }
            
            // Criar partículas
            for (let i = 0; i < particleCount; i++) {
                particles.push(new Particle());
            }
            
            // Animação
            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                particles.forEach(particle => {
                    particle.update();
                    particle.draw();
                });
                
                requestAnimationFrame(animate);
            }
            
            animate();
        });
    </script>
</body>
</html>