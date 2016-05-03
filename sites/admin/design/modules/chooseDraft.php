<?php

$module->addJsFile('chooseDraft.js');
?>

<h1>
    Choisir son brouillon
</h1>

<p>
    Plusieurs brouillons sont en cours, veuilliez choisir parmis ceux ci-dessous ou en créer un à partir du contenu actuellement publié.
</p>

<form name="chooseDraft" id="chooseDraft" method="post" >
    <table>
        <thead>
            <th>
                &nbsp;
            </th>
            <?
                foreach( $draftsColumns as $label )
                {
                    ?><th><?
                    echo $label;
                    ?></th><?
                }
            ?>
        </thead>
        <tbody>
            <? foreach( $drafts as $draft ) { ?>
                <tr>
                    <td>
                        <input  type="radio" 
                                name="editDraftID" 
                                value="<?=$draft['id']?>" />
                    </td>
                    <? foreach( $draft as $value ) { ?>
                        <td>
                            <?=$value?>
                        </td>
                    <? } ?>
                </tr>
            <? } ?>
        </tbody>
    </table>
    <input  type="button" 
            title="Editer le brouillon sélectionné" 
            value="Editer Brouillon" 
            name="editButton" 
            class="button" 
            onclick="javascript: testSelection()"/>
    <input  type="submit" 
            title="Créer un brouillon à partir du contenu publié" 
            value="Nouveau Brouillon" 
            name="newDraftButton" 
            class="button" />
    <input  type="submit" 
            title="Retourner à la visualisation" 
            value="Annuler" 
            name="cancelButton" 
            class="button" />
</form>

