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

    use \Support\Context as Context;
/**
 * Support /project/
 */
    class Project extends \Framework\Siteaction
    {
/**
 * Handle project operations
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handle(Context $context)
        {
            $projectId = $context->rest()[0];
            $project = \R::load('project', $projectId);

            if($project->user_id != $context->user()->getID())
            {
                return '@error/403.twig';
            }

            $context->local()->addval('project', $project);
            return '@content/project.twig';
        }
    }
?>