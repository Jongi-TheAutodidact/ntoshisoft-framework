<?php

class FormValidator
{
    private $rules = [];
    private $errors = [];

    public function __construct(array $rules = [])
    {
        $this->rules = $rules;
    }

    public function validate(array $data): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $ruleSet) {
            foreach ($ruleSet as $rule) {
                $value = $data[$field] ?? '';

                switch ($rule) {
                    case 'required':
                        if (empty($value)) {
                            $this->errors[$field] = "** $field is required **";
                        }
                        break;

                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $this->errors[$field] = "** Invalid email format **";
                        }
                        break;

                    case 'numeric':
                        if (!is_numeric($value)) {
                            $this->errors[$field] = "** Must be a number **";
                        }
                        break;

                    case 'positive':
                        if ($value <= 0) {
                            $this->errors[$field] = "** Must be a positive number **";
                        }
                        break;
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