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

use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;

use TheCodingOwl\BeUserNotes\Domain\Model\NoteCategory;
use TheCodingOwl\BeUserNotes\Domain\Repository\NoteCategoryRepository;

/**
 * This is a TypeConverter that can convert incoming arrays or identifier values to a NoteCategory ValueObject
 *
 * @author Kevin Ditscheid <kevinditscheid@gmail.com>
 */
class NoteCategoryConverter extends AbstractTypeConverter{
    /**
     * The source types this converter can convert.
     *
     * @var array<string>
     */
    protected $sourceTypes = ['int','string','array'];
    
    /**
     * The target type this converter can convert to.
     *
     * @var string
     */
    protected $targetType = NoteCategory::class;

    /**
     * The priority for this converter.
     *
     * @var int
     */
    protected $priority = 50;
    
    /**
     * Convert from $source to $targetType
     *
     * @param int|string|array $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param \TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface $configuration
     *
     * @return NULL|\TYPO3\CMS\Extbase\Error\Error|\TheCodingOwl\BeUserNotes\Domain\Model\NoteCategory
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = NULL) {
        if( $targetType !== $this->targetType ){
            return new Error('Given target type "%s" does not match the converters target type "%s"', 1496429616, [$targetType, $this->targetType]);
        }
        if( is_array($source) ){
            return $this->convertFromArray($source, $configuration);
        }
        if( is_string($source) || is_int($source) ){
            return $this->convertFromIdentifier($source, $configuration);
        }
        return NULL;
    }

    /**
     * Convert the source by handling it as an identifier for the NoteCategory
     *
     * @param int|string $identifier The identifier
     * @param PropertyMappingConfigurationInterface $configuration The PropertyMappingConfiguration
     *
     * @return NULL|NoteCategory
     */
    protected function convertFromIdentifier($identifier, PropertyMappingConfigurationInterface $configuration = NULL){
        $noteCategoryRepository = $this->objectManager->get(NoteCategoryRepository::class);
        return $noteCategoryRepository->findByIdentifier($identifier);
    }
    
    /**
     * Convert the source by handling it as an array representing the NoteCategory
     *
     * @param array $source An array representing a NoteCategory
     * @param PropertyMappingConfigurationInterface $configuration The PropertyMappingConfiguration
     *
     * @return NULL|NoteCategory
     */
    protected function convertFromArray(array $source, PropertyMappingConfigurationInterface $configuration = NULL){
        $noteCategoryRepository = $this->objectManager->get(NoteCategoryRepository::class);
        if( isset($source['__identifier']) ){
            $noteCategory = $noteCategoryRepository->findByIdentifier($source['__identifier']);
            $noteCategory->setIcon($source['icon']);
            $noteCategory->setLabel($source['label']);
        } else {
            $noteCategory = $this->objectManager->get(NoteCategory::class);
            $noteCategory->setValue($source['value']);
            $noteCategory->setIcon($source['icon']);
            $noteCategory->setLabel($source['label']);
        }
        return $noteCategory;
    }
}
