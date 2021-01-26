<?php


    namespace Pages;


    use Support\Context as Context;

    class AddProject extends ProjectFormBase
    {
        /**
         * Handle adding New Project
         *
         * @param Context   $context    The context object for the site
         *
         * @return string|array   A template name
         */
        public static function handle(Context $context){
            $project = \R::dispense('project');
            return self::handleForm($context, $project, '@content/addproject.twig');

        }
    }