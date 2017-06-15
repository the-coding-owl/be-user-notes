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

use TYPO3\CMS\Extbase\Persistence\Generic\Exception\NotImplementedException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Database\DatabaseConnection;

use TheCodingOwl\BeUserNotes\Domain\Model\NoteCategory;
use TheCodingOwl\BeUserNotes\Domain\Model\Note;

/**
 * Repository class for note categories
 * Please be aware that the note categories are not actually stored in the database
 * therefore there is no adding, updating or removing of categories and no persisting
 *
 * @author Kevin Ditscheid <kevinditscheid@gmail.com>
 */
class NoteCategoryRepository {
    /**
     * Find all NoteCategories
     *
     * @return array<\TheCodingOwl\BeUserNotes\Domain\Model\NoteCategory>
     * @throws \RuntimeException
     */
    public function findAll(){
        if( isset($GLOBALS['TCA']['sys_note']['columns']['category']['config']['items']) ){
            $categoryModels = [];
            foreach( $GLOBALS['TCA']['sys_note']['columns']['category']['config']['items'] as $category ){
                $categoryModel = GeneralUtility::makeInstance(NoteCategory::class);
                $categoryModel->setValue($category[1]);
                $label = LocalizationUtility::translate($category[0],'SysNote');
                if( $label === NULL ){
                    $label = '';
                }
                $categoryModel->setLabel($label);
                $categoryModel->setIcon($category[2]);
                $categoryModels[] = $categoryModel;
            }
            return $categoryModels;
        } else {
            throw new \RuntimeException('The TCA configuration for the category items of table sys_note is not loaded!');
        }
    }
    
    /**
     * Find a NoteCategory by its identifier
     *
     * @param int $identifier The identifier of a NoteCategory
     *
     * @return NULL|\TheCodingOwl\BeUserNotes\Domain\Model\NoteCategory
     * @throws \RuntimeException
     */
    public function findByIdentifier(int $identifier){
        if( isset($GLOBALS['TCA']['sys_note']['columns']['category']['config']['items']) ){
            $categoryIdentifiers = array_column($GLOBALS['TCA']['sys_note']['columns']['category']['config']['items'], 1);
            $categoryConfigIndex = array_search($identifier, $categoryIdentifiers);
            if( $categoryConfigIndex === FALSE ){
                return NULL;
            }
            $category = $GLOBALS['TCA']['sys_note']['columns']['category']['config']['items'][$categoryConfigIndex];
            $categoryModel = GeneralUtility::makeInstance(NoteCategory::class);
            $categoryModel->setValue($category[1]);
            $label = LocalizationUtility::translate($category[0],'SysNote');
            if( $label === NULL ){
                $label = '';
            }
            $categoryModel->setLabel($label);
            $categoryModel->setIcon($category[2]);
            return $categoryModel;
        } else {
            throw new \RuntimeException('The TCA configuration for the category items of table sys_note is not loaded!');
        }
    }
    
    /**
     * Find a NoteCategory by its identifier
     * This simply is an alias for the findByIdentifier method
     *
     * @param int $uid The uid/identifier of the NoteCategory
     *
     * @return \TheCodingOwl\BeUserNotes\Domain\Model\NoteCategory
     * @throws \RuntimeException
     */
    public function findByUid(int $uid){
        return $this->findByIdentifier($uid);
    }

    /**
     * \TheCodingOwl\BeUserNotes\Domain\Model\NoteCategory
     * 
     * @param int|string|\TheCodingOwl\BeUserNotes\Domain\Model\Note $note The note to find the category for
     *
     * @return   \TheCodingOwl\BeUserNotes\Domain\Model\NoteCategory
     * @throws \TypeError
     */
    public function findByNote($note){
        if( $note instanceof Note ){
            $note = $note->getUid();
        } elseif( is_int($note) || is_string ($note) ){
            $note = (int) $note;
        } else{
            throw new \TypeError('This method can not handle a note parameter of type \"' . gettype($note) . '\"!');
        }
        $db = $this->getDatabaseConnection();
        $note = $db->exec_SELECTgetSingleRow('category', 'sys_note', 'uid=' . (int)$note);
        return $this->findByIdentifier($note['category']);
    }

    /**
     * Finds the default NoteCategory
     *
     * @return \TheCodingOwl\BeUserNotes\Domain\Model\NoteCategory
     */
    public function findDefaultNoteCategory(){
        $categoryIdentifier = $GLOBALS['TCA']['sys_note']['columns']['category']['config']['default'] ?? 0;
        return $this->findByIdentifier($categoryIdentifier);
    }
    
    /**
     * Add a NoteCategory
     * This method is not implemented!
     *
     * @param NoteCategory $noteCategory The NoteCategory record to add
     *
     * @throws NotImplementedException
     */
    public function add(NoteCategory $noteCategory){
        throw new NotImplementedException('Can not use the %s-method on note categories!', 1496352201, ['methodName' => __METHOD__]);
    }
    
    /**
     * Update a NoteCategory
     * This method is not implemented!
     *
     * @param NoteCategory $noteCategory The NoteCategory record to update
     *
     * @throws NotImplementedException
     */
    public function update(NoteCategory $noteCategory){
        throw new NotImplementedException('Can not use the %s-method on note categories!', 1496352201, ['methodName' => __METHOD__]);
    }
    
    /**
     * Remove a NoteCategory
     * This method is not implemented!
     *
     * @param NoteCategory $noteCategory The NoteCategory record to remove
     *
     * @throws NotImplementedException
     */
    public function remove(NoteCategory $noteCategory){
        throw new NotImplementedException('Can not use the %s-method on note categories!', 1496352201, ['methodName' => __METHOD__]);
    }
    
    /**
     * Persist changes to database
     * This method is not implemented!
     *
     * @throws NotImplementedException
     */
    public function persist(){
        throw new NotImplementedException('Can not use the %s-method on note categories!', 1496352201, ['methodName' => __METHOD__]);
    }
    
    /**
     * Get the DatabaseConnection object
     *
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection(): DatabaseConnection{
        return $GLOBALS['TYPO3_DB'];
    }
}
