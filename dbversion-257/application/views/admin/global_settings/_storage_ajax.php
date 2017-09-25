<?php

/**
 * @since 2017-09-20
 * @author Olle Härstedt
 */

?>

<label><?php eT('Overview'); ?></label>
<table class='table table-striped table-bordered'>
    <tfoot>
        <tr>
            <td style='width: 70%;'><?php eT('Total storage'); ?></td>
            <td><?php echo $totalStorage; ?></td>
        </tr>
    </tfoot>
    <tbody>
        <tr>
            <td><?php eT('Survey storage'); ?></td>
            <td><?php echo $surveySize; ?></td>
        </tr>
        <tr>
            <td><?php eT('Template storage'); ?></td>
            <td><?php echo $templateSize; ?></td>
        </tr>
        <tr>
            <td><?php eT('Label set storage'); ?></td>
            <td><?php echo $labelSize; ?></td>
        </tr>
    </tbody>
</table>


<?php if ($surveys): ?>
    <label><?php eT('Survey storage'); ?></label>
    <table class='table table-striped table-bordered'>
        <?php foreach ($surveys as $survey): ?>
        <tr>
            <td style='width: 70%;'><?php echo $survey['name']; ?> (<?php echo $survey['sid']; ?>)</td>
            <td><?php echo $survey['size']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<?php if ($templates): ?>
    <label><?php eT('Template storage'); ?></label>
    <table class='table table-striped table-bordered'>
        <?php foreach ($templates as $templates): ?>
        <tr>
            <td style='width: 70%;'><?php echo $templates['name']; ?></td>
            <td><?php echo $templates['size']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
