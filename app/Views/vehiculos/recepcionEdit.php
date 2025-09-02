<?php include __DIR__ . '/../layout/header.php'; ?>
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #183de1ff 0%, #2482dbff 100%);
        --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        --card-shadow-hover: 0 20px 40px rgba(0, 0, 0, 0.15);
        --border-radius: 16px;
        --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }



    /* Cards principales con gradientes y efectos modernos */
    .modern-card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        overflow: hidden;
        position: relative;
    }

    .modern-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary-gradient);
        opacity: 0;
        transition: var(--transition);
    }

    .modern-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--card-shadow-hover);
    }

    .modern-card:hover::before {
        opacity: 1;
    }

    /* Card de información de la OC */
    .info-card-oc {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.05) 100%);
        border: 1px solid rgba(102, 126, 234, 0.2);
    }

    .info-card-oc .card-header {
        background: var(--primary-gradient);
        color: white;
        border: none;
        padding: 1.5rem;
        font-weight: 600;
        font-size: 1.1rem;
    }

    /* Card de vehículos */
    .vehicles-card {
        background: linear-gradient(135deg, rgba(79, 172, 254, 0.08) 0%, rgba(0, 242, 254, 0.05) 100%);
        border: 1px solid rgba(79, 172, 254, 0.2);
    }

    .vehicles-card .card-header {
        background: var(--primary-gradient);
        color: white;
        border: none;
        padding: 1.5rem;
        font-weight: 600;
        font-size: 1.1rem;
    }

    /* Cards internas (concesionario y datos OC) */
    .inner-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        transition: var(--transition);
        background: white;
        position: relative;
        overflow: hidden;
    }


    .inner-card:hover::after {
        transform: scaleX(1);
    }

    .inner-card .card-header {
        background: rgba(102, 126, 234, 0.05);
        border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        font-weight: 600;
        color: #4a5568;
        font-size: 0.9rem;
        padding: 0.75rem 1.25rem;
    }



    /* Tabla moderna */
    .modern-table {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
    }

    .modern-table thead th {
        background: var(--success-gradient);
        color: white;
        font-weight: 600;
        border: none;
        padding: 1rem 1.5rem;
        font-size: 0.95rem;
        letter-spacing: 0.5px;
    }

    .modern-table tbody tr {
        transition: var(--transition);
        cursor: pointer;
        border: none;
    }

    .modern-table tbody tr.selected {
        background: var(--primary-gradient);
        font-weight: bold;
        transform: scale(1.02);
        box-shadow: 0 4px 15px rgba(23, 17, 209, 1);
    }

    .modern-table td {
        padding: 1rem 1.5rem;
        border-color: rgba(0, 0, 0, 0.05);
        vertical-align: middle;
    }

    /* Formulario de actualización */
    .update-form-card {
        background: linear-gradient(135deg, rgba(220, 220, 233, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }


    .update-form-card .card-header {
        background: var(--primary-gradient);
    }




    /* Inputs modernos */
    .modern-input {
        border: 2px solid rgba(102, 126, 234, 0.1);
        border-radius: 10px;
        transition: var(--transition);
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
    }

    .modern-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        background: white;
        transform: translateY(-2px);
    }



    /* Switch moderno */
    .modern-switch .form-check-input {
        width: 3rem;
        height: 1.5rem;
        background: linear-gradient(135deg, #ddd, #bbb);
        border: none;
        border-radius: 1rem;
        transition: var(--transition);
    }

    .modern-switch .form-check-input:checked {
        background: var(--success-gradient);
        box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
    }


    /* Animaciones de entrada */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #updateFormCard {
        display: none;
        animation: slideInUp 0.5s ease-out;
    }

    #updateFormCard.active {
        display: block;
    }

    /* Efectos de glassmorphism */
    .glass-effect {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.18);
    }

    /* Texto con gradiente */
    .gradient-text {
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 700;
    }

    /* Iconos flotantes */
    .floating-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary-gradient);
        color: white;
        margin-right: 0.75rem;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    /* Responsive mejoras */
    @media (max-width: 768px) {
        .modern-card:hover {
            transform: none;
        }

        .modern-table tbody tr:hover {
            transform: none;
        }
    }
</style>

