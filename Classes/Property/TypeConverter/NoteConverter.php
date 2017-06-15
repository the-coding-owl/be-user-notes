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

namespace TheCodingOwl\BeUserNotes\Property\TypeConverter;

use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TheCodingOwl\BeUserNotes\Domain\Repository\NoteCategoryRepository;

/**
 * TypeConverter for Note objects, because the DataMapper must not be used 
 * to convert the properties of the Note. It can not handle the NoteCategory
 * property and tries to convert it to a wrong target type. We want the
 * PropertyMapper to take care of the mapping.
 *
 * @author Kevin Ditscheid <kevinditscheid@gmail.com>
 */
class NoteConverter extends PersistentObjectConverter{
    /**
     * Convert the source to a Note
     *
     * @param int|string|array $source
     * @param strng $targetType
     * @param array $convertedChildProperties
     * @param PropertyMappingConfigurationInterface $configuration
     * @return Note
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null) {
        $object = parent::convertFrom($source, $targetType, $convertedChildProperties, $configuration);
        try{
            // test if the category is converted correctly
            $categoryValue = $category->getValue();
        } catch (\Error $ex) {
            // fix the category mapping
            if( is_int($source) || is_string($source) ){
                $noteId = $source;
            } elseif( is_array($source) && $source['__identity'] ){
                $noteId = $source['__identity'];
            }
            $noteCategoryRepository = $this->objectManager->get(NoteCategoryRepository::class);
            $noteCategory = $noteCategoryRepository->findByNote($noteId);
            if( $noteCategory === NULL ){
                $noteCategory = $noteCategoryRepository->findDefaultNoteCategory();
            }
            $object->setCategory($noteCategory);
        }
        return $object;
    }
}
