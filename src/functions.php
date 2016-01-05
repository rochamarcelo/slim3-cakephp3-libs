<?php
function getAnimeValidationErrors(array $anime = array())
{
    $validator = new \Cake\Validation\Validator();

    $validator->requirePresence('name')
        ->notEmpty('name', 'We need the anime name.');

    $validator->requirePresence('episodes')
        ->notEmpty('episodes', 'We need the episodes number.')
        ->add('episodes', [
            'naturalNumber' => [
                'rule' => ['naturalNumber'],
                'message' => 'We need a valid episodes number',
            ]
        ]);

    return $validator->errors($anime);
}