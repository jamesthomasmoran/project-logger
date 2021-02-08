<?php
/**
* Contains definition of EditNote class
*
* @author James Moran <j.moran3@ncl.ac.uk>
* @package Pages
* @subpackage Note
*/

    namespace Pages\Note;


    use RedBeanPHP\OODBBean;
    use Support\Context as Context;
/**
* A class that contains code to handle any /project/{projectid}/note/{noteid}/edit request.
*
* User is authorised to access project in project.php so no validation needed here
* Note is confirmed to belong to Project {projectid} in note.php so no validation needed here
*
*/
    class EditNote extends NoteFormBase
    {

/**
* Handle Edit Note operations /
*
* @param Context $context The Context object
* @param OODBBean $project project note is edited in
* @param OODBBean $note Note to be Edited
* @return array|string
*/
        public static function handle(Context $context, OODBBean $project, OODBBean $note){
            return self::handleForm($context, $project, $note, '@content/editnote.twig');
        }
    }