<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <!-- CABECERA -->
    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="#" class="text-decoration-none">Área de Cobranza</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Notificar a los Clientes
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/Cobranza" class="btn btn-outline-primary btn-sm">Volver</a>
            </div>
        </div>
    </div>

    <!-- TABLA DE DATOS -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <!-- CABECERA DE LA TABLA -->
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-bell me-2"></i>Clientes por Notificar</h6>
                        <button class="btn btn-primary btn-sm" id="btnNotificarTodos">
                            <i class="fas fa-paper-plane me-1"></i>Notificar Todos
                        </button>
                    </div>
                </div>

                <!-- TABLA CON DATOS -->
                <div class="card-body">
                    <table class="table table-sm table-hover table-hover-yonda">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Vehículo</th>
                                <th>Tienda</th>
                                <th>Cuotas</th>
                                <th>Monto Cuota</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody id="tabla-clientes">
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p class="mt-2">Cargando clientes...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Función para cargar clientes próximos a vencer
    function cargarClientesProximosVencer() {
        return new Promise((resolve, reject) => {
            fetch('/Cobranza/getClientesNotificar')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        resolve(data.data);
                    } else {
                        reject(new Error(data.message || 'Error al cargar clientes'));
                    }
                })
                .catch(error => reject(error));
        });
    }

    // Función para renderizar la tabla
    function renderizarTabla(clientes) {
        const tbody = document.getElementById('tabla-clientes');

        if (clientes.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay clientes próximos a vencer</p>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        clientes.forEach((cliente, index) => {
            html += `
                <tr data-row="${index + 1}" 
                    data-idcontrato="${cliente.idcontrato}"
                    data-telefono="${cliente.telefono}"
                    data-cliente="${cliente.cliente}"
                    data-monto="${cliente.monto_cuota}"
                    data-fecha="${cliente.fecha_vencimiento}">
                    <td>${index + 1}</td>
                    <td>${cliente.cliente}</td>
                    <td>${cliente.telefono || 'N/A'}</td>
                    <td>${cliente.vehiculo}</td>
                    <td>${cliente.local}</td>
                    <td>${cliente.cuotas_pagadas} de ${cliente.cuotas_totales}</td>
                    <td>S/. ${parseFloat(cliente.monto_cuota).toFixed(2)}</td>
                    <td>${cliente.fecha_vencimiento}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-secondary btn-estado">
                            <i class="fas fa-minus"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    }

    // Función para enviar SMS individual
    async function enviarSms(row) {
        const btnEstado = row.querySelector('.btn-estado');

        const datosCliente = {
            idcontrato: row.dataset.idcontrato,
            telefono: row.dataset.telefono,
            cliente: row.dataset.cliente,
            monto_cuota: row.dataset.monto,
            fecha_vencimiento: row.dataset.fecha
        };

        try {
            const response = await fetch('/Cobranza/enviarSmsNotificacion', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datosCliente)
            });

            const resultado = await response.json();

            if (resultado.success) {
                btnEstado.className = 'btn btn-sm btn-success btn-estado';
                btnEstado.innerHTML = '<i class="fas fa-check"></i>';
                btnEstado.title = 'Notificación enviada';
                return true;
            } else {
                btnEstado.className = 'btn btn-sm btn-danger btn-estado';
                btnEstado.innerHTML = '<i class="fas fa-times"></i>';
                btnEstado.title = 'Error al enviar';
                return false;
            }
        } catch (error) {
            console.error('Error al enviar SMS:', error);
            btnEstado.className = 'btn btn-sm btn-danger btn-estado';
            btnEstado.innerHTML = '<i class="fas fa-times"></i>';
            btnEstado.title = 'Error al enviar';
            return false;
        }
    }

    // Inicializar vista
    document.addEventListener('DOMContentLoaded', function () {
        cargarClientesProximosVencer()
            .then(clientes => {
                renderizarTabla(clientes);
                console.log('Clientes cargados correctamente');
            })
            .catch(error => {
                console.error('Error al cargar clientes:', error);
                document.getElementById('tabla-clientes').innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="alert alert-danger mb-0" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Error al cargar los datos. Por favor, recargue la página.
                            </div>
                        </td>
                    </tr>
                `;
            });
    });

    // Botón Notificar Todos
    document.getElementById('btnNotificarTodos').addEventListener('click', async function () {
        const btn = this;
        const rows = document.querySelectorAll('tbody tr[data-row]');

        if (rows.length === 0) {
            alert('No hay clientes para notificar');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Enviando...';

        for (const row of rows) {
            await enviarSms(row);
            // Esperar 1 segundo entre cada envío
            await new Promise(resolve => setTimeout(resolve, 1000));
        }

        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Notificar Todos';

        alert('Proceso de notificación completado');
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>