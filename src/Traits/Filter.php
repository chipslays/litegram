<?php

namespace Litegram\Traits;

trait Filter
{
    /**
     * Выполнить функцию если все ключи существуют
     *
     * @param string|array $filters
     * @param $func
     * @return mixed
     */
    public function filter($filters, $func)
    {
        foreach ((array) $filters as $value) {
            if (is_array($value)) {
                $key = key($value);
                if (!$this->update()->has($key) || $this->update()->get($key) !== $value[$key]) {
                    return false;
                }
            }

            if (is_string($value) && !$this->update()->has($value)) {
                return false;
            }
        }

        return $this->callFunc($func);
    }

    /**
     * Выполнить функцию если хотя бы один ключ существует
     *
     * @param string|array $filters
     * @param $func
     * @return mixed
     */
    public function filterAny($filters, $func)
    {
        foreach ((array) $filters as $value) {
            if (is_array($value)) {
                $key = key($value);
                if ($this->update()->has($key) && $this->update()->get($key) == $value[$key]) {
                    return $this->callFunc($func);
                }
            }

            if (is_string($value) && $this->update()->has($value)) {
                return $this->callFunc($func);
            }
        }

        return false;
    }

    /**
     * Выполнить функцию если все ключи не существуют
     *
     * @param string|array $filters
     * @param $func
     * @return mixed
     */
    public function filterNot($filters, $func)
    {
        foreach ((array) $filters as $value) {
            if (is_array($value)) {
                $key = key($value);
                if ($this->update()->has($key) || $this->update()->get($key) == $value[$key]) {
                    return false;
                }
            }

            if (is_string($value) && $this->update()->has($value)) {
                return false;
            }
        }

        return $this->callFunc($func);
    }

    /**
     * Выполнить функцию если хотя бы один ключ не существует
     *
     * @param string|array $filters
     * @param $func
     * @return mixed
     */
    public function filterAnyNot($filters, $func)
    {
        foreach ((array) $filters as $value) {
            if (is_array($value)) {
                $key = key($value);
                if (!$this->update()->has($key) && $this->update()->get($key) !== $value[$key]) {
                    return $this->callFunc($func);
                }
            }

            if (is_string($value) && !$this->update()->has($value)) {
                return $this->callFunc($func);
            }
        }

        return false;
    }
}
