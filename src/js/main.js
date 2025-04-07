function showMessage(idMessage, message) {
  const messageDiv = document.getElementById(idMessage);
  messageDiv.textContent = message;
  messageDiv.style.display = "flex";
  messageDiv.style.opacity = "1";
  // temporisation de 2 secondes
  setTimeout(() => {
    hideMessage(idMessage);
  }, 2000);
}

function hideMessage(idMessage) {
  const messageDiv = document.getElementById(idMessage);
  messageDiv.style.opacity = "0";
  setTimeout(() => {
    messageDiv.style.display = "none";
  }, 1000); // correspond à la durée de la transition CSS
}
