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
    $ll = 'LLL:EXT:be_user_notes/Resources/Private/Language/locallang_db.xlf:';
    TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_note', [
        'be_user' => [
            'label' => $ll . 'sys_note.be_user',
            'exclude' => 1,
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'be_users',
                'maxitems' => 1,
                'minitems' => 1
            ]
        ]
    ]);
});