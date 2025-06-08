<?php

class Validator
{
    private string $text = "";
    private array $errors = [];

    private string $alias = "";

    public function __construct(string $text, string $alias = "")
    {
        $this->text = $text;
        $this->alias = $alias;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function required(): Validator
    {
        if (empty($this->text) && $this->text !== '0' && $this->text !== 'false') {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  es obligatorio.';
        }
        return $this;
    }

    public function minLength(int $length): Validator
    {
        if (strlen($this->text) < $length) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  debe tener al menos ' . $length . ' caracteres.';
        }
        return $this;
    }

    public function maxLength(int $length): Validator
    {
        if (strlen($this->text) > $length) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  no puede tener más de ' . $length . ' caracteres.';
        }
        return $this;
    }

    public function email(): Validator
    {
        if (!filter_var($this->text, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  no es un correo electrónico válido.';
        }
        return $this;
    }

    public function alpha(): Validator
    {
        if (!preg_match('/^\p{L}+$/', $this->text)) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  solo puede contener letras.';
        }
        return $this;
    }

    public function alphaWithSpaces(): Validator
    {
        if (!preg_match('/^[\p{L}\s]+$/u', $this->text)) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  solo puede contener letras y espacios.';
        }
        return $this;
    }

    public function numeric(): Validator
    {
        if (!is_numeric($this->text)) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  solo puede contener números.';
        }
        return $this;
    }

    public function minValue(int $min): Validator
    {
        if ($this->text < $min) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  debe ser mayor o igual a ' . $min . '.';
        }
        return $this;
    }

    public function maxValue(int $max): Validator
    {
        if ($this->text > $max) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  debe ser menor o igual a ' . $max . '.';
        }
        return $this;
    }

    public function alphaNumeric(): Validator
    {
        if (!preg_match('/^[\p{L}\d]+$/u', $this->text)) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  solo puede contener letras y números.';
        }
        return $this;
    }

    public function alphaNumericWithSpaces(): Validator
    {
        if (!preg_match('/^[\p{L}\d\s]+$/u', $this->text)) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  solo puede contener letras, números y espacios.';
        }
        return $this;
    }

    public function alphaNumericWithSecureSpecialChars(): Validator
    {
        if (!preg_match('/^[\p{L}\d\s\-_@.,]+$/u', $this->text)) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  solo puede contener letras, números, espacios y caracteres especiales seguros (- _ @ . ,).';
        }
        return $this;
    }

    public function spaces(int $spaces): Validator
    {
        if (substr_count($this->text, ' ') !== $spaces) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  debe contener exactamente ' . $spaces . ' espacios.';
        }
        return $this;
    }

    public function maxSpaces(int $maxSpaces): Validator
    {
        if (substr_count($this->text, ' ') > $maxSpaces) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  no puede contener más de ' . $maxSpaces . ' espacios.';
        }
        return $this;
    }

    public function password(): Validator
    {
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,64}$/', $this->text)) {
            $this->errors[] = 'La contraseña debe tener al menos 8 caracteres, máximo 64, una letra mayúscula, una letra minúscula y un número.';
        }
        return $this;
    }

    public function phone(): Validator
    {
        if (!preg_match('/^\+?[0-9]{10,15}$/', $this->text)) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  no es un número de teléfono válido.';
        }
        return $this;
    }

    public function date(): Validator
    {
        if (!DateTime::createFromFormat('Y-m-d', $this->text)) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  no es una fecha válida.';
        }
        return $this;
    }

    public function dateTime(): Validator
    {
        if (!DateTime::createFromFormat('Y-m-d H:i:s', $this->text)) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  no es una fecha y hora válida.';
        }
        return $this;
    }

    public function url(): Validator
    {
        if (!filter_var($this->text, FILTER_VALIDATE_URL)) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  no es una URL válida.';
        }
        return $this;
    }

    public function noSpecialChars(): Validator
    {
        if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $this->text)) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  no puede contener caracteres especiales.';
        }
        return $this;
    }

    public function noNumbers(): Validator
    {
        if (preg_match('/[0-9]/', $this->text)) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  no puede contener números.';
        }
        return $this;
    }

    public function alphaNumericWithDashes(): Validator
    {
        if (!preg_match('/^[a-zA-Z0-9-_]+$/', $this->text)) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  solo puede contener letras, números, guiones y guiones bajos.';
        }
        return $this;
    }

    public function isTinyInt(): Validator
    {
        $intValue = intval($this->text);
        if ($intValue < 0 || $intValue > 1) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  debe ser 0 o 1.';
        }
        return $this;
    }

    public function CiOrRif(): Validator
    {
        if (!preg_match('/^[VEJG]{1}[-]{1}[0-9]{7,12}$/', $this->text)) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  no es un RIF o CI válido.';
        }
        return $this;
    }

    public function boolean(): Validator
    {
        // Sea booleano, ya sea false, true, 0 o 1, o sus equivalentes strings
        if (!is_bool($this->text) && !is_numeric($this->text) && !in_array($this->text, ['false', 'true', '0', '1'])) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  debe ser un valor booleano.';
        }
        return $this;
    }

    // Métodos estáticos

    public static function ensureFields(array $data, array $requiredFields): void
    {
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            Responses::json(['errors' => ['Faltan los siguientes campos: ' . implode(', ', $missingFields)]], 400);
        }
    }
}

?>