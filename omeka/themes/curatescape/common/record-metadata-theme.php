<?php foreach ($elementsForDisplay as $setName => $setElements): ?>
<div class="element-set">
    <?php if ($showElementSetHeadings): ?>
    <?php endif; ?>
    <?php foreach ($setElements as $elementName => $elementInfo): ?>
    <div id="<?php echo text_to_id(html_escape("$setName $elementName")); ?>" class="metadata">
        <h3><?php echo html_escape(__($elementName)); ?></h3>
        <ul>
        <?php foreach ($elementInfo['texts'] as $text): ?>
            <li><?php echo $text; ?></li>
        <?php endforeach; ?>
        </ul>
    </div><!-- end element -->
    <?php endforeach; ?>
</div><!-- end element-set -->
<?php endforeach;

