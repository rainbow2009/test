</div><!--.vg-main.vg-right-->
</div><!--.vg-carcass-->
<div class="vg-modal vg-center">

    <? if (isset($_SESSION['res'])) {
        unset($_SESSION['res']);
    }
    ?>

</div>
<script>
    const PATH = '<?=PATH?>'
    const ADMIN_MODE = '1';
</script>
<?php $this->getScripts(); ?>
</body>
</html>