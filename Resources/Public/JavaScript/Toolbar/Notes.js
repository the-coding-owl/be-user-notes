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

/**
 * Module: TYPO3/CMS/BeUserNotes/Toolbar/Notes
 * toolbar menu for the workspaces functionality to switch between the workspaces
 * and jump to the workspaces module
 */
define([
    'jquery',
    'TYPO3/CMS/Backend/Modal',
    'TYPO3/CMS/Backend/Severity'
],
function($, Modal, Severity) {
	'use strict';
    var NotesMenu = {
        toolbarContainerSelector: '#thecondingowl-beusernotes-backend-toolbar-usernotetoolbaritem',
        addButtonSelector: '.note-add',
        itemsSelector: '.note-item',
        itemsNewSelector: '.note-new',
        toolbarContainer: null,
        addButton: null,
        items: null,
        itemsNew: null
    };
    NotesMenu.initializeEvents = function(){
        NotesMenu.toolbarContainer.on('click.add-note', NotesMenu.addButtonSelector, function(event){
            event.preventDefault();
            var content = '',
                buttons = [
                    {
                        text: TYPO3.lang['button.cancel'] || 'Cancel',
                        active: true,
                        btnClass: 'btn-default',
                        name: 'cancel'
                    },
                    {
                        text: TYPO3.lang['modal.notes.button.add'] || 'Add',
                        active: true,
                        btnClass: 'btn-success',
                        name: 'create'
                    }
                ];
            Modal.confirm(TYPO3.lang['modal.notes.item.add'] || 'Add new note', content, Severity.info, buttons);
        });
    };
    NotesMenu.initialize = function(){
        NotesMenu.toolbarContainer = $(NotesMenu.toolbarContainerSelector);
        NotesMenu.addButton = NotesMenu.toolbarContainer.find(NotesMenu.addButtonSelector);
        NotesMenu.items = NotesMenu.toolbarContainer.find(NotesMenu.itemsSelector);
        NotesMenu.itemsNew = NotesMenu.toolbarContainer.find(NotesMenu.itemsNewSelector);
        NotesMenu.initializeEvents();

    };

    $(function(){
        NotesMenu.initialize();
    });

    TYPO3.NotesMenu = NotesMenu;

    return NotesMenu;
});
