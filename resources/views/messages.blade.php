@if(Session::has('success'))
    <div class="alert alert-success alert-dismissible fade-in-pop text-center position-relative" role="alert">
        <span class="icon">🧪</span>{{ Session::get('success') }}
        <button type="button"
                class="close position-absolute" style="top: 0.5rem; right: 1rem;"
                data-dismiss="alert" aria-label="Cerrar">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

@if(Session::has('error'))
    <div class="alert alert-danger alert-dismissible fade-in-pop text-center position-relative" role="alert">
        <span class="icon">⚠️</span>
        <strong>¡Atención!</strong> {{ Session::get('error') }}
        <button type="button"
                class="close position-absolute" style="top: 0.5rem; right: 1rem;"
                data-dismiss="alert" aria-label="Cerrar">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

<style>
    .fade-in-pop {
        animation: fadePop 0.8s ease-out forwards;
        transform: scale(0.9);
        opacity: 0;
        transition: all 0.5s ease;
    }

    @keyframes fadePop {
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .alert {
        margin-top: 1rem;
        padding: 1rem 2rem;
        border-radius: 0.75rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .alert .icon {
        font-size: 1.5rem;
        margin-right: 0.5rem;
    }

    .alert-success {
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
        border: 1px solid #b2dfb2;
        color: #155724;
    }

    .alert-danger {
        background: linear-gradient(135deg, hsl(0, 100%, 88.6%), #f5b5b5);
        border: 1px solid #e6c3c3;
        color: rgb(87, 21, 21);
    }

    .close {
        background: none;
        border: none;
        font-size: 1.2rem;
        color: #000;
        cursor: pointer;
    }
</style>

<script>
    // Opcional: Auto cerrar después de 5 segundos
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.classList.remove('fade-in-pop');
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>
