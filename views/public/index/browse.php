<?php
if (empty($types) || count($types) > 1) {
    $pageTitle = __('References');
} elseif ($types['Element']) {
    $pageTitle = __('Metadata');
} else {
    $pageTitle = __('Item Types');
}
echo head(array(
    'title' => $pageTitle,
    'bodyclass' => 'reference',
));
$reference_hide_empty = (bool) get_option('reference_hide_empty');
$reference_show_count = (bool) get_option('reference_show_count');
?>
<div id="primary">
    <h1><?php echo $pageTitle; ?></h1>
    <nav class="items-nav navigation secondary-nav">
        <?php echo public_nav_items(); ?>
    </nav>
    <?php if (empty($types)): ?>
        <p><?php echo __('No reference available.'); ?></p>
    <?php else: ?>
    <div id="reference-headings">
        <ul class="reference-list" style="margin-top: 16px;">
        <?php
        // References are ordered: Item Types, then Elements.
        $type = null;
        $first = true;
        foreach ($references as $slug => $slugData):
            $changedType = $slugData['type'] != $type;
            if ($changedType):
                if ($first):
                    $first = false;
                else:
                    echo "</ul>";
                                echo "<ul class='reference-list' style='margin-top: 16px;'>";
                endif;
            ?>
            <li>
            <?php
                echo '<h3 class="reference-heading">' . ($slugData['type'] == 'ItemType' ?  __('Main Item Types') : __('Metadata')) . '</h3>';
                $type = $slugData['type'];
            ?>
            </li>
        <?php endif; ?>

        <?php
            if (!$reference_hide_empty || $this->reference()->count($slug) > 0) {
                echo '<li class="reference-record">';
                if ($reference_show_count) {
                    echo sprintf(
                        '<a href="%s" title="%s">%s</a> (%d)',
                        html_escape(url(array('slug' => $slug), 'reference_list')),
                        __('Browse %s', $slugData['label']),
                        __($slugData['label']),
                        $this->reference()->count($slug)
                    );
                } else {
                    echo sprintf(
                        '<a href="%s" title="%s">%s</a>',
                        html_escape(url(array('slug' => $slug), 'reference_list')),
                        __('Browse %s', $slugData['label']),
                        __($slugData['label'])
                    );
                }
                echo '</li>';
            }
        ?>
        <?php endforeach; ?>
        </ul></li>
    </ul>
    <?php endif; ?>
</div>
<?php echo foot();
