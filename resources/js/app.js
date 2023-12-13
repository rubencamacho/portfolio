import './bootstrap';

// import Alpine from 'alpinejs';

// window.Alpine = Alpine;
// // import { Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm'

// // Livewire.start()
// Alpine.start();

// import './sweetalert';
// import './events';

/* Sweetalert 2 */
const GeneralSwal = Swal.mixin({
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

/* Events */
window.addEventListener('notify', event => {
    GeneralSwal.fire({
        icon: 'success',
        title: event.detail.message
    })
})


