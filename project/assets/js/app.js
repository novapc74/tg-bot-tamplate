// Код для бургер-меню (обновлён: fallback для mainContent)
const toggler = document.querySelector('.my-navbar-toggler');
const menu = document.querySelector('.my-navbar-menu');
const mainContent = document.querySelector('main') || document.body; // Fallback на body, если main нет

if (toggler && menu && mainContent) {
    toggler.addEventListener('click', () => {
        const isOpen = menu.classList.toggle('open');
        toggler.classList.toggle('active');

        if (isOpen) {
            const menuHeight = menu.scrollHeight;
            // Сдвигаем контент на 60px (navbar) + menuHeight (открытое меню)
            mainContent.style.paddingTop = (60 + menuHeight) + 'px';
            mainContent.style.transition = 'padding-top 0.3s ease';
        } else {
            // Возвращаем к статическому 60px (из CSS)
            mainContent.style.paddingTop = '60px';
        }
    });

    // Закрытие при клике вне (только если меню открыто)
    document.addEventListener('click', (e) => {
        if (!menu.contains(e.target) && !toggler.contains(e.target)) {
            if (menu.classList.contains('open')) {  // Проверяем, открыто ли меню
                menu.classList.remove('open');
                toggler.classList.remove('active');
                mainContent.style.paddingTop = '60px';  // Устанавливаем только при закрытии
                // Закрываем все открытые dropdown при закрытии меню
                document.querySelectorAll('.dropdown.open').forEach(d => {
                    d.classList.remove('open');
                    d.querySelector('.dropdown-toggle')?.setAttribute('aria-expanded', 'false');
                });
            }
            // Если меню уже закрыто, ничего не делаем — контент использует CSS-паддинг
        }
    });
}

// Код для dropdown (обновлён: только click, закрытие при клике на ссылки, stopPropagation)
const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

dropdownToggles.forEach(toggle => {
    // Функция для toggle
    const toggleDropdown = (e) => {
        e.preventDefault(); // Предотвращает переход по ссылке
        e.stopPropagation(); // Предотвращает закрытие бургер-меню
        const dropdown = toggle.closest('.dropdown');
        const isOpen = dropdown.classList.toggle('open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    };

    // Для всех устройств: click (работает на touch и mouse)
    toggle.addEventListener('click', toggleDropdown);

    // Для десктопа: hover (только если ширина >= 768px)
    toggle.addEventListener('mouseenter', () => {
        if (window.innerWidth >= 768) {
            const dropdown = toggle.closest('.dropdown');
            dropdown.classList.add('open');
            toggle.setAttribute('aria-expanded', 'true');
        }
    });

    toggle.closest('.dropdown').addEventListener('mouseleave', () => {
        if (window.innerWidth >= 768) {
            const dropdown = toggle.closest('.dropdown');
            dropdown.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
});

// Закрытие всех dropdown при клике вне (только на десктопе)
document.addEventListener('click', (e) => {
    if (window.innerWidth >= 768 && !e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown.open').forEach(d => {
            d.classList.remove('open');
            d.querySelector('.dropdown-toggle').setAttribute('aria-expanded', 'false');
        });
    }
});

// Закрытие dropdown при клике на ссылки внутри него (для всех устройств)
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('dropdown-link')) {
        document.querySelectorAll('.dropdown.open').forEach(d => {
            d.classList.remove('open');
            d.querySelector('.dropdown-toggle').setAttribute('aria-expanded', 'false');
        });
    }
});

// Дополнительно: если меню закрывается, закрываем dropdown (уже добавлено выше)
