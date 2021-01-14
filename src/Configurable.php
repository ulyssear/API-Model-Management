<?php


namespace App;


trait Configurable
{

    private Collection $config;

    public function config() {
        return $this->config;
    }

    private function initializeConfig()
    {

        $this->config = (new Collection())
            ->setFunction('add', function ($arguments) {

                $name = $arguments[0] ?? null;
                $entry = $arguments[1] ?? null;

                if (null === $entry) return;

                $this->config->map[$name] = $entry;

            })
            ->setFunction('get', function ($arguments) {
                $config = $this->config->entries();

                $name = $arguments[0] ?? null;

                if (null === $name) return null;

                $names = explode('.', $name);

                $cursorEntry = null;

                foreach ($names as $currentName) {

                    try {
                        $cursorEntry = $cursorEntry[$currentName] ?? ($config[$currentName] ?? null);
                    } catch (\Throwable $exception) {
                        break;
                    }

                }

                return $cursorEntry;
            });

        $files = glob('../config/*.{ini}', GLOB_BRACE);
        foreach ($files as $file) {
            $filename = basename($file);
            $filename = explode('.', $filename);
            $filename = $filename[0];

            $this->config->add($filename, parse_ini_file($file, true));
        }

    }

}