document.addEventListener("DOMContentLoaded", function () {
  const adresseInput = document.getElementById("association_adresse");
  const villeInput = document.getElementById("association_ville");
  const codePostalInput = document.getElementById("association_codePostal");
  const suggestionsList = document.getElementById("address-suggestions");

  adresseInput.addEventListener("input", function () {
    const query = adresseInput.value.trim();

    // On vérifie si la recherche contient au moins 3 caractères
    if (query.length >= 3) {
      fetch(
        `https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(
          query
        )}&limit=5`
      )
        .then((response) => response.json())
        .then((data) => {
          // Vider la liste des suggestions à chaque nouvelle recherche
          suggestionsList.innerHTML = "";

          if (data.features && data.features.length > 0) {
            // Affichage des résultats
            data.features.forEach((feature) => {
              const suggestionItem = document.createElement("li");
              suggestionItem.classList.add("list-group-item");
              suggestionItem.classList.add("cursor-pointer");
              suggestionItem.textContent = feature.properties.label;

              // Ajout de l'événement pour remplir les champs lorsque l'utilisateur sélectionne une adresse
              suggestionItem.addEventListener("click", function () {
                adresseInput.value = feature.properties.label;
                villeInput.value = feature.properties.city;
                codePostalInput.value = feature.properties.postcode;
                suggestionsList.innerHTML = ""; // Vider la liste
                suggestionsList.style.display = "none"; // Cacher la liste
              });

              suggestionsList.appendChild(suggestionItem);
            });

            suggestionsList.style.display = "block"; // Afficher la liste des résultats
          } else {
            // Si aucun résultat trouvé, afficher un message
            const noResultItem = document.createElement("li");
            noResultItem.classList.add("list-group-item");
            noResultItem.textContent =
              "Aucun résultat ne correspond à votre recherche";
            suggestionsList.appendChild(noResultItem);
            suggestionsList.style.display = "block"; // Afficher la liste
          }
        })
        .catch((error) => {
          //   console.error("Erreur API adresse:", error);
          //   // Afficher un message d'erreur si l'API échoue
          //   const errorItem = document.createElement("li");
          //   errorItem.classList.add("list-group-item");
          //   errorItem.textContent =
          //     "Erreur lors de la recherche de l'adresse. Veuillez réessayer.";
          //   suggestionsList.appendChild(errorItem);
          //   suggestionsList.style.display = "block"; // Afficher la liste
        });
    } else {
      // Si l'utilisateur tape moins de 3 caractères, cacher la liste
      suggestionsList.innerHTML = "";
      suggestionsList.style.display = "none";
    }
  });

  // Fermer la liste si l'utilisateur clique ailleurs
  document.addEventListener("click", function (event) {
    if (
      !adresseInput.contains(event.target) &&
      !suggestionsList.contains(event.target)
    ) {
      suggestionsList.style.display = "none";
    }
  });
});
