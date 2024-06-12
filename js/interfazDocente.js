document.addEventListener("DOMContentLoaded", function () {
    // Función para actualizar las opciones de tiempo
    function updateTimeOptions(element, startTime, endTime) {
        let options = "";
        let current = new Date(`2021-01-01T${startTime}:00`);
        let end = new Date(`2021-01-01T${endTime}:00`);

        while (current <= end) {
            let hour = current.getHours();
            let value = (hour < 10 ? "0" + hour : hour) + ":00"; // Solo se considera la hora completa
            options += `<option value="${value}">${value}</option>`;
            current.setHours(current.getHours() + 1); // Incrementa en 1 hora
        }

        element.innerHTML = options;
        element.value = startTime; // Establece un valor inicial
    }

    // Actualizar opciones para horaInicioMatutina y horaFinMatutina
    const inicioMatutina =
        document.getElementById("horaInicioMatutina");
    const finMatutina = document.getElementById("horaFinMatutina");
    updateTimeOptions(inicioMatutina, "07:00", "13:00");
    updateTimeOptions(finMatutina, "07:00", "13:00");

    const inicioVespertina = document.getElementById(
        "horaInicioVespertina"
    );
    const finVespertina =
        document.getElementById("horaFinVespertina");
    updateTimeOptions(inicioVespertina, "14:00", "22:00");
    updateTimeOptions(finVespertina, "14:00", "22:00");
});

$(document).ready(function() {
    $('#docenteYHorariosForm').on('submit', function(e) {
        e.preventDefault();  // Evitar que el formulario se envíe de manera tradicional
        
        var formData = $(this).serialize();  // Serializar los datos del formulario

        $.post('../Controladores/docenteController.php', formData, function(response) {
            alert('Información guardada');  // Mostrar un mensaje al usuario
            $('#docenteYHorariosForm').trigger("reset");  // Resetear el formulario
        });
    });
});

window.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const message = urlParams.get('message') || 'Ocurrió un error no especificado.';

    if (status === 'success') {
        Swal.fire({
            title: 'Éxito',
            text: 'El docente se ha guardado correctamente.',
            icon: 'success'
        });
    } else if (status === 'error') {
        Swal.fire({
            title: 'Error',
            text: message,
            icon: 'error'
        });
    }
});


