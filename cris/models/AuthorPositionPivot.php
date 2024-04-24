<?php namespace Bree7e\Cris\Models;

use October\Rain\Database\Pivot;

/**
 * Pivot Model
 */
class AuthorPositionPivot extends Pivot
{
    /**
     * @var array Relations
     */

    public $belongsTo = [
        'department' => ['Bree7e\Cris\Models\Department']
    ];
}
