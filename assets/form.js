// <-- BOUTON RADIO CUSTOM -->
document.querySelectorAll(".btn-check").forEach((radio) => {
  // Assurez-vous que le label est correctement lié au radio.
  const label = document.querySelector(`label[for="${radio.id}"]`);

  label.addEventListener("keydown", function (event) {
    // Lorsque l'utilisateur appuie sur la barre d'espace ou Entrée, sélectionnez l'élément radio
    if (event.key === " " || event.key === "Enter") {
      event.preventDefault();
      // Empêcher la page de se défiler sur espace

      // Cocher le bouton radio et le mettre en focus
      radio.checked = true;
      radio.focus();

      // Mettre à jour l'attribut aria-checked pour les technologies d'assistance
      label.setAttribute("aria-checked", "true");

      // Assurez-vous que les autres boutons radio sont désactivés et que leur état aria-checked est mis à jour
      document.querySelectorAll(".btn-check").forEach((otherRadio) => {
        if (otherRadio !== radio) {
          const otherLabel = document.querySelector(
            `label[for="${otherRadio.id}"]`
          );
          otherLabel.setAttribute("aria-checked", "false");
        }
      });
    }
  });
});
