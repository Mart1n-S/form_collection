function toggleFields() {
  const fields = [
    { id: "#sirenNumber", required: true },
    { id: "#structureCreateAt", required: true },
    { id: "#expectedCreationDate", required: false },
  ];

  const isYesChecked =
    $('input[name="customer_form[structureCreated]"]:checked').val() === "1";

  $.each(fields, function (_, field) {
    const element = $(field.id);
    const shouldBeRequired = isYesChecked ? field.required : !field.required;

    element.find("label").toggleClass("required", shouldBeRequired);
    element.toggleClass("d-none", !shouldBeRequired);
    element
      .find("input")
      .attr("required", shouldBeRequired)
      .val(shouldBeRequired ? element.find("input").val() : "")
      .toggleClass("is-invalid", false);
  });
}

function toggleFields() {
  const fields = [
    { id: "#sirenNumber", required: true },
    { id: "#structureCreateAt", required: true },
    { id: "#expectedCreationDate", required: false },
  ];

  const isYesChecked =
    $('input[name="customer_form[structureCreated]"]:checked').val() === "1";

  fields.forEach((field) => {
    const element = $(field.id);
    const shouldBeRequired = isYesChecked ? field.required : !field.required;

    element.find("label").toggleClass("required", shouldBeRequired);
    element.toggleClass("d-none", !shouldBeRequired);
    element
      .find("input")
      .attr("required", shouldBeRequired)
      .val(shouldBeRequired ? element.find("input").val() : "")
      .toggleClass("is-invalid", false);
  });
}
