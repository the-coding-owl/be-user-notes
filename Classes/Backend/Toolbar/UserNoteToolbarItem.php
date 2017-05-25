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

namespace TheCodingOwl\BeUserNotes\Backend\Toolbar;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Lang\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * This class provides the toolbar item for the TYPO3 toolbar, that provides
 * the dropdown menu for quick access to the sys_notes assigned to a backend user
 *
 * @author Kevin Ditscheid <kevinditscheid@gmail.com>
 */
class UserNoteToolbarItem implements \TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface{
    /**
     * The BackendUserAuthentication aka BE_USER
     *
     * @var \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected $beUser;

    /**
     * The icon factory
     *
     * @var \TYPO3\CMS\Core\Imaging\IconFactory
     */
    protected $iconFactory;

    /**
     * Constructor of the ToolbarItem
     */
    public function __construct() {
        $this->init();
    }

    /**
     * Initialize the ToolbarItem
     *
     * @return void
     */
    protected function init() {
        $this->beUser = $GLOBALS['BE_USER'];
        $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $pageRenderer = $this->getPageRenderer();
        $pageRenderer->addInlineLanguageLabel('modal.notes.button.add', $this->getLanguageService()->sL('LLL:EXT:be_user_notes/Resources/Private/Language/locallang_notes.xlf:modal.notes.button.add'));
        $pageRenderer->addInlineLanguageLabel('modal.notes.item.add', $this->getLanguageService()->sL('LLL:EXT:be_user_notes/Resources/Private/Language/locallang_notes.xlf:modal.notes.item.add'));
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/BeUserNotes/Toolbar/Notes');

    }

    /**
     * Check if the user is allowed to see the ToolbarItem
     *
     * @return bool
     */
    public function checkAccess(): bool {
        $dataHandler = $this->getDataHandler();
        $dataHandler->start([], []);
        return $dataHandler->checkModifyAccessList('sys_note');
    }

    /**
     * Add additional attributes [unused]
     *
     * @return array
     */
    public function getAdditionalAttributes(): array {
        return [];
    }

    /**
     * Add the dropdown with the sys notes
     *
     * @return string
     */
    public function getDropDown(): string {
        $outNewNotes = '';
        $outNotes = '';
        $notes = $this->getNotes();
        foreach($notes as $note){
            $moduleUrl = BackendUtility::getModuleUrl('record_edit', [ 'sys_note' => [ $note['uid'] => 'edit' ] ]);
            $listItemClass = 'note-item';
            if( $note['viewed'] ){
                $listItemClass .= ' note-new';
            }
            $noteListItem = '<li class="dropdown-item"><a href="' . $moduleUrl . '" data-note="' . $note['uid'] . '" class="' . $listItemClass . '">' . $note['subject'] . '</a></li>';
            if( $note['viewed'] ){
                $outNewNotes .= $noteListItem;
            } else {
                $outNotes .= $noteListItem;
            }
        }
        return '<ul class="dropdown-list">'
            . '<li class="dropdown-header">' . $this->getLanguageService()->sL('LLL:EXT:be_user_notes/Resources/Private/Language/locallang_notes.xlf:toolbar.notes.item.new.title') . '</li>'
            . ( $outNewNotes !== '' ? $outNewNotes : '<li class="dropdown-item">' . $this->getLanguageService()->sL('LLL:EXT:be_user_notes/Resources/Private/Language/locallang_notes.xlf:toolbar.notes.item.new.none') . '</li>' )
            . '<li class="divider" role="separator"></li>'
            . '<li class="dropdown-header">' . $this->getLanguageService()->sL('LLL:EXT:be_user_notes/Resources/Private/Language/locallang_notes.xlf:toolbar.notes.item.title') . '</li>'
            . ( $outNotes !== '' ? $outNotes : '<li class="dropdown-item">' . $this->getLanguageService()->sL('LLL:EXT:be_user_notes/Resources/Private/Language/locallang_notes.xlf:toolbar.notes.item.none') . '</li>' )
            . '<li class="divider" role="separator"></li>'
            . '<li class="dropdown-item"><a class="note-add" href="' . BackendUtility::getModuleUrl('notes_new'). '">' . $this->iconFactory->getIcon('mimetypes-x-sys_note', Icon::SIZE_SMALL, 'overlay-new') . $this->getLanguageService()->sL('LLL:EXT:be_user_notes/Resources/Private/Language/locallang_notes.xlf:toolbar.notes.item.add') . '</a></li>'
            . '</ul>';
    }

    /**
     * Set the position index of the ToolbarItem
     *
     * @return int
     */
    public function getIndex(): int {
        return 50;
    }

    /**
     * Get the ToolbarItem that is shown at the top of the TYPO3 backend
     *
     * @return string
     */
    public function getItem(): string {
        $count = $this->countNewNotes();
        return '<span title="' . $this->getLanguageService()->sL('LLL:EXT:be_user_notes/Resources/Private/Language/locallang_notes.xlf:toolbar.notes.title') . '">'
            . $this->iconFactory->getIcon('mimetypes-x-sys_note', Icon::SIZE_SMALL)
            . ( (int) $count > 0 ? '<span class="badge badge-info" style="display: none;">' . $this->countNewNotes() . '</span>' : '' )
            . '</span>';
    }

    /**
     * Check if the ToolbarItem has a dropdown
     *
     * @return bool
     */
    public function hasDropDown(): bool {
        return TRUE;
    }

    /**
     * Get the DataHandler
     *
     * @return DataHandler
     */
    protected function getDataHandler(): DataHandler {
        return GeneralUtility::makeInstance(DataHandler::class);
    }

    /**
     * Count the new notes for the be_user
     *
     * @return int
     */
    protected function countNewNotes(): int {
        return $this->getDatabaseConnection()->exec_SELECTcountRows(
            'user_sys_note.uid',
            'user_sys_note LEFT JOIN sys_note ON sys_note.uid=user_sys_note.sys_note',
            'user_sys_note.be_user=' . (int)$this->beUser->user['uid'] . ' AND user_sys_note.viewed=0 ' . BackendUtility::BEenableFields('sys_note')
        );
    }

    /**
     * Get the notes
     *
     * @return array
     */
    protected function getNotes(): array {
        return $this->getDatabaseConnection()->exec_SELECTgetRows(
            'sys_note.*, user_sys_note.viewed as "viewed"',
            'user_sys_note LEFT JOIN sys_note ON sys_note.uid=user_sys_note.sys_note',
            'user_sys_note.be_user=' . (int)$this->beUser->user['uid'] . BackendUtility::BEenableFields('sys_note')
        );
    }

    /**
     * Get the database connection
     *
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection(): DatabaseConnection {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * Get the LanguageService
     *
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService(): LanguageService {
        return $GLOBALS['LANG'];
    }

    /**
     * Returns current PageRenderer
     *
     * @return \TYPO3\CMS\Core\Page\PageRenderer
     */
    protected function getPageRenderer(): PageRenderer {
        return GeneralUtility::makeInstance(PageRenderer::class);
    }
}
