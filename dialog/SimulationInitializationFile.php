<?php

/**
 * Created by PhpStorm.
 */
class SimulationInitializationFile
{
    /**
     * data posted in the form
     */
    protected $simulationFilePath = "";

    public function getSimulationFilePath(){
        return $this->simulationFilePath;
    }

    public function setSimulationFilePath($simulationFilePath){
        $this->simulationFilePath = $simulationFilePath;
    }
}