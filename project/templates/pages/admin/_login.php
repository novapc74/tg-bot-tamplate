{% extends 'base.php' %}

{% block body %}

<section class="upload-prompt">
    <h1 class="form-menu">Authentication</h1>
    <form action="/auth" method="POST">

        <label for="login">Login:</label>
        <input type="text" id="login" name="login" placeholder="Enter your login" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>

        <input type="hidden" name="csrf_token" value="{{ csrf_token }}">
        <button type="submit">Login</button>
    </form>
</section>

{% end block %}
