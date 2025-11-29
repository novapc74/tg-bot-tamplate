{% extends 'app_base.php' %}

{% block body %}

<div class="container">
    <div class="raw">
        <div class="col">
            <h1>Home page!</h1>
            <a href="/admin/manual/show">Manual</a>
            <a href="/admin" data-controller="prefetch">admin</a>.
        </div>
        <div data-controller="hello">
            Загрузка Stimulus...
        </div>
    </div>
</div>

{% end block %}
