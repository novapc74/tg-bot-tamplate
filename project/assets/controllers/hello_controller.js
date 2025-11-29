import { Controller } from "@hotwired/stimulus";

export default class HelloController extends Controller{
    // Метод connect() вызывается, когда контроллер привязывается к DOM-элементу
    connect() {
        this.element.textContent = "Привет, мир! Я Stimulus контроллер.";
    }
}
