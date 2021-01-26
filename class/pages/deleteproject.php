<?php


    namespace Pages;


    class DeleteProject
    {
        public static function handle($context){
            \R::trash('project', $context->local()->id);

            $context->divert('/');
        }
    }