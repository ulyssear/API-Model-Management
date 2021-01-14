<?php


namespace App;


use DebugBar\StandardDebugBar;

trait Debuggable
{

    private StandardDebugBar $debugBar;

    public function debugbar() {
        return $this->debugBar;
    }

    private function initializeDebugBar() {
        $this->debugBar = new StandardDebugBar();
    }

}