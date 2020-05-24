<?php

/**
 * @author   Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @copyright: (c) 2017 Beeflow Ltd
 *
 * Date: 24.09.17 20:00
 */

namespace BMServerBundle\Server\Traits;

trait RequiredCheckTrait
{
    /**
     * Sprawdzanie, czy zostały wypełnione wszystkie wymagane pola.
     * Jeżeli jeden z elementów listy będzie tablicą oznacza to, że na tej liście znajdują się klucze względnie
     * obowiązkowe.
     *
     * @param array $formData Dane przychodzące od użytkownika
     * @param array $requiredFields Lista pół, które są polami wymaganymi.
     *
     * @return bool
     */
    protected function hasAllRequired(array $formData, array $requiredFields): bool
    {
        $requestParamsKeys = array_keys($formData);

        foreach ($requiredFields as $value) {
            if (is_array($value)) {
                if ($this->checkOneOfRequired($formData, $value)) {
                    continue;
                }

                return false;
            }

            if (!in_array($value, $requestParamsKeys, true) || empty($formData[$value])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sprawdza, czy chociaż jeden z wymaganych jest na liście.
     * Pozwala to na wskazanie listy kluczy dla pól, z których chociaż jedno musi zostać wypełnione
     *
     * @param array $formData
     * @param array $requiredFields
     *
     * @return bool
     */
    protected function checkOneOfRequired(array $formData, array $requiredFields): bool
    {
        $requestParamsKeys = array_keys($formData);

        foreach ($requiredFields as $key) {
            if (in_array($key, $requestParamsKeys, true) && isset($formData[$key]) && !empty($formData[$key])) {
                return true;
            }
        }

        return false;
    }
}
