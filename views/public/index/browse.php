<?php
$pageTitle = __('References');
echo head(array(
    'title' => $pageTitle,
    'bodyclass' => 'reference',
));
?>
<div id="primary">
    <h1><?php echo $pageTitle; ?></h1>
    <?php if (empty($types)): ?>
        <p><?php echo __('No references available.'); ?></p>
    <?php else: ?>
        <ul class='references'>
        <?php
    if (count($types) == 1):
        foreach ($references as $slug => $slugData): ?>
            <li><?php echo sprintf('<a href="%s" title="%s">%s (%d)</a>',
                url('references/' . $slug),
                __('Browse %s', $slugData['label']),
                $slugData['label'],
                $this->referenceCount($slug)); ?>
            </li>
        <?php endforeach;
    else:
        // References are ordered: Item Types, then Elements.
        $type = null;
        $first = true;
        foreach ($references as $slug => $slugData):
            $changedType = $slugData['type'] != $type;
            if ($changedType):
                if ($first):
                    $first = false;
                else: ?>
                    </ul></li>
                <?php endif; ?>
            <li><?php
                echo $slugData['type'] == 'ItemType' ?  __('Main Types of Items') : __('Metadata');
                $type = $slugData['type'];
            ?><ul>
            <?php endif; ?>
            <li><?php echo sprintf('<a href="%s" title="%s">%s (%d)</a>',
                url('references/' . $slug),
                __('Browse %s', $slugData['label']),
                $slugData['label'],
                $this->referenceCount($slug)); ?>
            </li>
        <?php endforeach; ?>
        </ul></li>
    <?php endif;
    endif;
    ?>
    </ul>
</div>
<?php echo foot();
