<?php


    namespace Pages;


    use Support\Context as Context;

    class EditNote
    {
        private const HOURSDAY = 24;

        /**
         * Handle returning project details to user
         *
         * @param Context   $context    The context object for the site
         *
         * @return string|array   A template name
         */
        public static function handle($context, $project, $note){

            $currentdate = new \DateTime();


            $form = $context->formData('post');
            $date = null;
            $hours = 0;
            $error = FALSE;

            $day = $note;
            $month = '';
            $year = '';


            if($form->exists('day') && $form->exists('month') && $form->exists('year'))
            {
                $day = (int) $form->mustFetch('day');
                $month = (int) $form->mustFetch('month');
                $year = (int) $form->mustFetch('year');

                if (!checkdate($month, $day, $year))
                {
                    $context->local()->message(\Framework\local::ERROR, 'Invalid Date');
                    $error = TRUE;
                }
                else
                {
                    $date = new \DateTime();
                    $date->setDate($year, $month, $day);
                    $date->setTime(0, 0, 0);

                    if ($currentdate <= $date)
                    {
                        $context->local()->message(\Framework\local::ERROR, 'Date must be in the past');
                        $error = TRUE;
                    }
                }
            }

            if ($form->exists('hours'))
            {
                $hours = (int) $form->mustfetch('hours');
                if ($hours <= 0)
                {
                    $context->local()->message(\Framework\local::ERROR, 'Hours must be greater than zero');
                    $error = TRUE;
                }
                if ($hours >  self::HOURSDAY)
                {
                    $context->local()->message(\Framework\local::ERROR, 'Hours for one timelog cannot be greater than 24 hours');
                    $error = TRUE;
                }
            }

            if($form->exists('notetext'))
            {
                $note->text = $form->mustFetch('notetext');
            }

            if($form->exists('attachments'))
            {
                $note->files = $form->mustFetchArray('attachments');
                $context->local()->addval('attachments', $note->files);
                $context->local()->addval('projectid', $context->rest()[0]);
                return '@content/addnote.twig';

            }

            if(!$error && ($date != $note->date || $hours != $note->hours))
            {
                $note->date = $date;
                $note->hours = $hours;

                //upload Files once everything else is validated
                $fileform = $context->formData('file');
                $fileError = FALSE;
                foreach ($fileform->fileArray('attachments') as $ix => $fa)
                {
                    $attachment = \R::dispense('upload');
                    if($attachment->savefile($context, $fa, FALSE, $context->user(), $ix)){
                        $note->ownAttachmentList[] = $attachment;
                    }
                    else
                    {
                        $context->local()->message(\Framework\local::ERROR, $fa['name'] . ' failed to upload');
                        $fileError = TRUE;
                    }
                }
                if($fileError){
                    $context->local()->addval('day', $day);
                    $context->local()->addval('month', $month);
                    $context->local()->addval('year', $year);
                    $context->local()->addval('hours', $hours);
                    $context->local()->addval('formaction', '/project/' . $project->getID() . '/note/add');
                    $context->local()->addval('currentyear', $currentdate->format('Y'));
                }


                $project->ownNoteList[] = $note;

                \R::store($project);

                $context->local()->addval('uploads', $note->ownAttachmentList);
                $context->divert('/project/' . $project->getId());
            }
            else
            {
                $context->local()->addval('day', $day);
                $context->local()->addval('month', $month);
                $context->local()->addval('year', $year);
                $context->local()->addval('hours', $hours);
                $context->local()->addval('formaction', '/project/' . $project->getID() . '/note/add');
                $context->local()->addval('currentyear', $currentdate->format('Y'));
                return '@content/addnote.twig';
            }
        }
    }