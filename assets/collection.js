$(document).ready(function () {
  //   <---- Navigation entre les étapes ---->

  let currentStep = 1;

  $(".next-step").on("click", function () {
    $("#step-" + currentStep).hide();
    currentStep++;

    // À l'étape 4, générer dynamiquement la card de prévisualisation du membre 1
    if (currentStep === 4) {
      let fieldTarget2 = $('div[data-target="fields"]')
        .find(".card")
        .attr("data-index");

      // Récupérer les valeurs saisies dans le formulaire de l'étape 3
      let nom = $("#association_membres_" + fieldTarget2 + "_nom").val();
      let prenom = $("#association_membres_" + fieldTarget2 + "_prenom").val();

      if (nom && prenom) {
        // Afficher la card avec les informations du membre 1
        $("#member-name").text(nom + " " + prenom);
        $("#member-preview-card").show();

        $("#modal-member-form").append(
          $("#membre-" + fieldTarget2).addClass("d-none")
        );
      } else {
        // Masquer la card si les données ne sont pas disponibles
        $("#member-preview-card").hide();
      }
    }

    $("#step-" + currentStep).show();
  });

  // Gestion du stepper previous
  $(".previous-step").on("click", function () {
    $("#step-" + currentStep).hide();
    currentStep--;
    // Remmetre le membre 1 dans le formulaire de l'étape 3
    if (currentStep === 3) {
      $('div[data-target="fields"]').append(
        $("#membre-0").removeClass("d-none")
      );
    }
    $("#step-" + currentStep).show();
  });

  //   <---- Fin logique navigation entre les étapes ---->

  //   <---- Début logique gestion collection ajout des membres ---->
  let fieldTarget = $('div[data-target="field"]');
  const fieldsTarget = $('div[data-target="fields"]');
  const prototypeValue = $("#initialMember").attr("data-prototype");

  let index = fieldTarget.length;
  let itemsCountValue = index;
  console.log(index);
  // Recuperation des elements pour les documents
  const fieldsTargetDoc = $('div[data-target-doc="fieldsDoc"]');
  const prototypeValueDoc = $("#initialMemberDoc").attr("data-prototype-doc");

  function addItemV2() {
    const isFirst = itemsCountValue === 0;
    const prototype = JSON.parse(prototypeValue).replace(/__name__/g, index);

    if (isFirst) {
      fieldsTarget.append(prototype);

      // Au click sur le bouton editer on modifie les evenements attachés aux autres boutons
      $(`.edit-member[data-index="${index}"]`).on("click", function () {
        $("#membre-0").removeClass("d-none");
        $("#editMemberModal").modal("show");

        //Modifier l'evenement de sauvergarde en edit
        $("#save-member")
          .off("click")
          .on("click", function () {
            editMember($("#membre-0").attr("data-index"));
          });

        // Lier les boutons de fermeture de la modal pour juste fermer la modal sans supprimer les informations du membre
        $('button[data-bs-dismiss="modal"]').each(function () {
          $(this)
            .off("click")
            .on("click", function (e) {
              $("#editMemberModal").modal("hide");
              $("#membre-0").addClass("d-none");
            });
        });
      });
    } else {
      $("#modal-member-form").append(prototype);
      // <------On supprime ça c'est ce qui cause le probleme de la modal on gere l'affichage avec les propriete bootstrap----->
      // $("#editMemberModal").modal("show");

      // Lier le bouton de sauvegarde
      $("#save-member")
        .off("click")
        .on("click", function () {
          saveMember($('div[data-target="field"]').last().attr("data-index"));
        });
    }
    // Ajout dynamique des documents du membre
    addItemDoc(index);

    index++;
    itemsCountValue++;
    checkItemsCount();
  }

  // <---------Rajout de code pour gérer lorsqu'il y a une erreur on remet tout a sa place et on attache les bons elements---------->
  if ($("#initialMember").attr("data-autoload-value") == "true") {
    addItemV2();
  } else {
    // Chercher tous les elements ayant data-taret="field"
    let nb = 0;
    $('div[data-target="field"]').each(function () {
      // Remplacer l'attribut 'id'
      const newId = $(this)
        .attr("id")
        .replace("membre-__name__", "membre-" + nb);
      $(this).attr("id", newId);

      // Remplacer l'attribut 'data-index'
      const newIndex = $(this).attr("data-index").replace("__name__", nb);
      $(this).attr("data-index", newIndex);

      $(`.edit-member[data-index="${nb}"]`).on("click", function () {
        $("#membre-0").removeClass("d-none");
        $("#editMemberModal").modal("show");

        //Modifier l'evenement de sauvergarde en edit
        $("#save-member")
          .off("click")
          .on("click", function () {
            editMember($("#membre-0").attr("data-index"));
          });

        // Lier les boutons de fermeture de la modal pour juste fermer la modal sans supprimer les informations du membre
        $('button[data-bs-dismiss="modal"]').each(function () {
          $(this)
            .off("click")
            .on("click", function (e) {
              $("#editMemberModal").modal("hide");
              $("#membre-0").addClass("d-none");
            });
        });
      });

      if (nb > 0) {
        $("#modal-member-form").append($(this).addClass("d-none"));
        saveMember(nb);
      }
      // <---Il reste a change le __name__ par le bon index pour les documents---->
      nb++;
    });
  }

  // Ajouter un nouveau membre
  $("#add-member").on("click", function () {
    addItemV2();

    $('button[data-bs-dismiss="modal"]').each(function () {
      $(this)
        .off("click")
        .on("click", function (e) {
          console.log("la");
          $("#modal-member-form").children().last().remove();

          fieldsTargetDoc.children().last().remove();
          //   index--;
          itemsCountValue--;
          checkItemsCount();
        });
    });
  });

  function saveMember(mumberIndex) {
    const nom = $(`#association_membres_${mumberIndex}_nom`).val();
    const prenom = $(`#association_membres_${mumberIndex}_prenom`).val();

    // Créer une carte preview pour le membre ajouté
    const memberCard = `
              <div class="card mb-4" id="member-card-${mumberIndex}" data-index="${mumberIndex}">
                  <div class="card-body">
                      <h5 class="card-title">Membre ${itemsCountValue}</h5>
                      <p class="card-text">${nom} ${prenom}</p>
                      <button type="button" class="btn btn-primary edit-member" data-index="${mumberIndex}">Éditer</button>
                      <button type="button" class="btn btn-danger delete-membre" data-index="${mumberIndex}">Supprimer</button>
                  </div>
              </div>
          `;

    // Ajouter la carte à la prévisualisation
    $(".member-cards").append(memberCard);
    console.log(mumberIndex);
    // Attacher les événements de suppression
    $(`.delete-membre[data-index="${mumberIndex}"]`).on("click", function (e) {
      removeItem(e);
    });

    // Attacher les événements d'édition
    $(`.edit-member[data-index="${mumberIndex}"]`).on("click", function () {
      $(`#membre-${mumberIndex}`).removeClass("d-none");
      $("#editMemberModal").modal("show");

      //Modifier l'evenement de sauvergarde en edit
      $("#save-member")
        .off("click")
        .on("click", function () {
          editMember(mumberIndex);
        });

      // Lier les boutons de fermeture de la modal pour juste fermer la modal sans supprimer les informations du membre
      $('button[data-bs-dismiss="modal"]').each(function () {
        $(this)
          .off("click")
          .on("click", function (e) {
            $("#editMemberModal").modal("hide");
            console.log("seconde");
            $(`#membre-${mumberIndex}`).addClass("d-none");
          });
      });
    });

    // Fermer la modal
    $("#editMemberModal").modal("hide");

    $(`#membre-${mumberIndex}`).addClass("d-none");
  }

  function editMember(mumberIndex) {
    let nom = $(`#association_membres_${mumberIndex}_nom`).val();
    let prenom = $(`#association_membres_${mumberIndex}_prenom`).val();

    if (nom && prenom) {
      // Afficher la card avec les informations du membre 1
      $(`#member-card-${mumberIndex}`)
        .find(".card-text")
        .text(nom + " " + prenom);
      $("#member-preview-card").show();
    }

    $("#editMemberModal").modal("hide");
    $(`#membre-${mumberIndex}`).addClass("d-none");
  }

  function removeItem(event) {
    let value = $(event.target.closest(".card")).attr("data-index");
    event.target.closest(".card").remove();

    $(`#modal-member-form`)
      .find(".card[data-index=" + value + "]")
      .remove();

    itemsCountValue--;

    if (itemsCountValue == 2) {
      $(".member-cards").find(".card-title").text("Membre 2");
    }
    removeItemDoc(value);
    checkItemsCount();
  }

  // Methode pour verifier le nombre de membres
  function checkItemsCount() {
    if (itemsCountValue >= 3) {
      $("#add-member").hide();
    } else {
      $("#add-member").show();
    }
  }
  //   <---- Fin logique gestion collection ajout des membres ---->

  //   <---- Debut logique gestion collection ajout des documents ---->

  function addItemDoc(indexDoc) {
    const prototypeDoc = JSON.parse(prototypeValueDoc).replace(
      /__name__/g,
      indexDoc
    );

    fieldsTargetDoc.append(prototypeDoc);
  }

  function removeItemDoc(value) {
    $('div[data-target-doc="fieldsDoc"]')
      .find(".cardDoc[data-index-doc=" + value + "]")
      .remove();
  }
});
//   <---- Fin logique gestion collection ajout des documents ---->

