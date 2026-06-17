<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>

<nav aria-label="Page navigation" class="d-flex justify-content-center">
    <ul class="pagination justify-content-center mb-0 mt-3 shadow-sm rounded-pill overflow-hidden bg-white py-1 px-2 border align-items-center">
        <?php if ($pager->hasPrevious()) : ?>
            <li class="page-item">
                <a class="page-link border-0 text-primary rounded-circle mx-1 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" href="<?= $pager->getFirst() ?>" aria-label="<?= lang('Pager.first') ?>">
                    <span aria-hidden="true">&laquo;&laquo;</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link border-0 text-primary rounded-circle mx-1 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" href="<?= $pager->getPrevious() ?>" aria-label="<?= lang('Pager.previous') ?>">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link border-0 rounded-circle mx-1 d-flex align-items-center justify-content-center <?= $link['active'] ? 'bg-primary text-white' : 'text-dark hover-light' ?>" style="width: 38px; height: 38px;" href="<?= $link['uri'] ?>">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNext()) : ?>
            <li class="page-item">
                <a class="page-link border-0 text-primary rounded-circle mx-1 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" href="<?= $pager->getNext() ?>" aria-label="<?= lang('Pager.next') ?>">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link border-0 text-primary rounded-circle mx-1 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" href="<?= $pager->getLast() ?>" aria-label="<?= lang('Pager.last') ?>">
                    <span aria-hidden="true">&raquo;&raquo;</span>
                </a>
            </li>
        <?php endif ?>
    </ul>
</nav>

<style>
.hover-light:hover {
    background-color: #f1f3f5 !important;
}
.page-item.active .page-link {
    background-color: var(--bs-primary) !important;
    border-color: var(--bs-primary) !important;
    color: white !important;
}
</style>
