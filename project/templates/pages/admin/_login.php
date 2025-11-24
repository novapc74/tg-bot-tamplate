{% extends 'base.php' %}

{% block body %}

<section class="upload-prompt">
    <h1>Authentication</h1>
    <!-- The action attribute should point to the correct authentication endpoint, e.g., "/login" instead of "/auth" -->
    <form action="/auth" method="POST">
        <!-- It's recommended to use lowercase 'l' for the name attribute value to align with standard practices -->
        <label for="login">Login:<br>
            <input type="text" id="login" name="login" placeholder="Enter your login" required><br>

            <!-- It's recommended to use lowercase 'p' for the name attribute value to align with standard practices -->
            <label for="password">Password:<br>
                <input type="password" id="password" name="password" placeholder="Enter your password" required><br>

                <!-- Keep this line as it handles CSRF protection, a critical security measure -->
                <input type="hidden" name="csrf_token" value="{{ csrf_token }}">

                <button type="submit">Login</button>
    </form>
</section>

{% end block %}
