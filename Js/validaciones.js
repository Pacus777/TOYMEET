function validaciones(event) {
  var fechaInput = document.getElementById("fecha_nac").value;
  var passwordInput = document.getElementById("password").value;

  // Validar fecha
  if (!fechaInput) {
      alert("Por favor, selecciona tu fecha de nacimiento.");
      event.preventDefault();
      return false;
  }

  const fechaNacimiento = new Date(fechaInput);
  const hoy = new Date();
  let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
  const mes = hoy.getMonth() - fechaNacimiento.getMonth();

  if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
      edad--;
  }

  if (edad < 18) {
      alert("Debes ser mayor de edad para registrarte.");
      event.preventDefault();
      return false;
  }

  // Validar contraseña segura
  const regexPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
  if (!regexPassword.test(passwordInput)) {
      alert("La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.");
      event.preventDefault();
      return false;
  }
  var telefono = document.getElementById("celular").value;
  var telefonoRegex = /^[0-9]+$/; // Solo números

  if (!telefono.match(telefonoRegex)) {
      alert("El número de teléfono debe contener solo números.");
      event.preventDefault();
      return false;
  }
  return true;
}