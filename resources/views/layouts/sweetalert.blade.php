<!-- SweetAlert2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener("turbo:load", initSweetAlerts);
    if (!window.Turbo) initSweetAlerts();

    function initSweetAlerts() {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Tampilkan Flash Message sebagai Toast
        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        @endif

        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: '{{ session('error') }}'
            });
        @endif

        @if(session('info'))
            Toast.fire({
                icon: 'info',
                title: '{{ session('info') }}'
            });
        @endif

        @if($errors->any())
            Toast.fire({
                icon: 'error',
                title: 'Terdapat kesalahan pada input Anda'
            });
        @endif
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
