<div class="input-group col-12">
    <?php if (isset($this->setting['aFormElementOptions']['inputGroup']['prefix'])) : ?>
        <div class="input-group-addon">
            <?= $this->setting['aFormElementOptions']['inputGroup']['prefix']; ?>
        </div>
    <?php endif; ?>
    <input
        type="number" 
        class="form-control" 
        name="<?= $inputBaseName ?>"
        id="<?= CHtml::getIdByName($inputBaseName); ?>"
        <?= ($this->setting['help']) ? 'aria-describedby="help-' . CHtml::getIdByName($inputBaseName) . '"' : "" ?>
        value="<?= CHtml::encode($this->setting['value']); ?>"
        min="<?= $this->setting['min'] ?? ''?>"
        max="<?= $this->setting['max'] ?? ''?>"
    />
    <?php if (isset($this->setting['aFormElementOptions']['inputGroup']['suffix'])) : ?>
        <div class="input-group-addon">
            <?= $this->setting['aFormElementOptions']['inputGroup']['suffix']; ?>
        </div>
    <?php endif; ?>
</div>
