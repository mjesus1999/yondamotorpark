<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid p-4">
    <div class="alert alert-info mt-2 text-primary p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb mb-0" style="background-color: transparent; padding: 0;">
                <li class="breadcrumb-item"><a href="/caja" class="text-info">Caja</a></li>
                <li class="breadcrumb-item active">Buscar cliente</li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap gap-2">
            <a href="/caja" class="btn btn-outline-primary btn-sm"><i class="fas fa-list me-1"></i> Lista caja</a>
            <a href="/caja/pagos/denominacion" class="btn btn-dark btn-sm">Cobros por denominación</a>
            <a href="/clientes/createpersonclient" class="btn btn-success btn-sm" title="Requiere permiso módulo Clientes">
                <i class="bi bi-person-plus me-1"></i> Nuevo cliente persona
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-search me-2"></i>Buscar por DNI (8 dígitos)</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Busca al cliente en la base y muestra sus <strong>contratos activos</strong> para ir al cronograma y cobrar cuotas.
                        Si no tiene contrato, podés usar <strong>Cobros por denominación</strong> para conceptos / boleta.
                    </p>
                    <div class="input-group mb-3">
                        <input type="text" id="input-dni" class="form-control" maxlength="8" placeholder="DNI"
                            inputmode="numeric" pattern="[0-9]*">
                        <button class="btn btn-primary" type="button" id="btn-buscar">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                    <div id="msg-cliente" class="small"></div>
                    <div id="card-cliente" class="card border mt-3 d-none bg-light">
                        <div class="card-body py-2">
                            <div><strong>ID cliente:</strong> <span id="c-id"></span></div>
                            <div><strong>Nombre:</strong> <span id="c-nombre"></span></div>
                            <div><strong>DNI:</strong> <span id="c-dni"></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Contratos activos (cuotas)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Contrato</th>
                                    <th>Vehículo (resumen)</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-contratos">
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Buscá un DNI para ver contratos.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const inputDni = document.getElementById('input-dni');
    const btnBuscar = document.getElementById('btn-buscar');
    const msg = document.getElementById('msg-cliente');
    const cardCliente = document.getElementById('card-cliente');
    const tbody = document.getElementById('tbody-contratos');

    function setMsg(text, cls) {
        msg.className = 'small ' + (cls || 'text-muted');
        msg.textContent = text;
    }

    function renderContratos(rows) {
        tbody.innerHTML = '';
        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-warning py-3">Sin contrato ACT para este cliente. Podés usar <a href="/caja/pagos/denominacion">Cobros por denominación</a> para cobrar por conceptos.</td></tr>';
            return;
        }
        rows.forEach(r => {
            const tr = document.createElement('tr');
            const v = (r.vehiculo_resumen || '').replace(/^\s*\/\s*|\s*\/\s*$/g, '').trim() || '—';
            tr.innerHTML = `
                <td><span class="badge bg-dark">${r.idcontrato}</span></td>
                <td class="small">${escapeHtml(v)}</td>
                <td class="text-end text-nowrap">
                    <a class="btn btn-sm btn-info" href="/caja/cronograma/${r.idcontrato}" title="Cronograma / cobrar cuota"><i class="bi-receipt"></i> Cuotas</a>
                    <a class="btn btn-sm btn-outline-secondary" href="/caja/historial/pagos/${r.idcontrato}" title="Historial"><i class="bi bi-clock-history"></i></a>
                </td>`;
            tbody.appendChild(tr);
        });
    }

    function escapeHtml(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    async function buscar() {
        const dni = (inputDni.value || '').trim();
        if (dni.length !== 8 || !/^\d+$/.test(dni)) {
            setMsg('Ingresá un DNI de 8 dígitos.', 'text-danger');
            cardCliente.classList.add('d-none');
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">—</td></tr>';
            return;
        }
        setMsg('Buscando…', 'text-info');
        cardCliente.classList.add('d-none');
        tbody.innerHTML = '<tr><td colspan="3" class="text-center py-3"><span class="spinner-border spinner-border-sm"></span></td></tr>';

        try {
            const res = await fetch('/api/clienteByDNI/' + encodeURIComponent(dni));
            if (res.status === 404) {
                setMsg('Cliente no encontrado. Podés registrarlo con «Nuevo cliente persona» (si tenés permiso).', 'text-warning');
                tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">Sin datos</td></tr>';
                return;
            }
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();
            if (!data.success || !data.cliente) {
                setMsg('No se encontró el cliente.', 'text-warning');
                tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">Sin datos</td></tr>';
                return;
            }
            const c = data.cliente;
            document.getElementById('c-id').textContent = c.idcliente;
            document.getElementById('c-nombre').textContent = (c.cliente || c.nombrecompleto || [c.nombres, c.apellidos].filter(Boolean).join(' ')).trim();
            document.getElementById('c-dni').textContent = c.nrodoc || dni;
            cardCliente.classList.remove('d-none');
            setMsg('Cliente encontrado.', 'text-success');

            const r2 = await fetch('/api/caja/contratos-por-cliente/' + encodeURIComponent(c.idcliente));
            const j2 = await r2.json();
            if (!j2.success) throw new Error(j2.message || 'Error contratos');
            renderContratos(j2.data || []);
        } catch (e) {
            console.error(e);
            setMsg('Error de red o servidor.', 'text-danger');
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger py-3">Error</td></tr>';
        }
    }

    btnBuscar.addEventListener('click', buscar);
    inputDni.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); buscar(); }
    });
})();
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
