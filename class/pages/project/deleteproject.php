<?php
/**
* Contains definition of DeleteProject class
*
* @author James Moran <j.moran3@ncl.ac.uk>
* @package Pages
* @subpackage Project
*/
    namespace Pages\Project;

    use RedBeanPHP\OODBBean;
    use Support\Context as Context;

/**
* A class that contains code to handle any /project/{projectid}/delete request.
*
* User is authorised to access project in project.php so no validation needed here
*/
    class DeleteProject
    {
/**
* Handle Deleting Project
*
* @param Context $context The context object for the site
* @param OODBBean $project project to be deleted
* @return string|array   A template name
*/
        public static function handle(Context $context, OODBBean $project)
        {
            \R::trash('project', $project->getID());

            $context->divert('/');
        }
    }