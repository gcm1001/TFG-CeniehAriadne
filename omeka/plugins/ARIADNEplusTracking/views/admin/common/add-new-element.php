<ul class="sortable">
<li class="element">
    <div class="sortable-item">
        <?php
        echo $this->formText(
            $element_name_name, $element_name_value,
            array('placeholder' => __('New Element Name'))
        );
        echo $this->formHidden(
            $element_order_name, $element_order_value,
            array('class' => 'element-order')
        );
        ?>
    </div>
    <div class="drawer-contents">
        <?php
        echo $this->formLabel($element_description_name, __('Description'));
        echo $this->formTextarea(
            $element_description_name, $element_description_value,
            array(
                'placeholder' => __('Add the description of the element, which will not be changed.'),
                'rows' => '3',
                'cols'=>'30'
            )
        );
        echo $this->formLabel($element_comment_name, __('Comment'));
        echo $this->formTextarea(
            $element_comment_name, $element_comment_value,
            array(
                'placeholder' => __('Element Comment'),
                'rows' => '3',
                'cols'=>'30'
            )
        );
        echo $this->formLabel($element_unique_name, __('Unrepeatable'));
        echo $this->formCheckbox($element_unique_name, true,
            array('checked' => $element_unique_value));
        echo $this->formLabel($element_steppable_name, __('Steps of a workflow'));
        echo $this->formCheckbox($element_steppable_name, true,
            array('checked' => $element_steppable_value));

        echo $this->formTextarea(
            $element_terms_name, $element_terms_value,
            array(
                'placeholder' => __('Ordered list of concise terms, one by line'),
                'rows' => '5',
                'cols'=>'10'
            )
        );

        echo $this->formLabel($element_default_name, __('Default term'));
        echo $this->formText(
            $element_default_name, $element_default_value,
            array(
                'placeholder' => __('The default term to use for new items, or let empty'),
            )
        );
        ?>
    </div>
</li>
</ul>
