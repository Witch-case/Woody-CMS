<?php

$module->addCssFile('structures.css');
?>

<h1>Structures des données</h1>

<div class="errorMessages">
    <? foreach( $messages as $message ) { ?>
    <p><?=$message?></p>
    <? } ?>
</div>

<p>
    Les données sont stockées sous la forme de structures qui sont éditables ici.
</p>

<div class="respiration">
    <form method="POST" name="structures">
        <div id="navHeader">
        <h2><?=$count?> Structures</h2>
        </div>
        <? if( $archiveHref ) { ?>
            <a id="structures-navHeader-a" 
                href="<?=$archiveHref["href"]?>">
                <?=$archiveHref["name"]?>
            </a>
        <? } ?>


        <table id="structures-navHeader-table">
            <thead>
                <tr>
                    <th>&nbsp;

                    </th>
                    <? foreach( $headers as $header => $href ) { ?>
                        <th>
                            <? if( $href ) { ?>
                                    <?=$header?>
                                <a href="<?=$href?>">
                                    <div class="triangle"></div>
                                </a>
                            <? } else { ?>
                                <?=$header?>
                            <? } ?>
                        </th>
                    <? } ?>
                </tr>
            </thead>
            <tbody>
                <? foreach( $structures as $structure ) { ?>
                    <tr>
                        <td>
                                <input  type="checkbox" 
                                        name="structures[]" 
                                        <? if( !$structure['modifyHref'] ) { ?>
                                            disabled="disabled"
                                        <? } ?>
                                        value="<?=$structure['name']?>" />
                        </td>
                        <td>
                            <a href="<?=$structure['viewHref']?>">
                                <?=$structure['name']?>
                            </a>
                        </td>
                        <?  if($archives) { ?>
                            <td align="center">
                                <?=$structure['isArchive']?>
                            </td>
                        <? } ?>
                        <td align="center">
                            <?=$structure['draftCount']?>
                        </td>
                        <td align="center">
                            <?=$structure['contentCount']?>
                        </td>
                        <td align="center">
                            <?=$structure['archiveCount']?>
                        </td>
                        <td>
                            <?=$structure['creation']->frenchFormat(true)?>
                        </td>
                        <? if( $structure['modifyHref'] ) { ?>
                            <td>
                                <a href="<?=$structure['modifyHref']?>">
                                    Modifier
                                </a>
                            </td>
                        <? } else {?>
                            <td>
                                <a href="<?=$structure['viewHref']?>">
                                    Voir
                                </a>
                            </td>
                        <? } ?>
                    </tr>
                <? } ?>
            </tbody>
        </table>

        <input  type="submit"
                name="createStructure"
                value="Créer Structure" />
        <input  type="submit"
                name="deleteStructures"
                value="Supprimer Structures" />
    </form>
</div>