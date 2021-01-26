<?php
 /**
  * Class for handling home pages
  *
  * @author Lindsay Marshall <lindsay.marshall@ncl.ac.uk>
  * @copyright 2012-2019 Newcastle University
  * @package Framework
  * @subpackage UserPages
  */
    namespace Pages;

    use \Support\Context;
/**
 * A class that contains code to implement a home page
 * @psalm-suppress UnusedClass
 */
    class Home extends \Framework\SiteAction
    {
/**
 * Handle various contact operations /
 *
 * @param Context   $context    The context object for the site
 *
 * @return string   A template name
 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
 */
        public function handle(Context $context)
        {
            $projects = \R::findAll('project', 'user_id = ?', [$context->user()->getID()]);
            $timelogs = array();
            $totalHours = array();

            foreach ( $projects as &$value){
                $timelogs[] = \R::findAll('timelog', 'project_id = ? ORDER BY date DESC', [$value->getID()]);

                $hours = 0;
                foreach (end($timelogs) as &$log)
                {
                    $hours += $log->hours;
                }
                $totalHours[] = $hours;
                $timelogs[count($timelogs) - 1] = array_slice($timelogs[count($timelogs) - 1], 0, 5);
        }

            $context->local()->addval('projects', $projects);
            $context->local()->addval('timelogs',$timelogs);
            $context->local()->addval('totalhours', $totalHours);
            return '@content/index.twig';
        }
    }
?>
