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
    'TYPO3/CMS/Backend/Severity',
    'TYPO3/CMS/Backend/Icons',
    'TYPO3/CMS/Backend/Notification'
],
function($, Modal, Severity, Icons, Notification) {
	'use strict';

    /**
     * Abbort an xhr request, but only if it is loaded and not finished already
     *
     * @param {XMLHttpRequest} xhrObject The xhr request to stop
     *
     * @returns {undefined}
     */
    function xhrAbort(xhrObject){
        if( xhrObject !== null && xhrObject.readyState !== 4 ){
            xhrObject.abort();
        }
    }
    
    /**
     * Check if a xhr request is ongoing
     *
     * @param {XMLHttpRequest} xhrObject The xhr request object to check
     *
     * @returns {Boolean}
     */
    function xhrInProgress(xhrObject){
        if( xhrObject !== null && xhrObject.readyState !== 4 ){
            return true;
        }
        return false;
    }

    /**
     * NotesMenu Object that is used to manage the toolbar menu item
     *
     * @type {}
     */
    var NotesMenu = {
        selectors:{
            toolbarContainerSelector: '#thecodingowl-beusernotes-backend-toolbar-usernotetoolbaritem',
            addButtonSelector: '.note-add',
            itemsSelector: '.note-item',
            itemsNewSelector: '.note-new',
            iconSelector: '.dropdown-toggle span.icon'
        },
        elements: {
            toolbarContainer: null,
            addButton: null,
            icon: null,
            spinner: '',
            spinnerDark: ''
        },
        items: null,
        itemsNew: null,
        loading: false,
        xhrObjects: {
            xhrCreate: null,
            xhrDelete: null,
            xhrRead: []
        }
    };
    /**
     * Initialize the events of the NotesMenu
     *
     * @returns {undefined}
     */
    NotesMenu.initializeEvents = function(){
        NotesMenu.elements.toolbarContainer.on('click.add-note', NotesMenu.selectors.addButtonSelector, function(event){
            event.preventDefault();
            NotesMenu.startLoading();
            xhrAbort(NotesMenu.xhrObjects.xhrCreate);
            NotesMenu.xhrObjects.xhrCreate = $.get(TYPO3.settings.ajaxUrls['notes_new']);
            NotesMenu.xhrObjects.xhrCreate.always(function(){
                NotesMenu.finishLoading();
            });
            NotesMenu.xhrObjects.xhrCreate.done(function(data){
                if( data && data.success ){
                    NotesMenu.openCreateModal(data.content);
                } else {
                    NotesMenu.notifyError(data.message);
                }
            });
            NotesMenu.xhrObjects.xhrCreate.fail(function(){
                NotesMenu.notifyError(NotesMenu.xhrObjects.xhrCreate.statusText);
            });
        });
    };
    /**
     * Initialize the NotesMenu
     *
     * @returns {undefined}
     */
    NotesMenu.initialize = function(){
        NotesMenu.elements.toolbarContainer = $(NotesMenu.selectors.toolbarContainerSelector);
        NotesMenu.loadIcons();
        NotesMenu.elements.addButton = NotesMenu.elements.toolbarContainer.find(NotesMenu.selectors.addButtonSelector);
        NotesMenu.items = NotesMenu.elements.toolbarContainer.find(NotesMenu.selectors.itemsSelector);
        NotesMenu.itemsNew = NotesMenu.elements.toolbarContainer.find(NotesMenu.selectors.itemsNewSelector);
        NotesMenu.elements.icon = NotesMenu.elements.toolbarContainer.find(NotesMenu.selectors.iconSelector).clone();
        NotesMenu.initializeEvents();
    };
    /**
     * Use the notification to send an error message to the user
     *
     * @param {type} message
     * @returns {undefined}
     */
    NotesMenu.notifyError = function(message){
        Notification.error(TYPO3.lang['modal.notes.error.title'] || 'Oops an error occured!', message);
    };
    /**
     * Open the modal for the creation of a sys note
     *
     * @param {string} content The content of the modal
     *
     * @returns {undefined}
     */
    NotesMenu.openCreateModal = function(content){
        var buttons = [
            {
                text: TYPO3.lang['button.cancel'] || 'Cancel',
                active: true,
                btnClass: 'btn-default',
                name: 'cancel',
                trigger: function(event){
                    Modal.dismiss();
                }
            },
            {
                text: TYPO3.lang['modal.notes.button.add'] || 'Add',
                active: true,
                btnClass: 'btn-success',
                name: 'create',
                trigger: function(event){
                    Modal.dismiss();
                    NotesMenu.startLoading();
                    NotesMenu.createNote();
                }
            }
        ];
        Modal.confirm(TYPO3.lang['modal.notes.item.add'] || 'Add new note', content, Severity.info, buttons);
    };
    /**
     * Send the note data to the server to create the note
     *
     * @returns {undefined}
     */
    NotesMenu.createNote = function(){
        var $noteForm = $('#tx-beusernotes-form-create'),
            data = $noteForm.serializeArray();
        xhrAbort(NotesMenu.xhrObjects.xhrCreate);
        NotesMenu.xhrObjects.xhrCreate = $.post(TYPO3.settings.ajaxUrls['notes_create'],data);
    };
    /**
     * Load some icons from the server
     *
     * @returns {undefined}
     */
    NotesMenu.loadIcons = function(){
        Icons.getIcon('spinner-circle-light', Icons.sizes.small).done(function(spinner) {
			NotesMenu.elements.spinner = spinner;
		});
        Icons.getIcon('spinner-circle-dark', Icons.sizes.small).done(function(spinner) {
			NotesMenu.elements.spinnerDark = spinner;
		});
    };
    /**
     * Start the loading spinner
     * Only do this, if the spinner isn't already running
     *
     * @returns {undefined}
     */
    NotesMenu.startLoading = function(){
        if( !NotesMenu.loading ){
            NotesMenu.elements.toolbarContainer.find(NotesMenu.selectors.iconSelector).replaceWith(NotesMenu.elements.spinner);
            NotesMenu.loading = true;
        }
    };
    /**
     * Finish the loading spinner
     * Only do this if all xhr requests have been finished
     *
     * @returns {undefined}
     */
    NotesMenu.finishLoading = function(){
        if(
            NotesMenu.loading &&
            !xhrInProgress(NotesMenu.xhrObjects.xhrCreate) &&
            !xhrInProgress(NotesMenu.xhrObjects.xhrDelete) &&
            NotesMenu.xhrObjects.xhrRead.map(xhrInProgress).indexOf(false) === -1
        ){
            NotesMenu.elements.toolbarContainer.find(NotesMenu.selectors.iconSelector).replaceWith(NotesMenu.elements.icon);
            NotesMenu.loading = false;
        }
    };

    $(function(){
        NotesMenu.initialize();
    });

    TYPO3.NotesMenu = NotesMenu;

    return NotesMenu;
});
