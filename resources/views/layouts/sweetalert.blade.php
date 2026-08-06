<!-- SweetAlert2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>

<div id="flash-messages" class="hidden">
    @if(session('success')) <span class="flash-success">{{ session('success') }}</span> @endif
    @if(session('error')) <span class="flash-error">{{ session('error') }}</span> @endif
    @if(session('info')) <span class="flash-info">{{ session('info') }}</span> @endif
    @if($errors->any()) <span class="flash-validation">Terdapat kesalahan pada input Anda</span> @endif
</div>

<script>
    document.addEventListener("turbo:load", initSweetAlerts);
    if (!window.Turbo) initSweetAlerts();

    function initSweetAlerts() {
        const flashContainer = document.getElementById('flash-messages');
        if (!flashContainer) return;

        const triggerToast = (msg, type) => {
            if (typeof window.showToast === 'function') {
                window.showToast(msg, type);
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({ toast: true, position: 'top-end', icon: type, title: msg, showConfirmButton: false, timer: 3000 });
            }
        };

        const success = flashContainer.querySelector('.flash-success');
        if (success) { triggerToast(success.innerText, 'success'); success.remove(); }

        const error = flashContainer.querySelector('.flash-error');
        if (error) { triggerToast(error.innerText, 'error'); error.remove(); }

        const info = flashContainer.querySelector('.flash-info');
        if (info) { triggerToast(info.innerText, 'info'); info.remove(); }

        const validation = flashContainer.querySelector('.flash-validation');
        if (validation) { triggerToast(validation.innerText, 'error'); validation.remove(); }
    }

    // Global Confirm Handler untuk elemen dengan data-confirm
    document.addEventListener('submit', function(e) {
        if (e.target && e.target.hasAttribute('data-confirm')) {
            e.preventDefault();
            const form = e.target;
            const message = form.getAttribute('data-confirm');
            
            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#14b8a6', // Warna brand Recyclink
                cancelButtonColor: '#ef4444', // Merah
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.removeAttribute('data-confirm'); // Hapus atribut agar tidak infinite loop
                    form.submit();
                }
            });
        }
    });
</script>
