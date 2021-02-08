<?php
/**
* Contains definition of AddNote class
*
* @author James Moran <j.moran3@ncl.ac.uk>
* @package Pages
* @subpackage Note
*/
    namespace Pages\Note;


    use RedBeanPHP\OODBBean;
    use Support\Context as Context;
/**
* A class that contains code to handle any /project/{projectid}/note/add request.
*
* User is authorised to access project in project.php so no validation needed here
*/
    class AddNote extends NoteFormBase
    {
/**
* Handles adding New Note to Project.
*
* @param Context $context The Context object
* @param OODBBean $project project note is to be added to
* @return array|string
*/
        public static function handle(Context $context, OODBBean $project)
        {
            $note = \R::dispense('note');
            $note->date = '';
            $note->hours = 0;
            return self::handleForm($context, $project, $note, '@content/addnote.twig');
        }
    }