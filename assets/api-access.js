document.addEventListener("DOMContentLoaded", function () {
  const adresseInput = document.getElementById("adress_form_adresse");
  const villeInput = document.getElementById("adress_form_ville");
  const codePostalInput = document.getElementById("adress_form_codePostal");
  const suggestionsList = document.getElementById("address-suggestions");

  let activeSuggestionIndex = -1;
  let suggestions = [];

  // ✅ Mettre à jour la sélection active
  function updateActiveSuggestion(index) {
    const items = suggestionsList.querySelectorAll(".list-group-item");
    items.forEach((item, i) => {
      item.classList.toggle("active", i === index);
      if (i === index) {
        item.setAttribute("aria-selected", "true");
        adresseInput.setAttribute("aria-activedescendant", item.id);
        item.scrollIntoView({ block: "nearest" });
      } else {
        item.setAttribute("aria-selected", "false");
      }
    });
  }

  // ✅ Sélection d'une suggestion
  function selectSuggestion(index) {
    if (index >= 0 && index < suggestions.length) {
      const feature = suggestions[index];
      adresseInput.value = feature.properties.label;
      villeInput.value = feature.properties.city;
      codePostalInput.value = feature.properties.postcode;
      suggestionsList.innerHTML = "";
      suggestionsList.style.display = "none";
      adresseInput.removeAttribute("aria-activedescendant");
    }
  }

  adresseInput.addEventListener("input", function () {
    const query = adresseInput.value.trim();

    if (query.length >= 3) {
      fetch(
        `https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(
          query
        )}&limit=5`
      )
        .then((response) => response.json())
        .then((data) => {
          suggestions = data.features || [];
          suggestionsList.innerHTML = "";
          activeSuggestionIndex = -1;

          if (suggestions.length > 0) {
            suggestions.forEach((feature, index) => {
              const suggestionItem = document.createElement("li");
              suggestionItem.id = `suggestion-${index}`;
              suggestionItem.classList.add("list-group-item");
              suggestionItem.classList.add("cursor-pointer");
              suggestionItem.setAttribute("role", "option");
              suggestionItem.setAttribute("aria-selected", "false");
              suggestionItem.textContent = feature.properties.label;

              suggestionItem.addEventListener("click", function () {
                selectSuggestion(index);
              });

              suggestionsList.appendChild(suggestionItem);
            });
            suggestionsList.style.display = "block";
            suggestionsList.setAttribute("role", "listbox");
          } else {
            const noResultItem = document.createElement("li");
            noResultItem.classList.add("list-group-item");
            noResultItem.textContent =
              "Aucun résultat ne correspond à votre recherche";
            suggestionsList.appendChild(noResultItem);
            suggestionsList.style.display = "block";
          }
        })
        .catch((error) => console.error("Erreur API:", error));
    } else {
      suggestionsList.innerHTML = "";
      suggestionsList.style.display = "none";
    }
  });

  // ✅ Navigation au clavier
  adresseInput.addEventListener("keydown", function (event) {
    const items = suggestionsList.querySelectorAll(".list-group-item");
    if (suggestions.length === 0) return;

    if (event.key === "ArrowDown") {
      event.preventDefault();
      activeSuggestionIndex = (activeSuggestionIndex + 1) % suggestions.length;
      updateActiveSuggestion(activeSuggestionIndex);
    } else if (event.key === "ArrowUp") {
      event.preventDefault();
      activeSuggestionIndex =
        (activeSuggestionIndex - 1 + suggestions.length) % suggestions.length;
      updateActiveSuggestion(activeSuggestionIndex);
    } else if (event.key === "Enter") {
      event.preventDefault();
      selectSuggestion(activeSuggestionIndex);
    } else if (event.key === "Escape") {
      suggestionsList.innerHTML = "";
      suggestionsList.style.display = "none";
      adresseInput.removeAttribute("aria-activedescendant");
    }
  });

  // ✅ Fermer la liste si l'utilisateur clique ailleurs
  document.addEventListener("click", function (event) {
    if (
      !adresseInput.contains(event.target) &&
      !suggestionsList.contains(event.target)
    ) {
      suggestionsList.style.display = "none";
    }
  });

  // ✅ Accessibilité ARIA initiale
  adresseInput.setAttribute("role", "combobox");
  adresseInput.setAttribute("aria-expanded", "false");
  adresseInput.setAttribute("aria-haspopup", "listbox");
  suggestionsList.setAttribute("role", "listbox");
});
