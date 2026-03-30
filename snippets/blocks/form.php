<?= ($form = $block->form()->toPage())?->title() ?>
<?php snippet('dreamform/form', [
    'form' => $block->form()->toPage(),
    'attr' => [
        // General attributes
        'form' => ['class' => 'dreamform'],
        'row' => [],
        'column' => [],
        'field' => [ 'class' => 'dreamform-field' ],
        'label' => ['class' => 'dreamform-label'],
        'error' => ['class' => 'dreamform-error'],
        'input' => ['class' => 'dreamform-input'],
        'button' => ['class' => 'gs-c-btn', 'data-type' => 'primary', 'data-size' => 'regular', 'data-style' => 'pill'],

        // Field-specific attributes
        'textarea' => [
            'field' => [],
            'label' => [],
            'error' => [],
            'input' => ['class' => 'dreamform-textarea'],
        ],
        'text' => [
            'field' => [],
            'label' => [],
            'error' => [],
            'input' => ['class' => 'dreamform-input', 'autocomplete' => 'name'],
        ],
        'select' => [
            'field' => [],
            'label' => [],
            'error' => [],
            'input' => ['class' => 'dreamform-select'],
        ],
        'number' => [
            'field' => [],
            'label' => [],
            'error' => [],
            'input' => ['class' => 'dreamform-input'],
        ],
        'file' => [
            'field' => [],
            'label' => [],
            'error' => [],
            'input' => ['class' => 'dreamform-file-upload'],
        ],
        'email' => [
            'field' => [],
            'label' => [],
            'error' => [],
            'input' => ['class' => 'dreamform-input', 'autocomplete' => 'email'],
        ],
        'radio' => [
            'field' => [],
            'label' => [],
            'error' => [],
            'input' => ['class' => 'dreamform-radio'],
            'row' => [],
        ],
        'checkbox' => [
            'field' => [],
            'label' => [],
            'error' => [],
            'input' => ['class' => ''],
            'row' => ['class' => 'dreamform-checkbox'],
        ],

        'success' => [], // Success message
        'inactive' => [], // Inactive message
    ],
]);
