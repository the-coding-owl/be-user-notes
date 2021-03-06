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

namespace TheCodingOwl\BeUserNotes\Controller;

use TYPO3\CMS\Extbase\Domain\Repository\BackendUserRepository;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

use TheCodingOwl\BeUserNotes\Domain\Model\Note;
use TheCodingOwl\BeUserNotes\Domain\Repository\NoteRepository;
use TheCodingOwl\BeUserNotes\Domain\Repository\NoteCategoryRepository;
use TheCodingOwl\BeUserNotes\Property\TypeConverter\NoteConverter;
use TheCodingOwl\BeUserNotes\Property\TypeConverter\NoteCategoryConverter;

/**
 * NoteController
 *
 * @author Kevin Ditscheid <kevinditscheid@gmail.com>
 */
class NoteController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController{

    /**
     * The repository of notes
     *
     * @var \TheCodingOwl\BeUserNotes\Domain\Repository\NoteRepository;
     */
    protected $noteRepository;

    /**
     * The BackendUserRepository
     *
     * @var \TYPO3\CMS\Extbase\Domain\Repository\BackendUserRepository
     */
    protected $backendUserRepository;

    /**
     * The NoteCategoryRepository
     *
     * @var \TheCodingOwl\BeUserNotes\Domain\Repository\NoteCategoryRepository
     */
    protected $noteCategoryRepository;
    
    /**
     * Inject the NoteRepository
     *
     * @param NoteRepository $noteRepository The repository to inject
     */
    public function injectNoteRepository(NoteRepository $noteRepository) {
        $this->noteRepository = $noteRepository;
    }

    /**
     * Inject the BackendUserRepository
     *
     * @param BackendUserRepository $backendUserRepository The repository to inject
     */
    public function injectBackendUserRepository(BackendUserRepository $backendUserRepository){
        $this->backendUserRepository = $backendUserRepository;
    }

    /**
     * Inject the NoteCategoryRepository
     *
     * @param NoteCategoryRepository $noteCategoryRepository The repository to inject
     */
    public function injectNoteCategoryRepository(NoteCategoryRepository $noteCategoryRepository){
        $this->noteCategoryRepository = $noteCategoryRepository;
    }
    
    /**
     * Initialize tha actions and add NoteCategory TypeConverter to the possible note arguments
     */
    public function initializeAction() {
        parent::initializeAction();
        if( $this->arguments->hasArgument('note') ){
            $propertyMappingConfiguration = $this->arguments->getArgument('note')->getPropertyMappingConfiguration();
            $propertyMappingConfiguration->setTypeConverter($this->objectManager->get(NoteConverter::class));
            $propertyMappingConfiguration->forProperty('category')->setTypeConverter($this->objectManager->get(NoteCategoryConverter::class));
        }
    }
    
    /**
     * Action that is used to display a form for new sys_notes
     *
     * @param Note $note The note to use to prefill the new form
     * @param string $target The target of the action, only option is "modal"
     */
    public function newAction(Note $note = NULL, string $target = '') {
        $backendUsers = $this->backendUserRepository->findAll();
        $backendUserToSelect = [];
        foreach($backendUsers as $backendUser){
            if( substr($backendUser->getUsername(), 0, 5) !== '_cli_' ){
                $backendUserToSelect[] = $backendUser;
            }
        }
        $this->view->assign('backendUsers', $backendUserToSelect);
        $noteCategories = $this->noteCategoryRepository->findAll();
        $this->view->assign('noteCategories', $noteCategories);
        $this->view->assign('note', $note);
        if( $target === 'modal' ){
            $this->renderModalContent();
        }
    }

    /**
     * Initialize the create action to add the NoteCategoryConverter
     */
    public function initializeCreateAction(){
        $propertyMappingConfiguration = $this->arguments->getArgument('note')->getPropertyMappingConfiguration();
        $propertyMappingConfiguration->allowProperties('cruser');
        $note = $this->request->getArgument('note');
        $note['cruser'] = $this->getBackendUserAuthentication()->user['uid'];
        $this->request->setArgument('note', $note);
    }
    
    /**
     * Create a new note
     *
     * @param \TheCodingOwl\BeUserNotes\Domain\Model\Note $note
     */
    public function createAction(Note $note){
        $this->noteRepository->add($note);
        try{
            $this->noteRepository->persist();
            $this->view->assign('success', TRUE);
            $this->view->assign('message', LocalizationUtility::translate('action.new.success', $this->request->getControllerExtensionName()));
        } catch (\Exception $ex) {
            $this->view->assign('success', FALSE);
            $this->view->assign('message', $ex->getMessage());
        }
    }

    /**
     * Override the default error action because we want to provide some info what happened
     */
    public function errorAction() {
        $this->view->assign('success', FALSE);
        $validationResults = $this->arguments->getValidationResults();
        $flattenedErrors = $validationResults->forProperty('note')->getFlattenedErrors();
        $noteErrors = [];
        foreach($flattenedErrors as $field => $fieldErrors){
            foreach($fieldErrors as $fieldError){
                $result = LocalizationUtility::translate('error.field.' . $field . '.' . $fieldError->getCode(), $this->request->getControllerExtensionName());
                $noteErrors[] = [
                    'field' => $field,
                    'result' => $result ? $result : (string)$fieldError
                ];
            }
        }
        $this->view->assign('validationResults',$noteErrors);
        if( !empty($noteErrors) ){
            $message = LocalizationUtility::translate('error.validation.message', $this->request->getControllerExtensionName());
        } else {
            $message = LocalizationUtility::translate('error.generic.message', $this->request->getControllerExtensionName());
        }
        $this->view->assign('message',$message);
    }
    
    /**
     * Show a note action
     *
     * @param Note $note The note to show
     * @param string $target The target of the action, only option is "modal"
     */
    public function showAction(Note $note, string $target = ''){
        $this->view->assign('note', $note);
        if( $target === 'modal' ){
            $this->renderModalContent();
        }
    }
    
    /**
     * Initialize the dismiss action ti alter the view format when the target
     * parameter is set to modal
     */
    public function initializeDismissAction(){
        if( $this->request->hasArgument('target') ){
            $target = $this->request->getArgument('target');
            if( $target === 'modal' ){
                $this->request->setFormat('json');
            }
        }
    }
    /**
     * Dismiss the note
     *
     * @param Note $note The note to dismiss
     */
    public function dismissAction(Note $note){
        $note->setViewed(TRUE);
        // call special method of the noterepository to persist the viewed state to the database
        $success = NoteRepository::setAsViewed($note);
        $this->view->assign('success', $success);
        $this->view->assign('message', $success ? LocalizationUtility::translate('action.dismiss.success', $this->request->getControllerExtensionName()) : LocalizationUtility::translate('acton.dismiss.fail', $this->request->getControllerExtensionName()));
    }
    
    /**
     * Render the view as a modals content in JSON form
     */
    protected function renderModalContent(){
        try{
            $content = $this->view->render();
            $success = TRUE;
        } catch (Exception $ex) {
            $success = FALSE;
            $message = $ex->getMessage();
        }
        $this->request->setFormat('json');
        $this->view = $this->resolveView();
        if( $this->view ){
            $this->initializeView($this->view);
        }
        $this->view->assign('success', $success);
        if( !$success ){
            $this->view->assign('message', $message);
        } else {
            $this->view->assign('content', $content);
        }
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
