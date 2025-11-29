<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Телеграм-бот">
    <title>{{ meta_title }}</title>
    {% app_css %}
</head>
<body>
    <main>
    {% block body %}
    {% end block %}
    </main>
    {% app_js %}
</body>
</html>
