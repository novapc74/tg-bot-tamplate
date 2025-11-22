<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ meta_title }}</title>
{% app_css %}
</head>
<body>
    {% include 'embed/navbar.php' %}
    <main>
    {% block body %}
    {% end block %}
    </main>
    {% include 'embed/footer.php' %}
    {% app_js %}
</body>
</html>
