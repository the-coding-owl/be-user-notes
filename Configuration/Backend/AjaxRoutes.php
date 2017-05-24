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
return [
    'notes_new' => [
        'path' => 'notes/new',
        'target' => \TheCodingOwl\BeUserNotes\Controller\NoteController::class . '->newAction'
    ],
    'notes_create' => [
        'path' => 'notes/create',
        'target' => \TheCodingOwl\BeUserNotes\Controller\NoteController::class . '->createAction'
    ]
];