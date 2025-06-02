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
        if (!preg_match('/^[a-zA-Z]+$/', $this->text)) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  solo puede contener letras.';
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

    public function alphaNumeric(): Validator
    {
        if (!preg_match('/^[a-zA-Z0-9]+$/', $this->text)) {
            $this->errors[] = 'El campo de ' . $this->getAlias() . '  solo puede contener letras y números.';
        }
        return $this;
    }

    public function password(): Validator
    {
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $this->text)) {
            $this->errors[] = 'La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una letra minúscula y un número.';
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
}

?>