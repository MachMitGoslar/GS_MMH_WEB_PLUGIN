<?php
$layout = $block->content()->get('layout')->or('standard')->value();
$entries = $block->content()->get('entries')->toStructure();
?>

<?php if ($layout === 'constrained') : ?>
    <!-- CONSTRAINED VERSION -->
    <div class="timeline-block timeline-block--compact c-project-timeline">

        <?php foreach ($entries as $entry) : ?>
            <?php $image = $entry->image()->toFile(); ?>

            <div class="c-project-timeline-card">

                <div class="info">

                    <div class="headline">
                        <h3 class="title">
                            <?= $entry->year()->html() ?>
                        </h3>
                    </div>

                    <div class="text">
                        <?= $entry->summary()->kt() ?>
                    </div>

                    <?php if ($image) : ?>
                        <div class="image">
                            <img src="<?= $image->url() ?>" alt="<?= $entry->year()->esc() ?>">
                        </div>
                    <?php endif ?>

                </div>

            </div>
        <?php endforeach ?>

    </div>

<?php else : ?>
    <!-- STANDARD VERSION (dein bestehendes CSS) -->
    <div class="timeline-block timeline-block--standard c-project-timeline">

        <?php foreach ($entries as $entry) : ?>
            <?php $image = $entry->image()->toFile(); ?>

            <div class="c-project-timeline-card">

                <div class="info">

                    <div class="headline">
                        <h3 class="title">
                            <?= $entry->year()->html() ?>
                        </h3>
                    </div>

                    <div class="text">
                        <?= $entry->summary()->kt() ?>
                    </div>

                    <?php if ($image) : ?>
                        <div class="image">
                            <img src="<?= $image->url() ?>" alt="<?= $entry->year()->esc() ?>">
                        </div>
                    <?php endif ?>

                </div>

            </div>

        <?php endforeach ?>

    </div>

<?php endif ?>
