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
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Lang\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

use TheCodingOwl\BeUserNotes\Domain\Repository\NoteRepository;

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
     * Recurring part of the locallang string
     *
     * @var string 
     */
    protected $ll = 'LLL:EXT:be_user_notes/Resources/Private/Language/locallang_notes.xlf:';
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
        $pageRenderer->addInlineLanguageLabel('modal.notes.button.add', $this->getLanguageService()->sL($this->ll . 'modal.notes.button.add'));
        $pageRenderer->addInlineLanguageLabel('modal.notes.item.add', $this->getLanguageService()->sL($this-> ll . 'modal.notes.item.add'));
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/BeUserNotes/Toolbar/Notes');
        $pageRenderer->addCssFile(ExtensionManagementUtility::extRelPath('be_user_notes') . 'Resources/Public/Css/Notes.css');
        
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
        $notes = NoteRepository::findAllArray();
        foreach($notes as $note){
            $moduleUrl = BackendUtility::getModuleUrl('user_BeUserNotesNotes', [ 'tx_beusernotes_user_beusernotesnotes[action]' => 'show', 'tx_beusernotes_user_beusernotesnotes[note]' => $note['uid'] ]);
            $listItemClass = 'note-item';
            $actions = '';
            if( !$note['viewed'] ){
                $actions .= '<a class="note-dismiss" title="' . $this->getLanguageService()->sL($this->ll . 'toolbar.notes.item.new.dismiss') . '" href="' . BackendUtility::getModuleUrl('user_BeUserNotesNotes', [ 'tx_beusernotes_user_beusernotesnotes[action]' => 'dismiss', 'tx_beusernotes_user_beusernotesnotes[note]' => $note['uid'] ]) . '">' . $this->iconFactory->getIcon('be_user_notes_actions-document-select', Icon::SIZE_SMALL) . '</a>';
                $listItemClass .= ' note-new';
            }
            if( 
                (
                    $note['cruser'] === $this->getBackendUserAuthentication()->user['uid'] &&
                    $note['owner'] === 0
                ) || 
                (
                    $note['owner'] === $this->getBackendUserAuthentication()->user['uid'] 
                )
            ){
                $actions .= '<a class="note-edit" title="' . $this->getLanguageService()->sL($this->ll . 'toolbar.notes.item.edit') . '" href="' . BackendUtility::getModuleUrl('user_BeUserNotesNotes', [ 'tx_beusernotes_user_beusernotesnotes[action]' => 'edit', 'tx_beusernotes_user_beusernotesnotes[note]' => $note['uid'] ]) . '">' . $this->iconFactory->getIcon('be_user_notes_actions-document-open', Icon::SIZE_SMALL) . '</a>';
            }
            if(
                $note['personal'] === 1 &&
                (
                    (
                        $note['cruser'] === $this->getBackendUserAuthentication()->user['uid'] &&
                        $note['owner'] === 0
                    ) || 
                    $note['owner'] === $this->getBackendUserAuthentication()->user['uid']
                )
            ){
                $actions .= '<a class="note-remove" title="' . $this->getLanguageService()->sL($this->ll . 'toolbar.notes.item.remove') . '" href="' . BackendUtility::getModuleUrl('user_BeUserNotesNotes', [ 'tx_beusernotes_user_beusernotesnotes[action]' => 'remove', 'tx_beusernotes_user_beusernotesnotes[note]' => $note['uid'] ]) . '">' . $this->iconFactory->getIcon('be_user_notes_actions-delete', Icon::SIZE_SMALL) . '</a>';
            }
            $noteListItem = '<li class="dropdown-item"><a href="' . $moduleUrl . '" data-note="' . $note['uid'] . '" class="' . $listItemClass . '">' . $this->iconFactory->getIcon('mimetypes-x-sys_note', Icon::SIZE_SMALL) . $note['subject'] . '</a><span class="note-actions">' . $actions . '</span></li>';
            if( !$note['viewed'] ){
                $outNewNotes .= $noteListItem;
            } else {
                $outNotes .= $noteListItem;
            }
        }
        return '<ul class="dropdown-list">'
            . '<li class="dropdown-header new-header">' . $this->getLanguageService()->sL($this->ll . 'toolbar.notes.item.new.title') . '</li>'
            . ( $outNewNotes !== '' ? $outNewNotes : '<li class="dropdown-item">' . $this->getLanguageService()->sL($this->ll . 'toolbar.notes.item.new.none') . '</li>' )
            . '<li class="divider" role="separator"></li>'
            . '<li class="dropdown-header read-header">' . $this->getLanguageService()->sL($this->ll . 'toolbar.notes.item.title') . '</li>'
            . ( $outNotes !== '' ? $outNotes : '<li class="dropdown-item">' . $this->getLanguageService()->sL($this->ll . 'toolbar.notes.item.none') . '</li>' )
            . '<li class="divider" role="separator"></li>'
            . '<li class="dropdown-item"><a class="note-add" href="' . BackendUtility::getModuleUrl('user_BeUserNotesNotes'). '">' . $this->iconFactory->getIcon('mimetypes-x-sys_note', Icon::SIZE_SMALL, 'overlay-new') . $this->getLanguageService()->sL($this->ll . 'toolbar.notes.item.add') . '</a></li>'
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
        $count = NoteRepository::countNew();
        return '<span title="' . $this->getLanguageService()->sL($this->ll . 'toolbar.notes.title') . '">'
            . $this->iconFactory->getIcon('mimetypes-x-sys_note', Icon::SIZE_SMALL)
            . ( (int) $count > 0 ? '<span class="badge badge-info">' . $count . '</span>' : '' )
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
    
    /**
     * Get the BackendUserAuthentication aka BE_USER
     *
     * @return BackendUserAuthentication
     */
    protected function getBackendUserAuthentication(): BackendUserAuthentication{
        return $GLOBALS['BE_USER'];
    }
}
