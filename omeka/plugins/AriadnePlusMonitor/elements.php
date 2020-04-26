<?php
$elementSetMetadata = array(
    'name' => 'Monitor',
    'description' => 'Metadata used to manage various status about items.',
    'record_type' => null,
    'elements' => array(
        array(
            'name' => 'metadata-status',
            'label' => 'Metadata Status',
            'description' => 'Main status of metadata of the record, set by the staff member that created the document metadata/record.',
            'comment' => '',
            'unique' => true,
            'terms' => array(
                'Proposed',
                'Incomplete',
                'Complete',
                'Mapped',
                'Enriched',
                'Ready to Publish',
                'Published',
            ),
            'steppable' => true,
            'default' => '',
        ),
        array(
            'name' => 'map-url',
            'label' => 'ID of your metadata transformation',
            'description' => 'You have to define a mapping with the X3ML mapping tool​',
            'comment' => '',
            'unique' => true,
        ),
        array(
            'name' => 'periodo-url',
            'label' => 'URL of your PeriodO collection',
            'description' => 'You can enrich your metadata with Period0.',
            'comment' => '',
            'unique' => true,
        ),
        array(
            'name' => 'gettyaat-mapping',
            'label' => 'GettyAAT mapping',
            'description' => '',
            'comment' => '',
            'unique' => true,
            
        ),
    ),
);
