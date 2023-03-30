<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CiUnique implements Rule
{
    protected $table;
    protected $column;
    protected $exceptId;
    protected $exceptColumn;

    /**
     * Create a new rule instance.
     *
     * @param  string  $table
     * @param  string  $column
     * @param  int|null  $exceptId
     * @param  string|null  $exceptColumn
     * @return void
     */
    public function __construct($table, $column, $exceptId = null, $exceptColumn = null)
    {
        $this->table = $table;
        $this->column = $column;
        $this->exceptId = $exceptId;
        $this->exceptColumn = $exceptColumn;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $query = DB::table($this->table)
                   ->whereRaw('LOWER('.$this->column.') = ?', [strtolower($value)]);

        if ($this->exceptId) {
            $query->where('id', '<>', $this->exceptId);
        }

        if ($this->exceptColumn) {
            $query->where($this->exceptColumn, '<>', request()->input($this->exceptColumn));
        }

        return ! $query->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute has already been taken.';
    }
}
