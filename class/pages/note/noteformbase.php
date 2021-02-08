<?php
/**
* Contains definition of NoteFormBase abstract class
*
* @author James Moran <j.moran3@ncl.ac.uk>
* @package Pages
* @subpackage Note
*/

    namespace Pages\Note;

    use RedBeanPHP\OODBBean;
    use Support\Context;

/**
* A class that contains code to handle Add snf Update requests for Note object.
*
* User is authorised to access project in project.php so no validation needed here
* Note is confirmed to belong to Project {projectid} in note.php so no validation needed here
*/
    abstract class NoteFormBase
    {

        private const HOURSDAY = 24;

/**
* Handle returning project details to user
*
* @param Context $context The context object for the site
* @param OODBBean $project project note related to
* @param OODBBean $note Note to be changed
* @param $template
* @return string|array   A template name
*/
         public static function handleForm(Context $context, OODBBean $project, OODBBean $note, $template)
         {
             $currentdate = new \DateTime();

             $form = $context->formData('post');
             $date = null;
             $hours = 0;
             $text = '';

             $error = FALSE;

             $day = '';
             $month = '';
             $year = '';

             if (!empty($note->date))
             {
                 $notedate = \DateTime::createFromFormat('Y-m-d H:i:s', $note->date);
                 $day = $notedate->format('d');
                 $month = $notedate->format('m');
                 $year = $notedate->format('Y');
             }
             if ($form->exists('day') && $form->exists('month') && $form->exists('year'))
             {
                 $day = (int) $form->mustFetch('day');
                 $month = (int) $form->mustFetch('month');
                 $year = (int) $form->mustFetch('year');

                 $error = self::validateDateField($context,$day, $month, $year, $currentdate);
                 $date = new \DateTime();
                 $date->setDate($year, $month, $day);
                 $date->setTime(0, 0, 0);
             }
             if ($form->exists('hours'))
             {
                 $hours = (int) $form->mustfetch('hours');
                 $error = self::validateHoursField($context, $hours);
             }
             if ($form->exists('notetext'))
             {
                 $text = $form->mustFetch('notetext');
             }

             if (!$error && ($date != $note->date || $hours != $note->hours || $text != $note->text) && (!empty($hours) && !empty($date)))
             {
                 $note->date = $date;
                 $note->hours = $hours;
                 $note->text = $text;

                 //upload Files once everything else is validated
                 $fileform = $context->formData('file');
                 $fileError = FALSE;

                 foreach ($fileform->fileArray('attachments') as $ix => $fa)
                 {
                     if (!empty($fa['name']))
                     {
                         $attachment = \R::dispense('upload');
                         if ($attachment->savefile($context, $fa, FALSE, $context->user(), $ix))
                         {
                             $note->ownAttachmentList[] = $attachment;
                         }
                         else
                         {
                             $context->local()->message(\Framework\local::ERROR, $fa['name'] . ' failed to upload');
                             $fileError = TRUE;
                         }
                     }
                 }
                 if ($fileError)
                 {
                     $context->local()->addval('day', $day);
                     $context->local()->addval('month', $month);
                     $context->local()->addval('year', $year);
                     $context->local()->addval('note', $note);
                     $context->local()->addval('project', $project);
                     $context->local()->addval('currentyear', $currentdate->format('Y'));
                     return $template;
                 }

                 $project->ownNoteList[] = $note;

                 \R::store($project);

                 $context->divert('/project/' . $project->getId());
             }
             else
             {
                 $context->local()->addval('day', $day);
                 $context->local()->addval('month', $month);
                 $context->local()->addval('year', $year);
                 $context->local()->addval('note', $note);
                 $context->local()->addval('project', $project);
                 $context->local()->addval('currentyear', $currentdate->format('Y'));
                 $context->local()->addval('fileArray', $context->formData('file')->fileArray('attachments'));
                 return $template;
             }
         }

/**
* Check provided values create a valid date
*
* @param Context $context The context object for the site
* @param int $day day of input date
* @param int $month month of input date
* @param int $year year of input date
* @param \DateTime $currentdate current date
* @return string|array   A template name
*/
         private static function validateDateField(Context $context, int $day, int $month, int $year, \DateTime $currentdate): bool
         {
             $error = FALSE;
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
             return $error;
         }

/**
* Check provided values create a valid date
*
* @param Context $context The context object for the site
* @param int $hours input of hours worked
* @return string|array   A template name
*/
         private static function validateHoursField(Context $context, int $hours): bool
         {
             $error = FALSE;
             if ($hours <= 0)
             {
                 $context->local()->message(\Framework\local::ERROR, 'Hours must be greater than zero');
                 $error = TRUE;
             }
             if ($hours > self::HOURSDAY)
             {
                 $context->local()->message(\Framework\local::ERROR, 'Hours for one timelog cannot be greater than 24 hours');
                 $error = TRUE;
             }
             return $error;
         }
    }