 <div class="vg-element vg-full vg-box-shadow img_wrapper">
    <div class="vg-wrap vg-element vg-full">
        <div class="vg-wrap vg-element vg-full">
            <div class="vg-element vg-full vg-left">
                <span class="vg-header"><?= $this->translate[$row][0] ?: $row ?></span>
            </div>
            <div class="vg-element vg-full vg-left">
                <span class="vg-text vg-firm-color5"></span><span
                        class="vg_subheader"><?= $this->translate[$row][1] ?></span>
            </div>
        </div>
        <a class="vg-wrap vg-element vg-full gallery_container">

            <label class="vg-dotted-square vg-center">
                <img src="<?= PATH . ADMIN_TEMPLATE ?>img/plus.png" alt="plus">
                <input class="gallery_img" style="display: none;" type="file" name="<?= $row ?>[]" multiple>
            </label>

            <?php if ($this->data[$row]): ?>
                <?php $this->data[$row] = json_decode($this->data[$row]); ?>
                <?php foreach ($this->data[$row] as $img): ?>
                    <a href="<?= $this->adminPath . 'delete/' . $this->table . '/' . $this->data[$this->columns['id_row']] . '/' . $row . '/'.base64_encode($img) ?>" class="vg-dotted-square vg-center">
                        <img class="vg_delete" src="<?= PATH . UPLOAD_DIR . $img ?>">
                    </a>
                <?php endforeach ?>
                <?php

                for ($i = 0; $i < 2; $i++) {
                    echo ' <div class="vg-dotted-square vg-center empty_container"></div>';
                }
                ?>
            <? else: ?>
                <?php
                for ($i = 0; $i < 13; $i++) {
                    echo ' <div class="vg-dotted-square vg-center empty_container"></div>';
                }
                ?>

            <? endif ?>

        </div>
    </div>
</div>