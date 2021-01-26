<?php
/**
 * A class that contains code to handle any requests for  /project/
 *
 * @author Your Name <Your@email.org>
 * @copyright year You
 * @package Framework
 * @subpackage UserPages
 */
    namespace Pages;

    use RedBeanPHP\OODBBean;
    use \Support\Context as Context;
/**
 * Support /project/
 */
    class Project extends \Framework\Siteaction
    {
        private const ERRORPAGE403 = '@error/403.twig';
        private const ERRORPAGE404 = '@error/404.twig';

        private const ADD = 'add';
        private const EDIT = 'edit';
        private const DELETE = 'delete';
        private const NOTE = 'note';
/**
 * Handle project operations
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handle(Context $context)
        {
            if($context->rest()[0] == self::ADD){
                return AddProject::handle($context);
            }

            $project = $this->getProjectIfUserIsAuthorised($context);
            if($project == self::ERRORPAGE403 || $project == self::ERRORPAGE404){
                return $project;
            }
            elseif(count($context->rest()) <= 1)
            {
                return $this->handleGetProject($context, $project);
            }
            elseif($context->rest()[1] == self::EDIT)
            {
                return EditProject::handle($context, $project);
            }
            elseif($context->rest()[1] == self::DELETE){
                return DeleteProject::handle($context);
            }
            else{
                return Note::handle($context, $project);
            }
        }

        /**
         * return a project bean using rest url parameter if it exists
         *
         * @param Context   $context    The context object for the site
         *
         * @return OODBBean|null   A project bean or null
         */
        private function getProjectIfProvidedProjectIdExists(Context $context){
            $project = null;
            if(count($context->rest()) > 0){
                $project = \R::load('project', $context->rest()[0]);
            }

            return $project;
        }

        /**
         * return a project bean to user if it exists and user is authorised to view it
         *
         * @param Context   $context    The context object for the site
         *
         * @return OODBBean|string|array   A project bean or an error template name
         */
        private function getProjectIfUserIsAuthorised(Context $context){
            $project = $this->getProjectIfProvidedProjectIdExists($context);
            if(!$project->getID()){
                return self::ERRORPAGE4044;
            }
            elseif($project->user_id != $context->user()->getID())
            {
                return self::ERRORPAGE403;
            }
            return $project;
        }

        /**
         * Handle returning project details to user
         *
         * @param Context   $context    The context object for the site
         *
         * @return string|array   A template name
         */
        private function handleGetProject($context, $project){

            $notes = \R::findAll('note', 'project_id = ? ORDER BY date DESC', [$project->id]);

            $context->local()->addval('project', $project);
            $context->local()->addval('notes', $notes);
            return '@content/project.twig';
        }

        /**
         * Handle adding New Project
         *
         * @param Context   $context    The context object for the site
         *
         * @return string|array   A template name
         */
        private function handleAddProject($context){

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
                $context->local()->addval('project', $project);
                return '@content/addproject.twig';
            }

        }
    }
?>