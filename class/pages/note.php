<?php


    namespace Pages;


    use RedBeanPHP\OODBBean;
    use Support\Context as Context;

    class Note
    {

        private const ERRORPAGE403 = '@error/403.twig';
        private const ERRORPAGE404 = '@error/404.twig';

        private const ADD = 'add';
        private const EDIT = 'edit';
        private const DELETE = 'delete';
        private const NOTE = 'note';

        /**
         * Handle returning Note operation to user
         *
         * @param Context $context The context object for the site
         * @param OODBBean $project
         * @return string|array   A template name
         */
        public static function handle(Context $context, OODBBean $project)
        {

            if (count($context->rest()) >= 3)
            {
                if ($context->rest()[1] === self::NOTE && $context->rest()[2] === self::ADD)
                {
                    return AddNote::handle($context, $project);
                }
                $note = self::getNoteIfPartOfProject($context, $project);

                if($context->rest()[1] == self::NOTE && $context->rest()[3] == self::EDIT){
                    return EditNote::handle($context, $project, $note);
                }
                /*elseif ($context->rest()[1] == self::NOTE && $context->rest()[3] == self::DELETE){
                    return DeleteNote::handle($context, $project, $note);
                }*/

            }
            return self::ERRORPAGE404;
        }


        /**
         * return a Note bean if it exists and it is parto of the provided project
         *
         * @param Context $context The context object for the site
         * @param OODBBean $project
         * @return OODBBean|string|array   A project bean or an error template name
         */
        private function getNoteIfPartOfProject(Context $context, OODBBean $project){
            $note = R::load('note', $context->rest()[2]);
            if(!$note->getID()){
                return self::ERRORPAGE404;
            }
            elseif($note->project_id != $project->getID())
            {
                return self::ERRORPAGE403;
            }
            return $note;
        }
    }