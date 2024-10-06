import { Controller } from "@hotwired/stimulus";
// import { useDispatch } from "stimulus-use";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static targets = ["fields", "field", "addButton"];

  static values = {
    prototype: String,
    maxItems: Number,
    itemsCount: Number,
    autoload: Boolean,
  };

  connect() {
    this.index = this.itemsCountValue = this.fieldTargets.length;
    if (this.autoloadValue) {
      this.addItem();
    }
  }

  addItem() {
    const isFirst = this.itemsCountValue === 0;
    let prototype = JSON.parse(this.prototypeValue);
    const newField = prototype.replace(/__name__/g, this.index);
    this.fieldsTarget.insertAdjacentHTML("beforeend", newField);

    this.index++;
    this.itemsCountValue++;
  }

  removeItem(event) {
    this.fieldTargets.forEach((element, i) => {
      if (element.contains(event.target)) {
        element.remove();
        this.itemsCountValue--;
        this.index--;
      }
    });
  }

  itemsCountValueChanged() {
    if (false === this.hasAddButtonTarget || 0 === this.maxItemsValue) {
      return;
    }
    const maxItemsReached = this.itemsCountValue >= this.maxItemsValue;
    this.addButtonTarget.classList.toggle("d-none", maxItemsReached);
  }
}
