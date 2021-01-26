<?php


    namespace Pages;


    use Support\Context as Context;

    class EditProject
    {
        /**
         * Handle updateproject operations
         *
         * @param Context   $context    The context object for the site
         *
         * @return string|array   A template name
         */
        public function handle(Context $context, $project)
        {
            return self::handleForm($context, $project, '@content/editproject.twig');
        }
    }