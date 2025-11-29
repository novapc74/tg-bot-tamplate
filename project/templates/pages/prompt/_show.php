{% extends 'layout/admin.php' %}

{% block body %}
<h1 style="padding-top: 40px">Prompt file content:</h1>
<div style="background-color: darkgrey; padding: 20px; border-radius: 15px">
    <pre>{{ file_content }}</pre>
</div>
{% end block %}