<?php  var_dump($datosRecepcion) ?>

  <div class="container-fluid py-4">
    <div class="alert alert-info mt-2" role="alert">
        <div class="row align-items-center">
            <div class="col-md-6 d-flex justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="#">Recepción Vehículos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Actualizar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/recepcionVehiculos" class=""><i class="fas fa-list-ul me-2"></i>Listar Recepciones</a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card modern-card info-card-oc">
                <div class="card-header">
                    <div class="floating-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    Información de la OC
                </div>
                <div class="card-body p-4">
                    <div class="card inner-card mb-4">
                        <div class="card-header">
                            <i class="fas fa-building me-2"></i>Concesionario
                        </div>
                        <div class="card-body text-center py-4">
                            <h5 class="card-title text-primary fw-bold mb-0">
                                <?php echo htmlspecialchars($datosRecepcion['info_compra']['razonsocial_concesionario'] ?? 'N/A'); ?>
                            </h5>
                        </div>
                    </div>

                    <div class="card inner-card">
                        <div class="card-header">
                            <i class="fas fa-chart-line me-2"></i>Datos de la OC
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive p-2">
                                <table class="table modern-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Amortizado</th>
                                            <th>Saldo</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($datosRecepcion['info_compra']['fechacompra'] ?? 'N/A'); ?></strong></td>
                                            <td><span class="badge bg-success"><?php echo number_format($datosRecepcion['info_compra']['total_amortizado'] ?? 0, 2); ?></span></td>
                                            <td><span class="badge bg-secondary"><?php echo number_format($datosRecepcion['info_compra']['saldo_pendiente'] ?? 0, 2); ?></span></td>
                                            <td><span class="badge bg-primary"><?php echo number_format($datosRecepcion['info_compra']['total_compra_con_igv'] ?? 0, 2); ?></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card modern-card vehicles-card mb-4">
                <div class="card-header">
                    <div class="floating-icon">
                        <i class="fas fa-car-side"></i>
                    </div>
                    Datos de los Vehículos de la OC
                </div>
                <div class="card-body">
                    <p class="text-muted fw-bold mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Seleccione un vehículo de la tabla para actualizar sus datos.
                    </p>
                    <div class="table-responsive">
                        <table class="table modern-table" id="tabla-vehiculo">
                            <thead>
                                <tr>
                                    <th>Modelo</th>
                                    <th>Versión</th>
                                    <th>Combustible</th>
                                    <th>Año</th>
                                    <th>Chasis</th>
                                    <th>Placa</th>
                                    <th>Placa r.</th>
                                    <th>Serie motor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($datosRecepcion['vehiculos'])): ?>
                                    <?php foreach ($datosRecepcion['vehiculos'] as $vehiculo): ?>
                                        <tr data-id="<?php echo htmlspecialchars($vehiculo['idvehiculo']); ?>" class="vehicle-row">
                                            <td><?php echo htmlspecialchars($vehiculo['modelo'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($vehiculo['version'] ?? 'N/A'); ?></td>
                                            <td><span class="badge <?php echo ($vehiculo['combustible'] == 'Gasolina') ? 'bg-primary' : 'bg-success'; ?>"><?php echo htmlspecialchars($vehiculo['combustible'] ?? 'N/A'); ?></span></td>
                                            <td><?php echo htmlspecialchars($vehiculo['anio'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($vehiculo['chasis'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($vehiculo['placa'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($vehiculo['placarotativa'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($vehiculo['seriemotor'] ?? 'N/A'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No hay vehículos asociados a esta Orden de Compra.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card modern-card update-form-card" id="updateFormCard">
                <div class="card-header fw-bold text-white">
                    <div class="floating-icon">
                        <i class="fas fa-pencil-alt"></i>
                    </div>
                    Actualizar datos del vehículo
                </div>
                <div class="card-body">
                    <div class="alert alert-info glass-effect mb-4" id="selectedVehicleInfo">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Vehículo Seleccionado:</strong> Ninguno
                    </div>

                    <form class="form">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6 col-lg-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-hashtag me-2 text-primary"></i>Chasis
                                </label>
                                <input type="text" class="form-control modern-input" name="chasis" placeholder="Número de chasis">
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-id-card me-2 text-success"></i>Placa
                                </label>
                                <input type="text" class="form-control modern-input" name="placa" placeholder="Número de placa">
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-sync-alt me-2 text-warning"></i>Placa R.
                                </label>
                                <input type="text" class="form-control modern-input" name="placarotativa" placeholder="Placa rotativa">
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-engine me-2 text-danger"></i>Serie Motor
                                </label>
                                <input type="text" class="form-control modern-input" name="seriemotor" placeholder="Número de serie">
                            </div>
                        </div>

                        <div class="row g-3 align-items-end mb-4">
                            <div class="col-md-4 col-lg-4">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-palette me-2 text-info"></i>Color
                                </label>
                                <input type="text" class="form-control modern-input" name="color" placeholder="Color del vehículo">
                            </div>
                            <div class="col-md-4 col-lg-4">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-store me-2 text-purple"></i>Tienda
                                </label>
                                <select name="local" class="form-select modern-input">
                                    <option value="">Seleccione una tienda</option>
                                    <option value="chincha">Chincha</option>
                                    <option value="ica">Ica</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-save me-2"></i>Guardar Cambios
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch modern-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="estEnPiso" checked>
                                    <label class="form-check-label fw-bold ms-2" for="estEnPiso">
                                        <i class="fas fa-check-circle me-2"></i>¿Está en piso de venta?
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tablaVehiculos = document.querySelector('#tabla-vehiculo');
            const updateFormCard = document.getElementById('updateFormCard');
            const selectedVehicleInfo = document.getElementById('selectedVehicleInfo');

            tablaVehiculos.addEventListener('click', (e) => {
                const row = e.target.closest('tr');
                if (row && row.classList.contains('vehicle-row')) {
                    // Elimina la clase 'selected' de todas las filas
                    document.querySelectorAll('.vehicle-row').forEach(r => r.classList.remove('selected'));

                    row.classList.add('selected');

                    // Obtiene los datos del vehículo de la fila seleccionada
                    const modelo = row.cells[0].textContent.trim();
                    const version = row.cells[1].textContent.trim();
                    const combustible = row.cells[2].textContent.trim();
                    const anio = row.cells[3].textContent.trim();

                    // Actualiza el texto en el formulario
                    selectedVehicleInfo.innerHTML = `
                        
                        <strong>Vehículo Seleccionado:</strong> ${modelo} ${version} - ${anio}
                        <span class="badge bg-primary ms-2">${combustible}</span>
                    `;

                    // Muestra el formulario con una animación suave
                    if (!updateFormCard.classList.contains('active')) {
                        updateFormCard.style.display = 'block';
                        setTimeout(() => updateFormCard.classList.add('active'), 10);
                    }
                }
            });

            // Animaciones adicionales para los inputs
            document.querySelectorAll('.modern-input').forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });

                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });
        });
    </script>

    <?php include __DIR__ . '/../layout/footer.php'; ?>