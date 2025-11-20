<?php

namespace App\Views;

class AdminView extends AbstractView
{
    /**
     * Рендерит страницу админки с layout
     * @param array $data Данные для шаблона admin.php
     * @return string Полный HTML
     */
    public function renderAdminPage(array $data = []): string
    {
        // Рендерим контент из admin.php
        $content = $this->render('pages/admin.php', $data);

        // Оборачиваем в base_layout.php
        return $this->renderLayout($content, 'base_layout.php', [
            'title' => $data['title'] ?? 'Admin Panel'
        ]);
    }
}
