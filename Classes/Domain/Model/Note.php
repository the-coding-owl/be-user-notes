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
     * @validate NotEmpty
     */
    protected $subject;

    /**
     * The message of the note
     *
     * @var string
     */
    protected $message = '';

    /**
     * This is a personal note
     *
     * @var bool
     */
    protected $personal = FALSE;
    
    /**
     * The user who creates the Note
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\BackendUser
     */
    protected $cruser;

    /**
     * The user who ownes the Note
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\BackendUser
     */
    protected $owner;

    /**
     * The category of the note
     *
     * @var \TheCodingOwl\BeUserNotes\Domain\Model\NoteCategory
     */
    protected $category;
    
    /**
     * The viewed state of the note
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
    public function setMessage(string $message = ''): self {
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
    public function setPersonal(bool $personal = FALSE): self {
        $this->personal = $personal;
        return $this;
    }

    /**
     * Set the cruser
     *
     * @param BackendUser $cruser The user to set
     *
     * @return self
     */
    public function setCruser(BackendUser $cruser): self {
        $this->cruser = $cruser;
        return $this;
    }

    /**
     * Set the osner of the note
     *
     * @param BackendUser $owner The user to set
     *
     * @return self
     */
    public function setOwner(BackendUser $owner = NULL): self {
        $this->owner = $owner;
        return $this;
    }

    /**
     * Set the note category
     *
     * @param NoteCategory $category The category to set
     *
     * @return self
     */
    public function setCategory(NoteCategory $category = NULL): self {
        $this->category = $category;
        return $this;
    }

    /**
     * Set the viewed state of the note
     *
     * @param bool $viewed TRUE or FALSE for the state
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
     * Get the cruser
     *
     * @return BackendUser
     */
    public function getCruser(): BackendUser {
        return $this->cruser;
    }

    /**
     * Get the owner
     *
     * @return NULL|BackendUser
     */
    public function getOwner() {
        return $this->owner;
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
     * Get the viewed state of the note
     *
     * @return bool
     */
    public function getViewed(): bool {
        if( $this->viewed === NULL ){
            \TheCodingOwl\BeUserNotes\Domain\Repository\NoteRepository::isViewed($this);
        }
        return $this->viewed;
    }
}
