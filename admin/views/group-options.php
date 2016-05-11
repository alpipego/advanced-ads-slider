<div class="advads-group-slider-options"
    <?php if( 'slider' !== $group->type ) : ?>
        style="display: none;"
    <?php endif; ?>>
    <div>
        <label>
            <strong>
                <?php _e('Slide delay', AAS_SLUG ); ?>
            </strong>
            <input type="number" name="advads-groups[<?php echo $group->id; ?>][options][slider][delay]" value="<?php echo $delay; ?>"/>
        </label>
        <p class="description"><?php _e('Pause for each ad slide in milliseconds', AAS_SLUG); ?></p>
        <br>
        <label>
            <strong>
                <?php _e('Random order', AAS_SLUG ); ?>
            </strong>
            <input type="checkbox" name="advads-groups[<?php echo $group->id; ?>][options][slider][random]"
            <?php if ($random) : ?>
                checked = "checked";
            <?php endif; ?>
            />
        </label>
        <p class="description"><?php _e('Display ads in the slider in a random order', AAS_SLUG); ?></p>
    </div>
</div>