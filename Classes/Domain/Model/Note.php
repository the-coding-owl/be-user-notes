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

namespace TheCodingOwl\BeUserNotes\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Domain\Model\BackendUser;

/**
 * Note model that will represent a record of sys_note
 *
 * @author Kevin Ditscheid <kevinditscheid@gmail.com>
 */
class Note extends AbstractEntity {
    /**
     * The subject of the note
     *
     * @var string
     */
    protected $subject;

    /**
     * The message of the note
     *
     * @var string
     */
    protected $message;

    /**
     * This is a personal note
     *
     * @var bool
     */
    protected $personal;

    /**
     * The user that this note belongs to
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\BackendUser
     */
    protected $beUser;

    /**
     * The category of the note
     *
     * @var \TheCodingOwl\BeUserNotes\Domain\Model\NoteCategory
     */
    protected $category;
    
    /**
     * The user viewed the note
     * This is a bit of a special one, because the note doesn't need to belong to
     * the user to be viewed by him. On the other hand, there could be other
     * users that can see the note, but did not view it.
     *
     * @var bool
     */
    protected $viewed;

    /**
     * Set the subject
     *
     * @param string $subject The subject to set
     *
     * @return self
     */
    public function setSubject(string $subject): self {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Set the message
     *
     * @param string $message The message to set
     *
     * @return self
     */
    public function setMessage(string $message): self {
        $this->message = $message;
        return $this;
    }

    /**
     * Set personal
     *
     * @param bool $personal TRUE if the Note should be personal, FALSE otherwise
     *
     * @return self
     */
    public function setPersonal(bool $personal): self {
        $this->personal = $personal;
        return $this;
    }

    /**
     * Set the backend user
     *
     * @param BackendUser $beUser The user to set
     *
     * @return self
     */
    public function setBeUser(BackendUser $beUser): self {
        $this->beUser = $beUser;
        return $this;
    }

    /**
     * Set the note category
     *
     * @param NoteCategory $category The category to set
     *
     * @return self
     */
    public function setCategory(NoteCategory $category): self {
        $this->category = $category;
        return $this;
    }

    /**
     * Set viewed
     *
     * @param bool $viewed TRUE if the Note has been viewed, FALSE otherwise
     *
     * @return self
     */
    public function setViewed(bool $viewed): self {
        $this->viewed = $viewed;
        return $this;
    }

    /**
     * Get the subject of the note
     *
     * @return string
     */
    public function getSubject(): string {
        return $this->subject;
    }

    /**
     * Get the message of the note
     *
     * @return string
     */
    public function getMessage(): string {
        return $this->message;
    }

    /**
     * Is the note a personal one
     *
     * @return bool
     */
    public function isPersonal(): bool {
        return $this->personal;
    }

    /**
     * Get the user
     *
     * @return BackendUser
     */
    public function getUser(): BackendUser {
        return $this->user;
    }

    /**
     * Get the category
     *
     * @return NoteCategory
     */
    public function getCategory(): NoteCategory {
        return $this->category;
    }

    /**
     * Is the note viewed by the current user
     *
     * @return bool
     */
    public function isViewed(): bool {
        return $this->viewed;
    }

}
