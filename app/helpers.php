<?php

if (!function_exists('safe_property')) {
    /**
     * Acessa uma propriedade de um objeto de forma segura
     * 
     * @param mixed $object
     * @param string $property
     * @param string $default
     * @return string
     */
    function safe_property($object, $property, $default = 'Não definido') {
        return $object && isset($object->$property) ? $object->$property : $default;
    }
}

if (!function_exists('safe_array_count')) {
    /**
     * Conta elementos de um array de forma segura
     * 
     * @param mixed $array
     * @return int
     */
    function safe_array_count($array) {
        return is_array($array) ? count($array) : 0;
    }
}

if (!function_exists('format_cnpj')) {
    /**
     * Formata CNPJ
     * 
     * @param string $cnpj
     * @return string
     */
    function format_cnpj($cnpj) {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        if (strlen($cnpj) === 14) {
            return substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
        }
        return $cnpj;
    }
}

if (!function_exists('format_cpf')) {
    /**
     * Formata CPF
     * 
     * @param string $cpf
     * @return string
     */
    function format_cpf($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        if (strlen($cpf) === 11) {
            return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
        }
        return $cpf;
    }
}

if (!function_exists('format_phone')) {
    /**
     * Formata telefone
     * 
     * @param string $phone
     * @return string
     */
    function format_phone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) === 11) {
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7, 4);
        } elseif (strlen($phone) === 10) {
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 4) . '-' . substr($phone, 6, 4);
        }
        return $phone;
    }
}
