<ul class="sortable">
<li class="element">
    <div class="sortable-item">
        <?= $this->formText(
            htmlspecialchars($element_name_name), htmlspecialchars($element_name_value),
            array('placeholder' => __('New Element Name'))
        );?>
        <?= $this->formHidden(
            htmlspecialchars($element_order_name), htmlspecialchars($element_order_value),
            array('class' => 'element-order')
        );
        ?>
    </div>
    <div class="drawer-contents">
        <?= $this->formLabel(htmlspecialchars($element_description_name), __('Description')); ?>
        <?= $this->formTextarea(
            htmlspecialchars($element_description_name), htmlspecialchars($element_description_value),
            array(
                'placeholder' => __('Add the description of the element, which will not be changed.'),
                'rows' => '3',
                'cols'=>'30'
            )
        ); ?>
        <?= $this->formLabel(htmlspecialchars($element_comment_name), __('Comment'));?>
        <?= $this->formTextarea(
            htmlspecialchars($element_comment_name), htmlspecialchars($element_comment_value),
            array(
                'placeholder' => __('Element Comment'),
                'rows' => '3',
                'cols'=>'30'
            )
        ); ?>
        <?= $this->formLabel(htmlspecialchars($element_unique_name), __('Unrepeatable'));?>
        <?= $this->formCheckbox(htmlspecialchars($element_unique_name), true,
            array('checked' => htmlspecialchars($element_unique_value)));?>
        <?= $this->formLabel(htmlspecialchars($element_steppable_name), __('Steps of a workflow'));?>
        <?= $this->formCheckbox(htmlspecialchars($element_steppable_name), true,
            array('checked' => htmlspecialchars($element_steppable_value)));?>

        <?= $this->formTextarea(
            htmlspecialchars($element_terms_name), htmlspecialchars($element_terms_value),
            array(
                'placeholder' => __('Ordered list of concise terms, one by line'),
                'rows' => '5',
                'cols'=>'10'
            )
        );?>

        <?= $this->formLabel(htmlspecialchars($element_default_name), __('Default term'));?>
        <?= $this->formText(
            htmlspecialchars($element_default_name), htmlspecialchars($element_default_value),
            array(
                'placeholder' => __('The default term to use for new items, or let empty'),
            )
        ); ?>
    </div>
</li>
</ul>
