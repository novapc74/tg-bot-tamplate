{% extends 'base.php' %}

{% block body %}
<section class="upload-prompt">
    <h1>Upload Help File</h1>
    <form action="/admin/help/upload" method="POST" enctype="multipart/form-data">
        <label for="help_file">Select a file in <span>*.md</span> format:</label>
        <input type="file" id="help_file" name="help_file" accept=".md" required>

        <input type="hidden" name="csrf_token" value="{{ csrf_token }}">

        <button type="submit">Upload File</button>
    </form>
    <div class="form-sub-menu"><a href="/admin/help/show">Refresh</a> | <a href="/admin/help/download">Download current help</a></div>
</section>
{% end block %}
