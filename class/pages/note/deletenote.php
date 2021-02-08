<?php
/**
* Contains definition of DeleteNote class
*
* @author James Moran <j.moran3@ncl.ac.uk>
* @package Pages
* @subpackage Note
*/

    namespace Pages\Note;

    use RedBeanPHP\OODBBean;
    use Support\Context as Context;

/**
* A class that contains code to handle any /project/{projectid}/note/{noteid}/delete request.
*
* User is authorised to access project in project.php so no validation needed here
* Note is confirmed to belong to Project {projectid} in note.php so no validation needed here
*/
    class DeleteNote
    {
/**
* Handles Deleting Note from project.
*
* @param Context $context The Context object
* @param OODBBean $project project note is to be deleted from
* @param OODBBean $note Note to be Deleted
* @return array|string
*/
        public static function handle(Context $context, OODBBean $project, OODBBean $note)
        {
            \R::trash('note', $note->getID());

            $context->divert('/project/' . $project->getID());
        }
    }