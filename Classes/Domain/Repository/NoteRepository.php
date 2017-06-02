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

namespace TheCodingOwl\BeUserNotes\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Repository for handling Note Models
 *
 * @author Kevin Ditscheid <kevinditscheid@gmail.com>
 */
class NoteRepository extends Repository{
    /**
     * Persist the changes to the database
     */
    public function persist(){
        $this->persistenceManager->persistAll();
    }
}