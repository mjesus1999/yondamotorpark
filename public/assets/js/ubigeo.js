 // Sistema de búsqueda nativo para selects
class SearchableSelect {
    constructor(inputId, dropdownId, hiddenInputId) {
        this.input = document.getElementById(inputId);
        this.dropdown = document.getElementById(dropdownId);
        this.hiddenInput = document.getElementById(hiddenInputId);
        this.options = [];
        this.isOpen = false;
        
        this.init();
    }
    
    init() {
        if (!this.input || !this.dropdown || !this.hiddenInput) return;
        
        // Event listeners
        this.input.addEventListener('focus', () => this.showDropdown());
        this.input.addEventListener('input', (e) => this.filterOptions(e.target.value));
        this.input.addEventListener('blur', () => {
            setTimeout(() => this.hideDropdown(), 150);
        });
        
        // Click en opciones del dropdown
        this.dropdown.addEventListener('click', (e) => {
            if (e.target.classList.contains('dropdown-item')) {
                this.selectOption(e.target);
            }
        });
        
        // Navegación con teclado
        this.input.addEventListener('keydown', (e) => this.handleKeyNavigation(e));
    }
    
    setOptions(options) {
        this.options = options;
        this.renderOptions();
    }
    
    renderOptions() {
        this.dropdown.innerHTML = '';
        this.options.forEach(option => {
            const div = document.createElement('div');
            div.className = 'dropdown-item';
            div.textContent = option.text;
            div.setAttribute('data-value', option.value);
            this.dropdown.appendChild(div);
        });
    }
    
    filterOptions(searchTerm) {
        const items = this.dropdown.querySelectorAll('.dropdown-item');
        let hasVisibleItems = false;
        
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            const matches = text.includes(searchTerm.toLowerCase());
            
            if (matches) {
                item.classList.remove('hidden');
                hasVisibleItems = true;
            } else {
                item.classList.add('hidden');
            }
        });
        
        if (hasVisibleItems) {
            this.showDropdown();
        } else {
            this.hideDropdown();
        }
    }
    
    selectOption(optionElement) {
        const value = optionElement.getAttribute('data-value');
        const text = optionElement.textContent;
        
        this.input.value = text;
        this.hiddenInput.value = value;
        this.input.classList.add('has-value');
        
        // Limpiar selección anterior
        this.dropdown.querySelectorAll('.dropdown-item').forEach(item => {
            item.classList.remove('selected');
        });
        
        // Marcar como seleccionado
        optionElement.classList.add('selected');
        
        this.hideDropdown();
        
        // Trigger change event
        this.hiddenInput.dispatchEvent(new Event('change'));
    }
    
    showDropdown() {
        this.dropdown.classList.add('show');
        this.isOpen = true;
    }
    
    hideDropdown() {
        this.dropdown.classList.remove('show');
        this.isOpen = false;
    }
    
    clear() {
        this.input.value = '';
        this.hiddenInput.value = '';
        this.input.classList.remove('has-value');
        this.dropdown.querySelectorAll('.dropdown-item').forEach(item => {
            item.classList.remove('selected', 'hidden');
        });
    }
    
    handleKeyNavigation(e) {
        if (!this.isOpen) return;
        
        const visibleItems = Array.from(this.dropdown.querySelectorAll('.dropdown-item:not(.hidden)'));
        const currentSelected = this.dropdown.querySelector('.dropdown-item.selected');
        let currentIndex = visibleItems.indexOf(currentSelected);
        
        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                currentIndex = Math.min(currentIndex + 1, visibleItems.length - 1);
                this.highlightOption(visibleItems[currentIndex]);
                break;
            case 'ArrowUp':
                e.preventDefault();
                currentIndex = Math.max(currentIndex - 1, 0);
                this.highlightOption(visibleItems[currentIndex]);
                break;
            case 'Enter':
                e.preventDefault();
                if (currentSelected) {
                    this.selectOption(currentSelected);
                }
                break;
            case 'Escape':
                this.hideDropdown();
                break;
        }
    }
    
    highlightOption(optionElement) {
        this.dropdown.querySelectorAll('.dropdown-item').forEach(item => {
            item.classList.remove('selected');
        });
        if (optionElement) {
            optionElement.classList.add('selected');
        }
    }
}

// Inicializar searchable selects
let departamentoSelect, provinciaSelect, distritoSelect;

document.addEventListener('DOMContentLoaded', function() {
    departamentoSelect = new SearchableSelect('departamento-input', 'departamento-dropdown', 'departamento');
    provinciaSelect = new SearchableSelect('provincia-input', 'provincia-dropdown', 'provincia');
    distritoSelect = new SearchableSelect('distrito-input', 'distrito-dropdown', 'distrito');
    
    // Event listeners para cascada
    document.getElementById('departamento').addEventListener('change', (e) => {
        getProvinciasByDepartamento(e.target.value);
    });
    
    document.getElementById('provincia').addEventListener('change', (e) => {
        getDistritosByProvincia(e.target.value);
    });
    
    // Cargar departamentos iniciales
    getAllDepartamentos();
});

async function getAllDepartamentos() {
    try {
        const response = await fetch(`/api/ubigeo/departamentos`, {
            method: 'GET'
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        
        const options = [
            { value: '', text: '' },
            ...data.map(item => ({ value: item.iddepartamento, text: item.departamento }))
        ];
        
        if (departamentoSelect) {
            departamentoSelect.setOptions(options);
        }
    } catch (e) {
        console.error("Error al obtener departamentos:", e);
    }
}

async function getProvinciasByDepartamento(iddepartamento) {
    // Limpiar provincia y distrito
    if (provinciaSelect) {
        provinciaSelect.clear();
        provinciaSelect.setOptions([{ value: '', text: '' }]);
    }
    if (distritoSelect) {
        distritoSelect.clear();
        distritoSelect.setOptions([{ value: '', text: '' }]);
    }
    
    if (!iddepartamento) return;
    
    try {
        const response = await fetch(`/api/ubigeo/provincias/${iddepartamento}`, {
            method: 'GET'
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        
        const options = [
            { value: '', text: '' },
            ...data.map(item => ({ value: item.idprovincia, text: item.provincia }))
        ];
        
        if (provinciaSelect) {
            provinciaSelect.setOptions(options);
        }
    } catch (e) {
        console.error("Error al obtener provincias:", e);
    }
}

async function getDistritosByProvincia(idprovincia) {
    // Limpiar distrito
    if (distritoSelect) {
        distritoSelect.clear();
        distritoSelect.setOptions([{ value: '', text: '' }]);
    }
    
    if (!idprovincia) return;
    
    try {
        const response = await fetch(`/api/ubigeo/distritos/${idprovincia}`, {
            method: 'GET'
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        
        const options = [
            { value: '', text: 'Seleccione distrito' },
            ...data.map(item => ({ value: item.iddistrito, text: item.distrito }))
        ];
        
        if (distritoSelect) {
            distritoSelect.setOptions(options);
        }
    } catch (e) {
        console.error("Error al obtener distritos:", e);
    }
}

const toastEl = document.getElementById('errorToast');
if (toastEl) {
    const toast = new bootstrap.Toast(toastEl, {
        delay: 3000, 
        autohide: true
    });
    toast.show();
}