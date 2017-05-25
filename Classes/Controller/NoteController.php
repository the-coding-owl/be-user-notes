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

use TheCodingOwl\BeUserNotes\Domain\Model\Note;
use TheCodingOwl\BeUserNotes\Domain\Repository\NoteRepository;

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
     * Inject the NoteRepository
     *
     * @param NoteRepository $noteRepository The repository to inject
     */
    public function injectNoteRepository(NoteRepository $noteRepository) {
        $this->noteRepository = $noteRepository;
    }

    /**
     * Action that is used to display a form for new sys_notes
     */
    public function newAction(Note $note = NULL) {
        $this->view->assign('note', $note);
    }

    /**
     * Create a new note
     *
     * @param \TheCodingOwl\BeUserNotes\Domain\Model\Note $note
     */
    public function createAction(Note $note){
        $this->noteRepository->add($note);
    }

}
