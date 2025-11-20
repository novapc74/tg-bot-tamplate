{% extends 'base.php' %}

{% block body %}

<section class="upload-prompt">
    <h1>Upload Prompt File</h1>
    <form action="/admin/prompt/upload" method="POST" enctype="multipart/form-data">
        <label for="prompt_file">Select a file in <span>JSON</span> or <span>TXT</span> format:</label><br>
        <input type="file" id="prompt_file" name="prompt_file" accept=".json,.txt" required><br>

        <input type="hidden" name="csrf_token" value="{{ csrf_token }}">

        <button type="submit">Upload File</button>
    </form>
    <div class="form-sub-menu"><a href="/admin/prompt/show">Refresh</a> | <a href="/admin/prompt/download">Download current prompt</a></div>
</section>

{% end block %}
