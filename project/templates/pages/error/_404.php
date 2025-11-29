{% extends 'app_base.php' %}

{% block body %}
<div style="text-align: center; padding-top: 30px;">
    <h1 style="font-size: 60px; color: #8B0000">{{ code }}</h1>
    <h2><span style="color: #8B0000">Ошибка: </span>{{ error }}</h2>
</div>

{% end block %}
