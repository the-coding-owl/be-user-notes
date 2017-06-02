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

namespace TheCodingOwl\BeUserNotes\View\Note;

use TYPO3\CMS\Extbase\Mvc\View\JsonView;

/**
 * Json view for the create view
 *
 * @author Kevin Ditscheid <kevinditscheid@gmail.com>
 */
class CreateJson extends JsonView{
    /**
     * Array of variables to render by this view
     *
     * @var array
     */
    protected $variablesToRender = ['success', 'validationResults', 'message'];
}
