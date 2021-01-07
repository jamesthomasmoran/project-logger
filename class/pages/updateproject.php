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
    class UpdateProject extends \Framework\Siteaction
    {
        private const MAXDESCRIPTIONLENGTH = 250;
        /**
         * Handle updateproject operations
         *
         * @param Context   $context    The context object for the site
         *
         * @return string|array   A template name
         */
        public function handle(Context $context)
        {
            $fdt = $context->formData('post');

            $projectId = $context->rest()[0];

            $project = \R::load('project', $projectId);

            if ($context->user()->getID() != $project->user_id){
                return '@error/403.twig';
            }

            $change = FALSE;
            if ($fdt->exists('name'))
            {
                $name = $fdt->mustfetch('name');
                if ($name === '')
                {
                    $context->local()->message(\Framework\local::ERROR, 'Project name is required');
                }
                else if ($name != $project->name)
                {
                    $project->name = $name;
                    $change = TRUE;
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
                if (!$errorPresent && $description != $project->description){
                    $project->description = $description;
                    $change = TRUE;
                }
            }
            if($project->name != '' && $project->description != '' && $change)
            {
                $user = $context->user();
                $user->ownProjectList[] = $project;

                \R::store($user);

                $context->divert('/');

            }
            else
            {
                $context->local()->addval('updateproject', $project);
                return '@content/updateproject.twig';
            }
        }
    }
    ?>