// Reste à gérer les messages d'erreur

// <---- Idée pour simplifier mais ne fonctionne pas quelque bug ---->
// $(document).ready(function () {
//   // Navigation entre les étapes
//   let currentStep = 1;

//   $(".next-step").on("click", function () {
//     changeStep(1);
//   });

//   $(".previous-step").on("click", function () {
//     changeStep(-1);
//   });

//   function changeStep(direction) {
//     $("#step-" + currentStep).hide();
//     currentStep += direction;

//     if (currentStep === 4) {
//       updateMemberPreview();
//     } else if (currentStep === 3) {
//       // Réafficher le membre 1 lorsqu'on revient à l'étape 3
//       $('div[data-target="fields"]').append(
//         $("#membre-0").removeClass("d-none")
//       );
//     }

//     $("#step-" + currentStep).show();
//   }

//   function updateMemberPreview() {
//     let fieldTarget2 = $('div[data-target="fields"]')
//       .find(".card")
//       .attr("data-index");
//     let nom = $("#association_membres_" + fieldTarget2 + "_nom").val();
//     let prenom = $("#association_membres_" + fieldTarget2 + "_prenom").val();

//     if (nom && prenom) {
//       $("#member-name").text(nom + " " + prenom);
//       $("#member-preview-card").show();
//       $("#modal-member-form").append(
//         $("#membre-" + fieldTarget2).addClass("d-none")
//       );
//     } else {
//       $("#member-preview-card").hide();
//     }
//   }

