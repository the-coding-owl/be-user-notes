<?php

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

defined('TYPO3_MODE') or die('Access denied!');

call_user_func(function(){
    if (TYPO3_MODE === 'BE') {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'TheCodingOwl.BeUserNotes',
            'user',
            'notes',
            '',
            [
                'Note' => 'new,create,show,dismiss,edit,remove,update',
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:be_user_notes/ext_icon.svg',
                'labels' => 'LLL:EXT:be_user_notes/Resources/Private/Language/locallang_mod.xlf',
                'configuration' => [
                    'shy' => true
                ]
            ]
        );
        // hide the module because it shall only be accessible via the toolbar icon
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('options.hideModules.user := addToList(BeUserNotesNotes)');
        
        $iconRegistry = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $iconRegistry->registerIcon('be_user_notes_actions-document-select', TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, ['source' => 'EXT:be_user_notes/Resources/Public/Icons/actions-document-select.svg']);
        $iconRegistry->registerIcon('be_user_notes_actions-document-open', TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, ['source' => 'EXT:be_user_notes/Resources/Public/Icons/actions-document-open.svg']);
        $iconRegistry->registerIcon('be_user_notes_actions-delete', TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, ['source' => 'EXT:be_user_notes/Resources/Public/Icons/actions-delete.svg']);
    }
});
