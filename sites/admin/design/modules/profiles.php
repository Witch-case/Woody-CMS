<?php

$module->addCssFile('profiles.css');
?>

<h1>Profiles Utilisateurs</h1>

<div class="errorMessages">
    <? foreach( $messages as $message ) { ?>
    <p><?=$message?></p>
    <? } ?>
</div>

<p>
    Voici la liste des profiles utilisateurs utilisés pour gérer les droits
</p>


<h2><?=$count?> Profiles</h2>
<div id="navHeader">
    Afficher
    <? foreach( $limits as $limit => $href ) { ?>
        <? if($href) { ?>
            <a href="<?=$href?>">
                <?=$limit?>
            </a>
        <? } else { ?>
            <span>
                <?=$limit?>
            </span>
        <? } ?>
     <? } ?>
    éléments
</div>

<form method="POST" name="profiles">
    <table id="profiles-navHeader-table">
        <thead>
            <tr>
                <th>&nbsp;
                    
                </th>
                <? foreach( $headers as $header => $href ) { ?>
                    <th>
                        <? if( $href ) { ?>
                            <a href="<?=$href?>">
                                <?=$header?>
                            </a>
                        <? } else { ?>
                            <?=$header?>
                        <? } ?>
                    </th>
                <? } ?>
            </tr>
        </thead>
        <tbody>
            <? foreach( $profiles as $profile ) { ?>
                <tr>
                    <td>
                        <input  type="checkbox" 
                                name="profileIDs[]" 
                                value="<?=$profile->id?>" />
                    </td>
                    <td>
                        <a href="<?=$viewHref.$profile->id?>">
                            <?=$profile->name?>
                        </a>
                    </td>
                    <td>
                        <?=$profile->datetime->frenchFormat(true)?>
                    </td>
                    <td>
                        <a href="<?=$modificationHref.$profile->id?>">
                            Modifier
                        </a>
                    </td>
                </tr>
            <? } ?>
        </tbody>
    </table>
    
    <div class="pagination">
        <ul>
            <? foreach( $pages as $key => $pageLinkData ) { ?>
                <? if( !is_int($key) ) { ?>
                    <li id="first-li-pagination">
                        <a href="<?=$pageLinkData['href']?>">
                            <?=$key?>
                        </a>
                    </li>
                <? } elseif( !$pageLinkData['href'] ) { ?>
                    <li id="second-li-pagination">
                        &nbsp;<?=$pageLinkData['name']?>
                    </li>
                <? } else { ?>
                    <li id="second-li-pagination">
                        <a href="<?=$pageLinkData['href']?>">
                            &nbsp;<?=$pageLinkData['name']?>
                        </a>
                    </li>
                <? } ?>
            <? } ?>
        </ul>
    </div>
    
    <input  type="submit"
            name="createProfile"
            value="Créer Profile" />
    <input  type="submit"
            name="deleteProfiles"
            value="Supprimer Profile" />
</form>