//   // Gestion de la collection pour l'ajout des membres
//   const fieldsTarget = $('div[data-target="fields"]');
//   const prototypeValue = $("#initialMember").attr("data-prototype");
//   let index = fieldsTarget.children().length;
//   let itemsCountValue = index;

//   // Ajout dynamique des documents
//   const fieldsTargetDoc = $('div[data-target-doc="fieldsDoc"]');
//   const prototypeValueDoc = $("#initialMemberDoc").attr("data-prototype-doc");

//   // Ajouter un membre
//   $("#add-member").on("click", function () {
//     addMember();
//   });

//   function addMember() {
//     const isFirst = itemsCountValue === 0;
//     const prototype = JSON.parse(prototypeValue).replace(/__name__/g, index);

//     if (isFirst) {
//       fieldsTarget.append(prototype);
//       setupEditEvent(index);
//     } else {
//       $("#modal-member-form").append(prototype);
//       $("#editMemberModal").modal("show");
//       setupSaveEvent($('div[data-target="field"]').last().attr("data-index"));
//     }

//     // Événement pour fermer la modal d'ajout sans sauvegarder
//     $("#editMemberModal").on("hidden.bs.modal", function () {
//       if (isFirst) {
//         $("#modal-member-form").find(".card").last().remove();
//         itemsCountValue--;
//       }
//     });

