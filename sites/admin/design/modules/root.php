<?php

$module->addCssFile('root.css');
$module->addJsFile('root.js');
?>

<div id="maincontent">
    <h1>
        Tableau de bord [ROOT]
    </h1>
    <div id="maincontentHeader">
        <p><?=$localisation->description?></p>
        <input  type="button"
                name="editRootDescription"
                value="Editer description" 
                onclick="javascript: editRootDescription();"/>
    </div>
    
    <div id="rootDescriptionEdit">
        <form method="post">
            <textarea   name="rootDescription" 
                        id="rootDescription"><?=$localisation->description?></textarea>
            <input  type="submit"
                    name="publishtRootDescription"
                    value="Publier" />
        </form>
    </div>
    
    <div id="leftMainPart">
        <div id="rootNavigation">
            <h2>NAVIGATION</h2>
            <form method="post">
                <div id="rootSubTree">
                    <table id="rootSubTree-table">
                        <thead>
                            <tr>
                                <? foreach( $childrenHeader as $header => $href ) { ?>
                                    <th>
                                        <a href="<?=$href?>">
                                            <?=$header?>
                                        </a>
                                    </th>
                                <? } ?>
                            </tr>
                        </thead>
                        <tbody >
                            <? foreach( $childrenElements as $childrenElement ) { ?>
                                <tr >
                                    <td >
                                        <? if( $childrenElement['href'] ) { ?>
                                            <a href="<?=$childrenElement['href']?>">
                                                <?=$childrenElement['name']?>
                                            </a>
                                        <? } else { ?>
                                            <?=$childrenElement['name']?>
                                        <? } ?>
                                    </td>
                                    <td >
                                        <?=$childrenElement['site']?>
                                    </td>
                                    <td >
                                        <?=$childrenElement['type']?>
                                    </td>
                                    <td >
                                        <input  id="priorities-input" 
                                        		type="number"
                                                name="priorities[<?=$childrenElement['id']?>]" 
                                                value="<?=$childrenElement['priority']?>" />
                                    </td>
                                </tr>
                            <? } ?>
                        </tbody>
                    </table>
                </div>
                <input  id="changerPriorities-input"  
                		type="submit"
						name="changePriorities"
                        value="Changer priorités" />
            </form>
        </div>
        
        <div id="rootActions">
            <h2>Actions</h2>
            <div id="rootActionBox">
                <a href="<?=$createSiteHref?>">
                    <input  type="button"
                            name="rootCreateSite"
                            value="Créer nouveau site" />
                </a>
                <a href="<?=$createModuleHref?>">
                    <input  type="button"
                            name="rootCreateModule"
                            value="Créer nouveau module" />
                </a>
                <a href="<?=$createElementHref?>">
                    <input  type="button"
                            name="createElement"
                            value="Créer élément admin" />
                </a>
            </div>
        </div>
    </div>
    
    <div id="rightMainPart">
        <div id="myDrafts">
            <h2>Mes Brouillons</h2>
            <div id="myDraftsElements">
                <table id="myDraftsElements-table">
                    <thead>
                        <tr>
                            <? foreach( $draftsHeader as $header => $href ) { ?>
                                <th>
                                    <a href="<?=$href?>">
                                        <?=$header?>
                                    </a>
                                </th>
                            <? } ?>
                        </tr>
                    </thead>
                    <tbody >
                        <? foreach( $myDraftElements as $myDraftElement ) { ?>
                            <tr >
                                <td >
                                    <a href="<?=$myDraftElement['href']?>">
                                        <?=$myDraftElement['name']?>
                                    </a>
                                </td>
                                <td >
                                    <?=$myDraftElement['site']?>
                                </td>
                                <td >
                                    <?=$myDraftElement['type']?>
                                </td>
                                <td >
                                    <?=$myDraftElement['modification']?>
                                </td>
                            </tr>
                        <? } ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="respiration">  
        <div id="myContents">
            <h2>Mes derniers Contenus</h2>
            <div id="myContents-rootSubTree">
                <table id="myContents-rootSubTree-table">
                    <thead>
                        <tr>
                            <? foreach( $contentsHeader as $header => $href ) { ?>
                                <th>
                                    <a href="<?=$href?>">
                                        <?=$header?>
                                    </a>
                                </th>
                            <? } ?>
                        </tr>
                    </thead>
                    <tbody >
                        <? foreach( $myContentsElements as $myContentsElement ) { ?>
                            <tr >
                                <td >
                                    <a href="<?=$myContentsElement['href']?>">
                                        <?=$myContentsElement['name']?>
                                    </a>
                                </td>
                                <td >
                                    <?=$myContentsElement['site']?>
                                </td>
                                <td >
                                    <?=$myContentsElement['type']?>
                                </td>
                                <td >
                                    <?=$myContentsElement['modification']?>
                                </td>
                            </tr>
                        <? } ?>
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
</div>