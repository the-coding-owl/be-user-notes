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

use TYPO3\CMS\Core\Type\TypeInterface;

/**
 * NoteCategory this model represents the NoteCategory which is only configured
 * in TCA of the sys_note category column
 *
 * @author Kevin Ditscheid <kevinditscheid@gmail.com>
 */
class NoteCategory implements TypeInterface{
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
     * Set the icon
     *
     * @param string $icon The icon to set
     *
     * @return self
     */
    public function setIcon(string $icon): self {
        $this->icon = $icon;
        return $this;
    }

    /**
     * Set the label
     *
     * @param string $label The label to set
     *
     * @return self
     */
    public function setLabel(string $label): self {
        $this->label = $label;
        return $this;
    }

    /**
     * Set the value
     *
     * @param int $value The value to set
     *
     * @return self
     */
    public function setValue(int $value): self {
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

    /**
     * Magic method to get the string representation of this object
     *
     * @return string
     */
    public function __toString() {
        return $this->getValue();
    }
}
