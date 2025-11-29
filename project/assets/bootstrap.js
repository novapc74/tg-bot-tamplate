import {Application} from "@hotwired/stimulus";

// 1. Инициализируем приложение Stimulus
const application = Application.start();

// Регистрация контроллера в приложении
// import HelloController from "./controllers/hello_controller"
// application.register('hello', HelloController);

// Функция для автоматической регистрации контроллеров
const registerControllers = async () => {
    const context = import.meta.glob('./controllers/**/*.js');
    for (const path in context) {
        if (path.includes('_controller.js')) {
            const controllerName = path.match(/\/([^\/]+)_controller\.js$/)[1];
            const module = await context[path]();
            application.register(controllerName, module.default);
        }
    }
};

// Регистрация контроллеров из директории /controllers
registerControllers();

export default application;
