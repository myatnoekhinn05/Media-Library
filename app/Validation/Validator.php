<?php

namespace App\Validation;

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {

            $value = trim($data[$field] ?? '');

            foreach ($fieldRules as $rule) {

                /*
                |--------------------------------------------------------------------------
                | REQUIRED
                |--------------------------------------------------------------------------
                */
                if ($rule === 'required') {

                    if ($value === '') {

                        $this->errors[$field] =
                            ucfirst(str_replace('_', ' ', $field)) . ' is required';

                        break; // stop all rules for this field
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | EMAIL
                |--------------------------------------------------------------------------
                */
                if ($rule === 'email' && $value !== '') {

                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {

                        $this->errors[$field] = 'Invalid email format';

                        break;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | MIN LENGTH
                |--------------------------------------------------------------------------
                */
                if (str_starts_with($rule, 'min:') && $value !== '') {

                    $min = (int) explode(':', $rule)[1];

                    if (strlen($value) < $min) {

                        $this->errors[$field] =
                            ucfirst(str_replace('_', ' ', $field))
                            . " must be at least {$min} characters";

                        break;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | MAX LENGTH
                |--------------------------------------------------------------------------
                */
                if (str_starts_with($rule, 'max:') && $value !== '') {

                    $max = (int) explode(':', $rule)[1];

                    if (strlen($value) > $max) {

                        $this->errors[$field] =
                            ucfirst(str_replace('_', ' ', $field))
                            . " must not exceed {$max} characters";

                        break;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | PASSWORD STRENGTH
                |--------------------------------------------------------------------------
                */
                if ($rule === 'password_strength' && $value !== '') {

                    $hasUppercase = preg_match('/[A-Z]/', $value);
                    $hasLowercase = preg_match('/[a-z]/', $value);
                    $hasNumber    = preg_match('/[0-9]/', $value);
                    $hasSpecial   = preg_match('/[\W_]/', $value);

                    if (
                        !$hasUppercase ||
                        !$hasLowercase ||
                        !$hasNumber ||
                        !$hasSpecial
                    ) {
                        $this->errors[$field] =
                            'Password must contain uppercase, lowercase, number and special character';

                        break;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | SAME (CONFIRM PASSWORD FIXED)
                |--------------------------------------------------------------------------
                */
                if (str_starts_with($rule, 'same:')) {

                    $targetField = explode(':', $rule)[1];

                    $targetValue = trim($data[$targetField] ?? '');

                    // ✅ let REQUIRED handle empty cases
                    if ($value === '' || $targetValue === '') {
                        continue;
                    }

                    // ❗ mismatch check
                    if ($value !== $targetValue) {

                        $this->errors[$field] = 'Passwords do not match';

                        break;
                    }
                }
            }
        }

        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
