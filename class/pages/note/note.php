<?php
/**
* Contains definition of Note class
*
* @author James Moran <j.moran3@ncl.ac.uk>
* @package Pages
* @subpackage Note
*/

    namespace Pages\Note;


    use RedBeanPHP\OODBBean;
    use Support\Context as Context;
/**
* A class that contains code to handle any /project/{projectid}/note request.
*
* User is authorised to access project in project.php so no validation needed here
*/
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

                if ($note == self::ERRORPAGE404 || $note == self::ERRORPAGE403)
                {
                    return $note;
                }

                if ($context->rest()[1] == self::NOTE && count($context->rest()) == 3)
                {
                    return self::handleGetNote($context, $project, $note);
                }
                elseif (count($context->rest()) > 3)
                {
                    if ($context->rest()[1] == self::NOTE && $context->rest()[3] == self::EDIT)
                    {
                        return EditNote::handle($context, $project, $note);
                    }
                    elseif ($context->rest()[1] == self::NOTE && $context->rest()[3] == self::DELETE)
                    {
                        return DeleteNote::handle($context, $project, $note);
                    }
                }
            }
            return self::ERRORPAGE404;
        }


/**
* return a Note bean if it exists and it is part of the provided project
*
* @param Context $context The context object for the site
* @param OODBBean $project
* @return OODBBean|string|array   A project bean or an error template name
*/
        private static function getNoteIfPartOfProject(Context $context, OODBBean $project)
        {
            $note = \R::load('note', $context->rest()[2]);
            if(!$note->getID())
            {
                return self::ERRORPAGE404;
            }
            elseif($note->project_id != $project->getID())
            {
                return self::ERRORPAGE403;
            }
            return $note;
        }

/**
* Handle returning project details to user
*
* @param Context $context The context object for the site
* @param OODBBean $project Project that Note is In
* @param OODBBean $note Note to Be Displayed
* @return string|array   A template name
*/
        private static function handleGetNote(Context $context, OODBBean $project, OODBBean $note)
        {
            $attachments = \R::findAll('upload', 'note_id = ?', [$note->getID()]);

            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $note->date);
            $note->date = $date->format('d/m/Y');

            $context->local()->addval('note', $note);
            $context->local()->addval('attachments', $attachments);
            $context->local()->addval('project', $project);
            return '@content/note.twig';
        }
    }