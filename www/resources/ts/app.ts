import Swal from 'sweetalert2';
import '../js/echo';
import './ui/DataTable';

declare global {
    interface Window {
        Swal: any;
        toast: any;
    }
}

window.Swal = Swal;

const toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
    didOpen: (toastEl) => {
        toastEl.addEventListener('mouseenter', Swal.stopTimer)
        toastEl.addEventListener('mouseleave', Swal.resumeTimer)
    }
});
window.toast = toast;

document.addEventListener('DOMContentLoaded', () => {
    // 1. Feedback Loading: Desabilita forms e mostra Loading ao Submit
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function () {
            if (form.classList.contains('is-submitting')) return;
            form.classList.add('is-submitting');

            const submitBtns = form.querySelectorAll('button[type="submit"]');
            submitBtns.forEach(btn => {
                const b = btn as HTMLButtonElement;
                // Blindar duplo click mas manter os estilos
                b.style.pointerEvents = 'none';
                b.style.opacity = '0.7';
                
                b.innerHTML = `<span style="display:inline-flex; align-items:center; gap:0.5rem;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite;"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/></svg> Carregando...</span>`;
            });
        });
    });

    // 2. Feedback Confirm: Substitui Alerts e Confirms Nativos por Modais Clean
    const confirms = document.querySelectorAll('[data-confirm]');
    confirms.forEach(el => {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            const message = el.getAttribute('data-confirm') || 'Confirmar essa operação crítica?';
            const tgt = (e.currentTarget) as HTMLElement;
            
            Swal.fire({
                title: 'Confirmação Necessária',
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#455073',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Sim, confirmar ação',
                cancelButtonText: 'Cancelar'
            }).then((result: any) => {
                if (result.isConfirmed) {
                    if (tgt.tagName === 'A') {
                        window.location.href = tgt.getAttribute('href')!;
                    } else {
                        const form = tgt.closest('form');
                        if (form) form.submit();
                    }
                }
            });
        });
    });

    // Spin CSS
    const style = document.createElement('style');
    style.innerHTML = `@keyframes spin { 100% { transform: rotate(360deg); } }`;
    document.head.appendChild(style);
});
