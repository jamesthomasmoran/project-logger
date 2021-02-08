<?php
 /**
  * Class for handling home page
  *
  * @author James Moran <j.moran3@ncl.ac.uk>
  * @package Pages
  */
    namespace Pages;

    use \Support\Context;
/**
 * A class that contains code to implement a home page
 */
    class Home extends \Framework\SiteAction
    {
/**
 * Return the home page to the user
 *
 * @param Context   $context    The context object for the site
 *
 * @return string   A template name
 */
        public function handle(Context $context): string
        {
            $projects = \R::findAll('project', 'user_id = ?', [$context->user()->getID()]);
            $notes = array();
            $totalHours = array();

            foreach ( $projects as &$value)
            {
                $notes[] = \R::findAll('note', 'project_id = ? ORDER BY date DESC', [$value->getID()]);

                $hours = 0;
                foreach (end($notes) as &$note)
                {
                    $hours += $note->hours;
                    $date = \DateTime::createFromFormat('Y-m-d H:i:s', $note->date);
                    $note->date = $date->format('d/m/Y');
                }
                $totalHours[] = $hours;
                $notes[count($notes) - 1] = array_slice($notes[count($notes) - 1], 0, 5);

            }

            $context->local()->addval('projects', $projects);
            $context->local()->addval('notes', $notes);
            $context->local()->addval('totalhours', $totalHours);
            return '@content/index.twig';
        }
    }

