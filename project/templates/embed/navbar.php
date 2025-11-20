<nav class="my-navbar">
    <div class="my-navbar-container">
        <!-- Логотип/бренд -->
        <a href="/admin" class="my-navbar-brand">⚙ Admin</a>

        <!-- Кнопка бургер-меню для мобильной версии -->
        <button class="my-navbar-toggler" type="button" aria-label="Toggle navigation">
            <span class="my-navbar-toggler-icon"></span>
        </button>

        <!-- Меню -->
        <div class="my-navbar-menu">
            <ul class="my-navbar-nav">
                <li class="my-nav-item">
                    <a href="#" class="my-nav-link active">About</a>
                </li>
                <li class="my-nav-item dropdown">
                    <a href="#" class="my-nav-link dropdown-toggle">Upload <span class="dropdown-arrow">▼</span></a>
                    <ul class="dropdown-menu">
                        <li><a href="/admin/prompt/create" class="dropdown-link first">Prompt</a></li>
                        <li><a href="#" class="dropdown-link">Help</a></li>
                        <li><a href="#" class="dropdown-link last">Commands</a></li>
                    </ul>
                </li>
                <li class="my-nav-item">
                    <a href="#" class="my-nav-link">Contact</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
