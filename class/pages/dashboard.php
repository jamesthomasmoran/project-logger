<?php
/**
 * A class that contains code to handle any requests for  /dashboard/
 *
 * @author Your Name <Your@email.org>
 * @copyright year You
 * @package Framework
 * @subpackage UserPages
 */
    namespace Pages;

    use \Support\Context as Context;
/**
 * Support /dashboard/
 */
    class Dashboard extends \Framework\Siteaction
    {
/**
 * Handle dashboard operations
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handle(Context $context)
        {
            return '@content/dashboard.twig';
        }
    }
?>