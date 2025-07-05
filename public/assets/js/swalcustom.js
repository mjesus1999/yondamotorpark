/**
 * Muestra una pregunta basado en la librería SweetAlert
 * @param {string} pregunta Describa la pregunta que quiere mostrar
 * @param {string} modulo Módulo de la aplicación desde donde se genera (créditos, clientes, ventas, etc.)
 * @returns {boolean} Retorna un valor lógico basado en una promesa
 */
async function ask(pregunta = ``, modulo = `Yonda`){
  const respuesta = await Swal.fire({
    title: pregunta,
    text: modulo,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Aceptar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#3498db',
    footer: 'Motorpark Yonda Perú App Ver. 1.0'
  });

  return respuesta.isConfirmed;
}

//Implementarlo así:
/*
document.querySelector("#btn1").addEventListener("click", async () => {
  if (await ask("¿Por qué siempre pierde la selección?")){
    console.log("Porque no hay inversión")
  }
})
*/

//Puede ser de 4 tipos: INFO, WARNING, ERROR, SUCCESS
function showToast(message = ``, type = `INFO`, duration = 2500, url = null){
  const bgColor = {
    'INFO'    : '#22a6b3',
    'WARNING' : '#f39c12',
    'SUCCESS' : '#6ab04c',
    'ERROR'   : '#eb4d4b'
  };

  Swal.fire({
    toast: true,
    icon: type.toLowerCase(),
    iconColor: 'white',
    color: 'white',
    text: message,
    timer: duration,
    timerProgressBar: true,
    position: 'top-end',
    showConfirmButton: false,
    background: bgColor[type]
  }).then(() => {
    if (url != null){
      window.location.href = url;
    }
  });
}

/**
 * Muestra una notificación de éxito
 * @param {string} message Mensaje a mostrar
 * @param {number} duration Duración en milisegundos
 * @param {string} url URL a la que redirigir después (opcional)
 */
function showSuccess(message = 'Operación exitosa', duration = 3000, url = null) {
  showToast(message, 'SUCCESS', duration, url);
}

/**
 * Muestra una notificación de error
 * @param {string} message Mensaje a mostrar
 * @param {number} duration Duración en milisegundos
 */
function showError(message = 'Ha ocurrido un error', duration = 4000) {
  showToast(message, 'ERROR', duration);
}

/**
 * Muestra una notificación de advertencia
 * @param {string} message Mensaje a mostrar
 * @param {number} duration Duración en milisegundos
 */
function showWarning(message = 'Advertencia', duration = 3000) {
  showToast(message, 'WARNING', duration);
}

/**
 * Muestra una notificación informativa
 * @param {string} message Mensaje a mostrar
 * @param {number} duration Duración en milisegundos
 */
function showInfo(message = 'Información', duration = 3000) {
  showToast(message, 'INFO', duration);
}

/**
 * Muestra un modal de confirmación para eliminar
 * @param {string} message Mensaje de confirmación
 * @param {string} title Título del modal
 * @returns {Promise<boolean>} Retorna true si confirma, false si cancela
 */
async function confirmDelete(message = '¿Estás seguro de que quieres eliminar este elemento?', title = 'Confirmar eliminación') {
  const result = await Swal.fire({
    title: title,
    text: message,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  });
  
  return result.isConfirmed;
}

/**
 * Muestra un modal de confirmación para actualizar
 * @param {string} message Mensaje de confirmación
 * @param {string} title Título del modal
 * @returns {Promise<boolean>} Retorna true si confirma, false si cancela
 */
async function confirmUpdate(message = '¿Deseas actualizar este elemento?', title = 'Confirmar actualización') {
  const result = await Swal.fire({
    title: title,
    text: message,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Sí, actualizar',
    cancelButtonText: 'Cancelar'
  });
  
  return result.isConfirmed;
}

/**
 * Muestra un loading spinner
 * @param {string} message Mensaje a mostrar
 */
function showLoading(message = 'Cargando...') {
  Swal.fire({
    title: message,
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });
}

/**
 * Oculta el loading spinner
 */
function hideLoading() {
  Swal.close();
}

/**
 * Muestra un modal de éxito con botón de confirmación
 * @param {string} message Mensaje a mostrar
 * @param {string} title Título del modal
 * @param {string} url URL a la que redirigir después (opcional)
 */
function showSuccessModal(message = 'Operación exitosa', title = '¡Éxito!', url = null) {
  Swal.fire({
    title: title,
    text: message,
    icon: 'success',
    confirmButtonColor: '#6ab04c',
    confirmButtonText: 'Aceptar'
  }).then(() => {
    if (url) {
      window.location.href = url;
    }
  });
}

/**
 * Muestra un modal de error con botón de confirmación
 * @param {string} message Mensaje a mostrar
 * @param {string} title Título del modal
 */
function showErrorModal(message = 'Ha ocurrido un error', title = 'Error') {
  Swal.fire({
    title: title,
    text: message,
    icon: 'error',
    confirmButtonColor: '#eb4d4b',
    confirmButtonText: 'Aceptar'
  });
}