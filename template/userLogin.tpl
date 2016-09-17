<form method="post" action="">
    <input type="hidden" name="csrfToken" value="{$csrfToken}">
    <label>Uživatelské jméno
        <input name="user_name" type="text" maxlength="100" value="{$user_name}" placeholder="Uživatelské jméno">
    </label>
    <label>Uživatelské heslo
        <input name="user_password" type="password" value="" placeholder="Uživatelské heslo">
    </label>
    <input type="submit" name="submit" value="Přihlásit">
</form>