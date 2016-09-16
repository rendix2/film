<?php

    /**
     * class for the Smarty variable object
     * This class defines the Smarty variable object
     * @package    Smarty
     * @subpackage Template
     */
    class Smarty_Variable {
        /**
         * template variable
         * @var mixed
         */
        public $value = NULL;

        /**
         * if true any output of this variable will be not cached
         * @var boolean
         */
        public $nocache = FALSE;

        /**
         * create Smarty variable object
         * @param mixed $value the value to assign
         * @param boolean $nocache if true any output of this variable will be not cached
         */
        public function __construct ( $value = NULL, $nocache = FALSE ) {
            $this->value   = $value;
            $this->nocache = $nocache;
        }

        /**
         * <<magic>> String conversion
         * @return string
         */
        public function __toString () {
            return (string) $this->value;
        }
    }

