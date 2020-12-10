<?php

namespace App\Handlers\Forms;

use Symfony\Component\Form\FormInterface;

class FormErrorsHandler
{
    public function getErrors(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childErrors = $this->getErrors($childForm)) {
                $errors[$childForm->getName()] = $childErrors;
            }
        }
        return $errors;
    }
}