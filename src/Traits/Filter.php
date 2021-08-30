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
                if (!$this->payload()->has($key) || $this->payload()->get($key) !== $value[$key]) {
                    return false;
                }
            }

            if (is_string($value) && !$this->payload()->has($value)) {
                return false;
            }
        }

        return $this->call($func);
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
                if ($this->payload()->has($key) && $this->payload()->get($key) == $value[$key]) {
                    return $this->call($func);
                }
            }

            if (is_string($value) && $this->payload()->has($value)) {
                return $this->call($func);
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
                if ($this->payload()->has($key) || $this->payload()->get($key) == $value[$key]) {
                    return false;
                }
            }

            if (is_string($value) && $this->payload()->has($value)) {
                return false;
            }
        }

        return $this->call($func);
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
                if (!$this->payload()->has($key) && $this->payload()->get($key) !== $value[$key]) {
                    return $this->call($func);
                }
            }

            if (is_string($value) && !$this->payload()->has($value)) {
                return $this->call($func);
            }
        }

        return false;
    }
}