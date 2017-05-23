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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Lang\LanguageService;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * NoteController
 *
 * @author Kevin Ditscheid <kevinditscheid@gmail.com>
 */
class NoteController {

    /**
     * The view object
     *
     * @var \TYPO3\CMS\Fluid\View\StandaloneView
     */
    protected $view;

    /**
     * The locallang path
     *
     * @var string
     */
    protected $ll = 'EXT:be_user_notes/Resources/Private/Language/locallang_notes.xlf:';

    /**
     * Constructor of the NoteController
     */
    public function __construct() {
        $this->initializeView();

    }

    /**
     * Initialize the view object of the controller
     */
    public function initializeView() {
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $this->view->setFormat('html');
        $this->view->setLayoutRootPaths([ 'EXT:be_user_notes/Resources/Private/Layouts/' ]);
        $this->view->setPartialRootPaths([ 'EXT:be_user_notes/Resources/Private/Partials/' ]);
        $this->view->setTemplateRootPaths([ 'EXT:be_user_notes/Resources/Private/Templates/' ]);
    }

    /**
     * Action that is used to display a form for new sys_notes
     *
     * @return string
     */
    public function newAction(RequestInterface $request, ResponseInterface $response): ResponseInterface {
        $this->view->setTemplate('New');
        $response->getBody()->write($this->view->render());
        return $response;
    }

    /**
     * Create a new note
     *
     * @param array $note
     */
    public function createAction(RequestInterface $request, ResponseInterface $response){
        $this->view->setFormat('json');
        $this->view->setTemplate('Create');

        $parsedBody = $request->getParsedBody();
        $queryParams = $request->getQueryParams();
        $note = (isset($parsedBody['note']) ? $parsedBody['note'] : $queryParams['note']);
        $status = $this->processNote($note);
        $this->view->assign('status', $status);
        $response->getBody()->write($this->view->render());
        return $response;
    }

    protected function processNote(array $note){
        $status = [
            'success' => FALSE,
            'validation' => [],
            'error' => []
        ];
        if( empty($note['subject']) ){
            $status['validation']['subject'] = [ 'message' => $this->getLanguageService()->sL($this->ll . 'action.create.validation.subject.empty') ];
        } elseif( $this->checkLength($note['subject'], 'subject') ){
            $status['validation']['subject'] = [ 'message' => $this->getLanguageService()->sL($this->ll . 'action.create.validation.subject.maxlength'), 'arguments' => [ $this->getMaxLength('subject') ] ];
        } else {
            $db = $this->getDatabaseConnection();
            $time = time();
            $insertArray = [
                'subject' => $db->fullQuoteStr($note['subject'], 'sys_note'),
                'message' => $db->fullQuoteStr($note['message'], 'sys_note'),
                'tstamp' => $time,
                'crdate' => $time,
                'cruser' => $this->getBackendUserAuthentication()->user['uid'],
                'personal' => (bool)$note['personal'],
                'pid' => (int)$note['pid']
            ];
            $db->exec_INSERTquery('sys_note', $insertArray);
        }

        return $status;
    }

    /**
     * Check the length of the given value against the given fieldname
     *
     * @param string $value The value to check
     * @param string $field The field to check against
     *
     * @return bool
     */
    protected function checkLength(string $value, string $field): bool {
        $maxLength = $this->getMaxLength($field);
        if( $maxLength && strlen($value) > $maxLength ){
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Get the maxlength of the given field
     *
     * @param string $field The field to fetch the max configuration from
     *
     * @return int
     */
    protected function getMaxLength(string $field): int {
        return (int)$GLOBALS['TCA']['sys_note']['columns'][$field]['config']['max'];
    }

    /**
     * Get the LanguageService aka $GLOBALS['LANG']
     *
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService(): LanguageService {
        return $GLOBALS['LANG'];
    }

    /**
     * Get the DatabaseConnection
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection(): DatabaseConnection {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * Get the BackendUserAuthentication object aka $GLOBALS['BE_USER']
     *
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication(): BackendUserAuthentication {
        return $GLOBALS['BE_USER'];
    }
}
