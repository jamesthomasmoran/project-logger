<?php
/**
 * A class that contains code to handle any requests for  /newtimelog/
 *
 * @author Your Name <Your@email.org>
 * @copyright year You
 * @package Framework
 * @subpackage UserPages
 */
    namespace Pages;

    use \Support\Context as Context;
/**
 * Support /newtimelog/
 */
    class NewTimelog extends \Framework\Siteaction
    {
        private const DATEFORMAT = '!Y-m-d';
        private const DATEVALIDATIONREGEX = '(\d{4}-\d{2}-\d{2})';
        private const HOURSDAY = 24;
/**
 * Handle newtimelog operations
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handlmessrestfule(Context $context)
        {
            if(count($context->rest()) < 1){
                return '@error/404.twig';
            }

            $projectId = $context->rest()[0];
            $project = \R::load('project', $projectId);
            $datestring = '';

            if($project == null)
            {
                return '@error/404.twig';
            }
            else if ($project->user_id != $context->user()->getID()){
                return '@error/403.twig';
            }

            $fdt = $context->formData('post');
            $timelog = \R::dispense('timelog');

            if ($fdt->exists('date'))
            {
                $datestring = $fdt->mustfetch('date');
                if($datestring === '')
                {
                    $context->local()->message(\Framework\local::ERROR, 'Date is required');
                }
                else
                {
                    $date = \DateTime::createFromFormat(self::DATEFORMAT, $datestring);
                    if(!$date){
                        $context->local()->message(\Framework\local::ERROR, 'Date must be in the format YYYY-MM-DD');
                    }
                    else if (!$date->format(self::DATEFORMAT) === $datestring . ' 00:00:00')
                    {
                        $context->local()->message(\Framework\local::ERROR, 'Date must be in the format YYYY-MM-DD');
                    }
                    else
                    {
                        if (new \DateTime() <= $date)
                        {
                            $context->local()->message(\Framework\local::ERROR, 'Date must be in the past');
                        }
                        else
                        {
                            $timelog->date = $date;
                        }
                    }
                }
            }
            if ($fdt->exists('hours'))
            {
                $hours = (int) $fdt->mustfetch('hours');
                $errorPresent = FALSE;
                if ($hours <= 0 )
                {
                    $context->local()->message(\Framework\local::ERROR, 'Hours must be greater than zero');
                    $errorPresent = TRUE;
                }
                if ($hours >  self::HOURSDAY)
                {
                    $context->local()->message(\Framework\local::ERROR, 'Hours for one timelog cannot be greater than 24 hours');
                    $errorPresent = TRUE;
                }
                if (!$errorPresent){
                    $timelog->hours = $hours;
                }
            }
            if($timelog->date != null && $timelog->hours != null)
            {
                $project->ownTimelogList[] = $timelog;

                \R::store($project);

                $context->divert('/project/' . $projectId);

            }
            else
            {
                $timelog->date = $datestring;
                $context->local()->addval('newtimelog', $timelog);
                return '@content/newtimelog.twig';
            }
        }

        private function validDateFormat($date)
        {

        }
    }
?>