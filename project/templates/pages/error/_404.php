{% extends 'base.php' %}

{% block body %}
<div style="text-align: center; padding-top: 30px;">
    <h1 style="font-size: 60px; color: #8B0000">404</h1>
    <h2>Страница не найдена</h2>
    <br>
    <h4><span style="color: #8B0000">Ошибка: </span>{{ error }}</h4>
</div>

{% end block %}
