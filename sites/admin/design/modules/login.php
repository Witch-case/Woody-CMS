<?php

?>

<h1>Identification</h1>

<div class="errorMessages">
    <? foreach( $user->loginMessages as $message ) { ?>
        <p><?=$message?></p>
    <? } ?>
</div>

<p>
    Vous devez vous identifier pour accéder à cette page.
</p>

<p>
    <form method="POST">
        <input type="hidden" name="login" value="login" />
        
        <p>
            <h2>Nom d'utilisateur ou email</h2>
            <input type="text" name="username" />
        </p>
        <p>
            <h2>Mot de passe</h2>
            <input type="password" name="password" />
            <input type="submit" name="submit" value="Envoyer" />
        </p>
        
    </form>
</p>