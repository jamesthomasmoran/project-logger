<?php
    namespace RedBeanPHP;

    class SimpleModel
    {
        /** @var \RedBeanPHP\OODBBean */
        /** @psalm-suppress MissingConstructor  */
        public $bean;
    }

    class OODBBean
    {
        /** @var \RedBeanPHP\OODBBean */
        private static $dummy;
 /** @psalm-suppress PossiblyUnusedParam */
        public function __get(string $name)
        {
        }
 /** @psalm-suppress PossiblyUnusedParam */
        /** @psalm-suppress MissingParamType */
        public function __set(string $name, $value): void {}
 /** @psalm-suppress PossiblyUnusedParam */
        /** @psalm-suppress MissingParamType */
        public function __call($function, $args)
        {
        }
/**
 * @return int
 */
        public function getID() : int
        {
             return 0;
        }
/**
 * @param string $m
 * @return mixed
 * @psalm-suppress PossiblyUnusedParam
 */
        public function getmeta(string $m)
        {
            return '';
        }
/**
 * @param string $x
 * @return \RedBeanPHP\OODBBean
 * @psalm-suppress PossiblyUnusedParam
 * @psalm-suppress PossiblyUnusedMethod
 */
        public function with(string $x) : \RedBeanPHP\OODBBean
        {
             return self::$dummy;
        }
/**
 * @param string $x
 * @return \RedBeanPHP\OODBBean
 * @psalm-suppress PossiblyUnusedParam
 * @psalm-suppress PossiblyUnusedMethod
 */
        public function withCondition(string $x) : \RedBeanPHP\OODBBean
        {
             return self::$dummy;
        }
/**
 * @return string
 * @psalm-suppress PossiblyUnusedMethod
 */
        public function export() : string
        {
             return '';
        }
    }
?>