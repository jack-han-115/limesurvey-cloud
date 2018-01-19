<?php
/**
 * Month dropdown Html
 * @var $monthId
 * @var $currentmonth
 * @var $montharray
 */
?>

<!-- month -->
<label for="month<?php echo $monthId; ?>" class="sr-only">
    <?php eT('Month'); ?>
</label>
<select id="month<?php echo $monthId; ?>" name="month<?php echo $monthId; ?>" class="month form-control">
    <option value="">
        <?php eT('Month'); ?>
    </option>

    <?php for ($i=1; $i<=12; $i++):?>
        <option value="<?php echo sprintf('%02d', $i); ?>" <?php if ($i == $currentmonth):?>SELECTED<?php endif; ?>>
            <?php echo $montharray[$i-1]; ?>
        </option>
    <?php endfor;?>
</select>

<!-- end of month -->
