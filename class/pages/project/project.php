<?php
/**
 * A class that contains class Project
 *
 * @author James Moran <j.moran3@ncl.ac.uk>
 * @package Pages
 * @subpackage Project
 */
    namespace Pages\Project;

    use Pages\Note\Note;
    use RedBeanPHP\OODBBean;
    use \Support\Context as Context;
/**
* A class that contains code to handle any /project request.
*
*/
    class Project extends \Framework\Siteaction
    {
        private const ERRORPAGE403 = '@error/403.twig';
        private const ERRORPAGE404 = '@error/404.twig';

        private const ADD = 'add';
        private const EDIT = 'edit';
        private const DELETE = 'delete';

/**
 * Handle project operations
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handle(Context $context)
        {
            if ($context->rest()[0] == self::ADD)
            {
                return AddProject::handle($context);
            }

            $project = $this->getProjectIfUserIsAuthorised($context);

            if ($project == self::ERRORPAGE403 || $project == self::ERRORPAGE404)
            {
                return $project;
            }
            elseif (count($context->rest()) <= 1)
            {
                return $this->handleGetProject($context, $project);
            }
            elseif ($context->rest()[1] == self::EDIT)
            {
                return EditProject::handle($context, $project);
            }
            elseif ($context->rest()[1] == self::DELETE)
            {
                return DeleteProject::handle($context, $project);
            }
            else
            {
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
        private function getProjectIfProvidedProjectIdExists(Context $context): ?OODBBean
        {
            $project = null;
            if (count($context->rest()) > 0)
            {
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
        private function getProjectIfUserIsAuthorised(Context $context)
        {
            $project = $this->getProjectIfProvidedProjectIdExists($context);
            if (!$project->getID())
            {
                return self::ERRORPAGE404;
            }
            elseif ($project->user_id != $context->user()->getID())
            {
                return self::ERRORPAGE403;
            }
            return $project;
        }

/**
* Handle returning project details to user
*
* @param Context $context The context object for the site
* @param OODBBean $project Project to be displayed
* @return string|array   A template name
*/
        private function handleGetProject(Context $context, OODBBean $project){

            $notes = \R::findAll('note', 'project_id = ? ORDER BY date DESC', [$project->id]);
            foreach ($notes as $note)
            {
                $date = \DateTime::createFromFormat('Y-m-d H:i:s', $note->date);
                $note->date = $date->format('d/m/Y');
            }
            $context->local()->addval('project', $project);
            $context->local()->addval('notes', $notes);
            return '@content/project.twig';
        }
    }
