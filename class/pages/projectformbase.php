<?php


    namespace Pages;


    use Support\Context;

    abstract class ProjectFormBase
    {
        private const MAXDESCRIPTIONLENGTH = 250;

        protected static function handleForm($context, $project, $template){
            $form = $context->formData('post');

            $nameerror = FALSE;
            $descriptionerror = FALSE;

            $name = '';
            $description = '';

            if ($form->exists('name'))
            {
                $name = $form->mustfetch('name');
                $nameerror = self::validateNameField($context, $name);
            }
            if ($form->exists('description'))
            {
                $description = $form->mustfetch('description');
                $descriptionerror = self::validateDescriptionField($context, $description);
            }

            if(self::validInputWithNewValues($nameerror, $descriptionerror, $name, $description, $project))
            {
                $project->name = $name;
                $project->description = $description;
                $user = $context->user();
                $user->ownProjectList[] = $project;

                \R::store($user);

                $context->divert('/');

            } else
            {
                $context->local()->addval('project', $project);
                return $template;
            }
        }

        private static function validateNameField(Context $context, $name): bool {
            $error = FALSE;
            if ($name == '')
            {
                $context->local()->message(\Framework\local::ERROR, 'Project name is required');
                $error = TRUE;
            }

            $oldProject = \R::findOne('project', 'user_id = ? && name = ?', [$context->user()->getID(), $name]);
            
            if(!empty($oldProject))
            {
                $context->local()->message(\Framework\local::ERROR, 'Project with this name already exists');
                $error = TRUE;
            }
            return $error;
        }

        private static function validateDescriptionField(Context $context, $description): bool {
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

        private static function validInputWithNewValues($nameerror, $descriptionerror, $name, $description, $project){
            return !$nameerror && !$descriptionerror && ($name != $project->name || $description != $project->description);
        }

    }