<?php
/**
* Contains definition of ProjectFormBase class
*
* @author James Moran <j.moran3@ncl.ac.uk>
* @package Pages
* @subpackage Project
*/
    namespace Pages\Project;


    use RedBeanPHP\OODBBean;
    use Support\Context;
/**
* A class that contains code to handle Add snf Update requests for Note object.
*
* User is authorised to access project in project.php so no validation needed here
*/
    abstract class ProjectFormBase
    {
        private const MAXDESCRIPTIONLENGTH = 250;

/**
* Handle returning project details to user
*
* @param Context $context The context object for the site
* @param OODBBean $project project to be changed
* @param $template
* @return string|array   A template name
*/
        protected static function handleForm(Context $context, OODBBean $project, $template)
        {
            $form = $context->formData('post');

            $nameerror = FALSE;
            $descriptionerror = FALSE;

            $name = '';
            $description = '';

            if ($form->exists('name'))
            {
                $name = $form->mustfetch('name');
                $nameerror = self::validateNameField($context, $project, $name);
            }
            if ($form->exists('description'))
            {
                $description = $form->mustfetch('description');
                $descriptionerror = self::validateDescriptionField($context, $description);
            }
            if (self::validInputWithNewValues($nameerror, $descriptionerror, $name, $description, $project))
            {
                $project->name = $name;
                $project->description = $description;
                $user = $context->user();
                $user->ownProjectList[] = $project;

                \R::store($user);

                $context->divert('/project/' . $project->getID());

            }
            else
            {
                $context->local()->addval('project', $project);
                return $template;
            }
        }

/**
* Check provided values create a valid name
*
* @param Context $context The context object for the site
* @param OODBBean $project project to be changed
* @param string $name name input field value
* @return string|array   A template name
*/
        private static function validateNameField(Context $context, OODBBean $project, string $name): bool
        {
            $error = FALSE;
            if ($name == '')
            {
                $context->local()->message(\Framework\local::ERROR, 'Project name is required');
                $error = TRUE;
            }

            $oldProject = \R::findOne('project', 'user_id = ? && name = ? && id != ?', [$context->user()->getID(), $name, $project->getID()]);
            
            if (!empty($oldProject))
            {
                $context->local()->message(\Framework\local::ERROR, 'Project with this name already exists');
                $error = TRUE;
            }
            return $error;
        }

/**
* Check provided values create a valid description
*
* @param Context $context The context object for the site
* @param string $description description input field value
* @return string|array   A template name
*/
        private static function validateDescriptionField(Context $context, string $description) : bool
        {
            $error = FALSE;

            if ($description == '')
            {
                $context->local()->message(\Framework\local::ERROR, 'Description is required');
                $error = TRUE;
            }
            if (strlen($description) > self::MAXDESCRIPTIONLENGTH)
            {
                $context->local()->message(\Framework\local::ERROR, 'Description must be under 250 characters');
                $error = TRUE;
            }
            return $error;
        }

/**
* Check if errors found in any field
*
* @param bool $nameerror TRUE if name field error
* @param bool $descriptionerror TRUE if description field error
* @param string $name name input field value
* @param string $description description input field value
* @param OODBBean $project project to be changed
* @return string|array   A template name
*/
        private static function validInputWithNewValues(bool $nameerror, bool $descriptionerror, string $name, string $description, OODBBean $project) : bool
        {
            return !$nameerror && !$descriptionerror && ($name != $project->name || $description != $project->description) && (!empty($name) && !empty($description));
        }
    }