//     addItemDoc(index);
//     index++;
//     itemsCountValue++;
//     checkItemsCount();
//   }

//   addMember();

//   function setupEditEvent(memberIndex) {
//     $(`.edit-member[data-index="${memberIndex}"]`).on("click", function () {
//       $("#membre-0").removeClass("d-none");
//       $("#editMemberModal").modal("show");
//       setupSaveEvent(memberIndex);
//       closeModalWithoutDeleting(memberIndex);
//     });
//   }

//   function setupSaveEvent(memberIndex) {
//     $("#save-member")
//       .off("click")
//       .on("click", function () {
//         saveMember(memberIndex);
//       });
//   }

//   function closeModalWithoutDeleting(memberIndex) {
//     $('button[data-bs-dismiss="modal"]')
//       .off("click")
//       .on("click", function () {
//         $("#editMemberModal").modal("hide");
//         console.log("premier");
//         // Ne pas supprimer lors de l'édition
//         if (memberIndex !== 0) {
//           $("#membre-0").addClass("d-none");
//         }
//       });
//   }

//   function saveMember(memberIndex) {
//     const nom = $(`#association_membres_${memberIndex}_nom`).val();
//     const prenom = $(`#association_membres_${memberIndex}_prenom`).val();

//     // Mettre à jour les valeurs de la carte de membre existante
//     $(`#member-card-${memberIndex} .card-text`).text(`${nom} ${prenom}`);

//     // Créer une carte preview si c'est un nouveau membre
//     if ($(`#member-card-${memberIndex}`).length === 0) {
//       const memberCard = `
//               <div class="card mb-4" id="member-card-${memberIndex}" data-index="${memberIndex}">
//                   <div class="card-body">
//                       <h5 class="card-title">Membre ${itemsCountValue}</h5>
//                       <p class="card-text">${nom} ${prenom}</p>
//                       <button type="button" class="btn btn-primary edit-member" data-index="${memberIndex}">Éditer</button>
//                       <button type="button" class="btn btn-danger delete-membre" data-index="${memberIndex}">Supprimer</button>
//                   </div>
//               </div>
//           `;

//       $(".member-cards").append(memberCard);
//       attachDeleteEvent(memberIndex);
//       attachEditEvent(memberIndex);
//     }

//     $("#editMemberModal").modal("hide");
//     $(`#membre-${memberIndex}`).addClass("d-none");
//   }

//   function attachDeleteEvent(memberIndex) {
//     $(`.delete-membre[data-index="${memberIndex}"]`).on("click", function (e) {
//       removeItem(e);
//     });
//   }

//   function attachEditEvent(memberIndex) {
//     $(`.edit-member[data-index="${memberIndex}"]`).on("click", function () {
//       $(`#membre-${memberIndex}`).removeClass("d-none");
//       $("#editMemberModal").modal("show");
//       setupSaveEvent(memberIndex);
//       closeModalWithoutDeleting(memberIndex);
//     });
//   }

//   function removeItem(event) {
//     let value = $(event.target.closest(".card")).attr("data-index");
//     event.target.closest(".card").remove();
//     $(`#modal-member-form`)
//       .find(".card[data-index=" + value + "]")
//       .remove();
//     removeItemDoc(value);
//     itemsCountValue--;
//     updateMemberCount();
//     checkItemsCount();
//   }

//   function updateMemberCount() {
//     if (itemsCountValue == 2) {
//       $(".member-cards").find(".card-title").text("Membre 2");
//     }
//   }

//   function checkItemsCount() {
//     $("#add-member").toggle(itemsCountValue < 3);
//   }

//   function addItemDoc(indexDoc) {
//     const prototypeDoc = JSON.parse(prototypeValueDoc).replace(
//       /__name__/g,
//       indexDoc
//     );
//     fieldsTargetDoc.append(prototypeDoc);
//   }

//   function removeItemDoc(value) {
//     $('div[data-target-doc="fields"]')
//       .find(".cardDoc[data-index-doc=" + value + "]")
//       .remove();
//   }
// });
