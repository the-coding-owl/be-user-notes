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

if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'TheCodingOwl.BeUserNotes',
        'user',
        'notes',
        '',
        [
            'Note' => 'new,create',
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
}