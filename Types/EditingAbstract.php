<?php

namespace fwc\Thing;

/**
 * EditingAbstract
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
abstract class EditingAbstract implements EditingInterface
{
    protected $settings;
    protected $tableOwner;
    protected $idOwner;
    protected $POST;
    protected $FILE;
    protected $idWebPage;

    public function __construct($settings) {
        $this->settings = $settings;
        
        $this->POST = filter_input_array(INPUT_POST) ?? $_POST ?? null;        
        $this->FILE = $_FILES ?? null;
        
        $this->tableOwner = $this->POST['tableOwner'] ?? null;
        unset($this->POST['tableOwner']);
        
        $this->idOwner = $this->POST['idOwner'] ?? null;
        unset($this->POST['idOwner']); 
        
        unset($this->POST['submit']);
        unset($this->POST['submit_x']);
        unset($this->POST['submit_y']);
        unset($this->POST['MAX_FILE_SIZE']);
        
        $this->idWebPage = isset($this->POST['idwebPage']) ? (int) $this->POST['idwebPage'] : null;
        unset($this->POST['idwebPage']);        
    }
}
