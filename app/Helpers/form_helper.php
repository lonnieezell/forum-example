<?php

use CodeIgniter\Validation\ValidationInterface;

function form_error(ValidationInterface $validator, string $field): string
{
    if ($validator->hasError($field)) {
        return '<label class="label error"><span class="label-text-all">' . $validator->getError($field) . '</span></label>';
    }

    return '';
}
