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
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Backend\Utility\BackendUtility;

use TheCodingOwl\BeUserNotes\Domain\Model\Note;

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
    
    /**
     * Count the new notes for the be_user
     *
     * @return int
     */
    static public function countNew(): int {
        return self::getDatabaseConnection()->exec_SELECTcountRows(
            'sys_note.uid',
            'sys_note LEFT JOIN sys_note_viewed ON sys_note.uid=sys_note_viewed.sys_note AND sys_note_viewed.be_user=' . (int)self::getBackendUserAuthentication()->user['uid'],
            '(sys_note_viewed.viewed = 0 OR sys_note_viewed.viewed IS NULL) AND ((sys_note.owner = ' . (int)self::getBackendUserAuthentication()->user['uid'] . ') OR (sys_note.personal = 0))' . BackendUtility::BEenableFields('sys_note')
        );
    }

    /**
     * Get the notes
     *
     * @return array
     */
    static public function findAllArray(): array {
        return self::getDatabaseConnection()->exec_SELECTgetRows(
            'sys_note.*, sys_note_viewed.viewed as "viewed"',
            'sys_note LEFT JOIN sys_note_viewed ON sys_note.uid=sys_note_viewed.sys_note AND sys_note_viewed.be_user=' . (int)self::getBackendUserAuthentication()->user['uid'],
            '((sys_note.owner = ' . (int)self::getBackendUserAuthentication()->user['uid'] . ') OR (sys_note.personal = 0))' . BackendUtility::BEenableFields('sys_note')
        );
    }

    /**
     * Set the viewed state for the given note to TRUE
     *
     * @param Note $note The note to set the viewed state for
     *
     * @return bool
     */
    static public function setAsViewed(Note $note): bool {
        $db = self::getDatabaseConnection();
        $viewedRow = self::getViewedRow($note);
        $success = FALSE;
        if( $viewedRow ){
            $success = $db->exec_UPDATEquery('sys_note_viewed', 'sys_note=' . (int)$note->getUid() . ' AND be_user=' . (int)self::getBackendUserAuthentication()->user['uid'], ['viewed' => TRUE]);
        } else {
            $success = $db->exec_INSERTquery('sys_note_viewed', [
                'sys_note' => (int)$note->getUid(),
                'be_user' => (int)self::getBackendUserAuthentication()->user['uid'],
                'viewed' => TRUE
            ]);
        }
        return $success;
    }
    
    /**
     * Get the row fromm the sys_note_viewed table for the given note
     *
     * @param Note $note The note to find the viewed row by
     *
     * @return NULL|array
     */
    static protected function getViewedRow(Note $note){
        return self::getDatabaseConnection()->exec_SELECTgetSingleRow('*', 'sys_note_viewed', 'sys_note=' . (int)$note->getUid() . ' AND be_user=' . (int)self::getBackendUserAuthentication()->user['uid']);
    }
    
    /**
     * Is the given note viewed by the current be user?
     *
     * @param Note $note The note to check the viewed state for
     */
    static public function isViewed(Note $note): bool {
        $isViewed = FALSE;
        $viewedRow = self::getViewedRow($note);
        if( $viewedRow && $viewedRow['viewed'] ){
            $isViewed = TRUE;
        }
        return $isViewed;
    }

    /**
     * Get the database connection
     *
     * @return DatabaseConnection
     */
    static protected function getDatabaseConnection(): DatabaseConnection {
        return $GLOBALS['TYPO3_DB'];
    }
    
    /**
     * Get the BackendUserAuthentication aka BE_USER
     *
     * @return BackendUserAuthentication
     */
    static protected function getBackendUserAuthentication(): BackendUserAuthentication{
        return $GLOBALS['BE_USER'];
    }
}
