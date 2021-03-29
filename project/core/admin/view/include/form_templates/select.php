<div class="vg-element vg-full vg-box-shadow">
    <div class="vg-wrap vg-element vg-full vg-box-shadow">
        <div class="vg-element vg-full vg-left">
            <span class="vg-header"><?= $this->translate[$row][0] ?: $row ?></span>
        </div>
        <div class="vg-element vg-full vg-left">
            <span class="vg-text vg-firm-color5"></span><span
                    class="vg_subheader"><?= $this->translate[$row][1] ?: '' ?></span>
        </div>
        <div class="select-wrapper vg-element vg-full vg-left vg-no-offset">
            <div class="select-arrow-3 select-arrow-31"></div>
            <select name="<?= $row ?>" class="vg-input vg-text vg-full vg-firm-color1">
                <?php foreach ($this->foreignData[$row] as $val): ?>
                    <option value="<?= $val['id'] ?>"
                        <?= $this->data[$row] == $val['id'] ? 'selected' : '' ?> >
                        <?= $val['name'] ?>
                    </option>
                <? endforeach ?>
            </select>
        </div>
    </div>
</div>