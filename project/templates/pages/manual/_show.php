{% extends 'layout/admin.php' %}

{% block body %}
<h1>Help file content:</h1>
<div>
    <pre>{{ file_content }}</pre>
</div>
{% end block %}
