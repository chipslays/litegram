<?php

namespace Litegram\Traits;

trait Components
{
    /**
     * Подключение компонентов
     *
     * @return void
     */
    private function loadComponents()
    {
        $components = $this->config()->get('components');

        if (!$components) {
            return;
        }

        foreach ($components as $component) {
            if (!$component['enable'] ?? null) {
                continue;
            }

            if (file_exists($component['entrypoint'] ?? null)) {
                try {
                    require_once $component['entrypoint'];
                } catch (\Throwable $th) {
                    echo $th->getMessage();
                }
            }
        }
    }
}
