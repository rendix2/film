<form method="post" action="">

    <label>Uživatelské jméno
        <input name="user_name" type="text" maxlength="100" value="{$user_name}" placeholder="Uživatelské jméno">
    </label>
    <label>Uživatelské heslo
        <input name="user_password" type="password" value="" placeholder="Uživatelské heslo">
    </label>
    <label>Uživatelské heslo pro kontrolu
        <input name="user_password_check" type="password" value="" placeholder="Uživatelské heslo pro kontrolu">
    </label>
    <input type="submit" name="submit" value="Registrovat">

</form>