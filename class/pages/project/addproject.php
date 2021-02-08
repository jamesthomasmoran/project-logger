<?php
/**
* Contains definition of AddProject class
*
* @author James Moran <j.moran3@ncl.ac.uk>
* @package Pages
* @subpackage Project
*/

    namespace Pages\Project;


    use Support\Context as Context;
/**
* A class that contains code to handle any /project/add request.
*
*/
    class AddProject extends ProjectFormBase
    {
/**
* Handle adding New Project
*
* @param Context   $context    The context object for the site
*
* @return string|array   A template name
*/
        public static function handle(Context $context)
        {
            $project = \R::dispense('project');
            return self::handleForm($context, $project, '@content/addproject.twig');
        }
    }