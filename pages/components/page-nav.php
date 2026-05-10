<?php
$navId = $navId ?? 'navbarPageNav';
$navTitle = $navTitle ?? 'CATEGORIES';
$navItems = $navItems ?? [];
$filters = $filters ?? [];
?>
<nav class="navbar navbar-expand-lg navbar-light">
<div class="container">
    <a class="navbar-brand fw-bold" style="font-family: 'Roboto';">&nbsp;</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#<?= e($navId) ?>" aria-controls="<?= e($navId) ?>" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-center" id="<?= e($navId) ?>">
      <ul class="navbar-nav">
        <?php foreach ($navItems as $item): ?>
          <li class="nav-item"<?= !empty($item['status']) ? ' data-status="' . e($item['status']) . '"' : '' ?>>
            <a class="nav-link fw-semibold<?= !empty($item['active']) ? ' active' : '' ?>" href="<?= e($item['href'] ?? '#') ?>"><?= e($item['label']) ?></a>
          </li>
        <?php endforeach; ?>
      </ul>

      <?php if (!empty($filters)): ?>
        <div class="filter-group d-flex align-items-center ms-auto">
          <?php foreach ($filters as $filter): ?>
            <button type="button" class="filter-btn<?= !empty($filter['active']) ? ' active-filter' : '' ?>" onclick="<?= e($filter['onclick'] ?? "filterMenu('{$filter['status']}', this)") ?>"><?= e($filter['label']) ?></button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</nav>

<?php if (!empty($filters)): ?>
<script>
function filterMenu(status, btn) {
  const projectItems = document.querySelectorAll('.project[data-status]');

  if (projectItems.length) {
    projectItems.forEach(item => {
      if (status === 'all' || item.dataset.status === status) {
        item.style.display = 'block';
      } else {
        item.style.display = 'none';
      }
    });
  }

  document.querySelectorAll('.filter-btn').forEach(button => {
    button.classList.remove('active-filter');
  });
  btn.classList.add('active-filter');
}
</script>
<?php endif; ?>
