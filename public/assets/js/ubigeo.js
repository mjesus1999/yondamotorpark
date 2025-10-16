 
        const departamentosSelect = document.querySelector('#departamento');
        const provinciasSelect = document.querySelector('#provincia');
        const distritosSelect = document.querySelector('#distrito');
        

        const toastEl = document.getElementById('errorToast');
        if (toastEl) {
            const toast = new bootstrap.Toast(toastEl, {
                delay: 3000, 
                autohide: true
            });
            toast.show();
        }

        async function getAllDepartamentos() {
            try {
                const response = await fetch(`/api/ubigeo/departamentos`, {
                    method: 'GET'
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                departamentosSelect.innerHTML = `<option value='' selected>Seleccione</option>`;
                if (data.length > 0) {
                    data.forEach(element => {
                        departamentosSelect.innerHTML += `
                            <option value='${element.iddepartamento}'>${element.departamento}</option>
                        `;
                    });
                }
            } catch (e) {
                console.error("Error al obtener departamentos:", e);
            }
        }

        async function getProvinciasByDepartamento(iddepartamento) {
            provinciasSelect.innerHTML = `<option value='' selected>Seleccione</option>`;
            distritosSelect.innerHTML = `<option value='' selected>Seleccione</option>`;
            if (!iddepartamento) return;
            try {
                const response = await fetch(`/api/ubigeo/provincias/${iddepartamento}`, {
                    method: 'GET'
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                if (data.length > 0) {
                    data.forEach(element => {
                        provinciasSelect.innerHTML += `
                            <option value='${element.idprovincia}'>${element.provincia}</option>
                        `;
                    });
                }
            } catch (e) {
                console.error("Error al obtener provincias:", e);
            }
        }

        async function getDistritosByProvincia(idprovincia) {
            distritosSelect.innerHTML = `<option value='' selected>Seleccione</option>`;
            if (!idprovincia) return;
            try {
                const response = await fetch(`/api/ubigeo/distritos/${idprovincia}`, {
                    method: 'GET'
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                if (data.length > 0) {
                    data.forEach(element => {
                        distritosSelect.innerHTML += `
                            <option value='${element.iddistrito}'>${element.distrito}</option>
                        `;
                    });
                }
            } catch (e) {
                console.error("Error al obtener distritos:", e);
            }
        }

        departamentosSelect.addEventListener('change', (event) => {
            const iddepartamento = event.target.value;
            getProvinciasByDepartamento(iddepartamento);
           
        });

        provinciasSelect.addEventListener('change', (event) => {
            const idprovincia = event.target.value;
            getDistritosByProvincia(idprovincia);
        });

        getAllDepartamentos();

    