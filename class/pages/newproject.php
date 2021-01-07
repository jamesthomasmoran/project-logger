<?php
/**
 * A class that contains code to handle any requests for  /newproject/
 *
 * @author Your Name <Your@email.org>
 * @copyright year You
 * @package Framework
 * @subpackage UserPages
 */
    namespace Pages;

    use \Support\Context as Context;
/**
 * Support /newproject/
 */
    class NewProject extends \Framework\Siteaction
    {
        private const MAXDESCRIPTIONLENGTH = 250;
/**
 * Handle newproject operations
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handle(Context $context)
        {
            $fdt = $context->formData('post');
            $project = \R::dispense('project');

            if ($fdt->exists('name'))
            {
                $name = $fdt->mustfetch('name');
                if($name === '')
                {
                    $context->local()->message(\Framework\local::ERROR, 'Project name is required');
                }
                else{
                    $project->name = $name;
                }
            }
            if ($fdt->exists('description'))
            {


                $description = $fdt->mustfetch('description');

                $errorPresent = FALSE;
                if ($description == '')
                {
                    $context->local()->message(\Framework\local::ERROR, 'Description is required');
                    $errorPresent = TRUE;
                }
                if (strlen($description) > self::MAXDESCRIPTIONLENGTH )
                {
                    $context->local()->message(\Framework\local::ERROR, 'Description must be under 250 characters');
                    $errorPresent = TRUE;
                }
                if (!$errorPresent){
                    $project->description = $description;
                }
            }
            if($project->name != '' && $project->description != '')
            {
                $user = $context->user();
                $user->ownProjectList[] = $project;

                \R::store($user);

                $context->divert('/');

            }
            else
            {
                $context->local()->addval('newproject', $project);
                return '@content/newproject.twig';
            }
        }
    }
?>