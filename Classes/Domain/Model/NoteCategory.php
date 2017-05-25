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

use TYPO3\CMS\Extbase\DomainObject\AbstractValueObject;

/**
 * NoteCategory this model represents the NoteCategory which is only configured
 * in TCA of the sys_note category column
 *
 * @author Kevin Ditscheid <kevinditscheid@gmail.com>
 */
class NoteCategory extends AbstractValueObject{
    /**
     * The icon of the NoteCategory
     *
     * @var string
     */
    protected $icon;
    
    /**
     * The label of the NoteCategory
     *
     * @var string
     */
    protected $label;
    
    /**
     * The value of the category
     *
     * @var int
     */
    protected $value;
    
    /**
     * All possible icons of a NoteCategory
     *
     * @var array
     */
    static public $possibleIcons = [];
    
    /**
     *All possible labels of a NoteCategory
     *
     * @var array
     */
    static public $possibleLabels = [];
    
    /**
     * All possible values of a NoteCategory
     *
     * @var array
     */
    static public $possibleValues = [];

    /**
     * Setting up the NoteCategory model and its static values
     */
    public function __construct() {
        if( isset($GLOBALS['TCA']['sys_note']['columns']['category']['config']['items']) ){
            foreach( $GLOBALS['TCA']['sys_note']['columns']['category']['config']['items'] as $item ){
                static::$possibleLabels[$item[1]] = $item[0];
                static::$possibleIcons[$item[1]] = $item[2];
                static::$possibleValues[$item[1]] = $item[1];
            }
        }
    }

    /**
     * Set the icon
     *
     * @param string $icon The icon to set
     *
     * @return self
     *
     * @throws \UnexpectedValueException The icon is not supported
     */
    public function setIcon(string $icon): self {
        if( !in_array($icon, static::$possibleIcons) ){
            throw new \UnexpectedValueException("The icon \"$icon\" is not supported for sys_note!");
        }
        $this->icon = $icon;
        return $this;
    }

    /**
     * Set the label
     *
     * @param string $label The label to set
     *
     * @return self
     *
     * @throws \UnexpectedValueException The label is not supported
     */
    public function setLabel(string $label): self {
        if( !in_array($label, static::$possibleLabels) ){
            throw new \UnexpectedValueException("The label \"$label\" is not supported for sys_note!");
        }
        $this->label = $label;
        return $this;
    }

    /**
     * Set the value
     *
     * @param int $value The value to set
     *
     * @return self
     *
     * @throws \UnexpectedValueException The value is not supported
     */
    public function setValue(int $value): self {
        if( !in_array($value, static::$possibleValues) ){
            throw new \UnexpectedValueException("The value \"$value\" is not supported for sys_note!");
        }
        $this->value = $value;
        return $this;
    }

    /**
     * Get the icon
     *
     * @return string
     */
    public function getIcon(): string {
        return $this->icon;
    }

    /**
     * Get the label
     *
     * @return string
     */
    public function getLabel(): string {
        return $this->label;
    }

    /**
     * Get the value of the NoteCategory
     *
     * @return string
     */
    public function getValue(): string {
        return $this->value;
    }

}
