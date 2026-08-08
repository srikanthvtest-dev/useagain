<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/image-upload.php';

if (!isUserLoggedIn()) {
    header('Location: ' . BASE_URL . '/login.php?redirect=' . urlencode('/repair-request-edit.php?id=' . (int) ($_GET['id'] ?? 0)));
    exit;
}

$user = currentUser();
$requestId = (int) ($_GET['id'] ?? $_POST['request_id'] ?? 0);
$request = $requestId ? getRepairRequestById($requestId) : null;

// Owner-only, and only while still unassigned — once a technician is
// assigned the request becomes read-only (matches repair-request-view.php).
if (!$request || (int) $request['user_id'] !== (int) $user['id']) {
    header('Location: ' . BASE_URL . '/my-account.php#repair-requests');
    exit;
}
if ($request['status'] !== 'submitted') {
    header('Location: ' . BASE_URL . '/repair-request-view.php?id=' . $requestId);
    exit;
}

$categories = getCategories();
$errors = [];

$old = [
    'category_id' => $request['category_id'], 'subcategory_id' => $request['subcategory_id'],
    'title' => $request['title'], 'description' => $request['description'],
    'address' => $request['address'], 'area' => $request['area'], 'city' => $request['city'],
    'state' => $request['state'], 'pincode' => $request['pincode'],
    'contact_number' => $request['contact_number'], 'whatsapp_number' => $request['whatsapp_number'],
];
$selectedProblemIds = array_column(getRepairRequestProblems($requestId), 'id');
$existingImages = getRepairRequestImages($requestId, 'customer');

$subcategories = getSubcategories($old['category_id']);
$problems = $old['subcategory_id'] ? getRepairProblemsForSubcategory($old['subcategory_id']) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['category_id', 'subcategory_id', 'title', 'description', 'address', 'area', 'city', 'state', 'pincode',
              'contact_number', 'whatsapp_number'] as $field) {
        $old[$field] = trim($_POST[$field] ?? '');
    }
    $selectedProblemIds = array_map('intval', $_POST['problem_ids'] ?? []);
    $subcategories = getSubcategories((int) $old['category_id']);

    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Your session expired — please try submitting the form again.';
    } elseif (isRateLimited('edit_repair_request')) {
        $errors[] = 'Please wait a few seconds before trying again.';
    } elseif ($request['status'] !== 'submitted') {
        // Re-check at submit time too — status could have changed between page load and submit.
        $errors[] = 'This request has already been assigned to a technician and can no longer be edited.';
    } else {
        $category = getCategoryById($old['category_id']);
        $subcategory = $category ? getSubcategoryForCategory($old['subcategory_id'], $category['id']) : null;

        if (!$category) $errors[] = 'Please select what kind of item needs repair.';
        if ($category && !$subcategory) $errors[] = 'Please select a sub category.';
        if (mb_strlen($old['title']) < 5) $errors[] = 'Please briefly name the item (e.g. "Washing machine not spinning").';
        if ($old['address'] === '') $errors[] = 'Please enter an address.';
        if ($old['area'] === '') $errors[] = 'Please enter an area/locality.';
        if ($old['city'] === '') $errors[] = 'Please enter a city.';
        if ($old['state'] === '') $errors[] = 'Please enter a state.';
        if (!preg_match('/^[0-9]{4,10}$/', $old['pincode'])) $errors[] = 'Please enter a valid pincode.';
        if (!preg_match('/^[0-9+\-\s]{7,15}$/', $old['contact_number'])) $errors[] = 'Please enter a valid contact number.';
        if (!preg_match('/^[0-9+\-\s]{7,15}$/', $old['whatsapp_number'])) $errors[] = 'Please enter a valid WhatsApp number.';

        if ($subcategory) {
            $problems = getRepairProblemsForSubcategory($subcategory['id']);
            $validProblemIds = array_column($problems, 'id');
            $selectedProblemIds = array_values(array_intersect($selectedProblemIds, $validProblemIds));
            if (empty($selectedProblemIds) && empty($errors)) {
                $errors[] = 'Please select at least one common problem, or add details in the description if yours isn\'t listed.';
            }
        }

        $uploadedImages = [];
        $remainingSlots = MAX_IMAGES_PER_PRODUCT - count($existingImages);
        if (empty($errors) && $remainingSlots > 0 && !empty($_FILES['images']['name'][0])) {
            $uploadedImages = handleProductImageUploads($_FILES['images'], $errors);
        }

        if (empty($errors)) {
            updateRepairRequest($requestId, [
                'category_id'     => $category['id'],
                'subcategory_id'  => $subcategory['id'],
                'title'           => $old['title'],
                'description'     => $old['description'] ?: null,
                'address'         => $old['address'],
                'area'            => $old['area'],
                'city'            => $old['city'],
                'state'           => $old['state'],
                'pincode'         => $old['pincode'],
                'contact_number'  => $old['contact_number'],
                'whatsapp_number' => $old['whatsapp_number'],
            ]);

            replaceRepairRequestProblems($requestId, $selectedProblemIds);

            foreach ($uploadedImages as $img) {
                addRepairRequestImage($requestId, $img, 'customer');
            }

            header('Location: ' . BASE_URL . '/repair-request-view.php?id=' . $requestId . '&updated=1');
            exit;
        }
    }
}

$pageTitle = 'Edit Repair Request - UseAgain';
require_once __DIR__ . '/includes/header.php';
?>

<section style="padding-top:30px; padding-bottom:80px;">
  <div class="container" style="max-width:760px;">
    <a href="<?= BASE_URL ?>/repair-request-view.php?id=<?= $requestId ?>" class="small">&larr; Back to request</a>

    <h1 class="section-title" style="text-align:left; margin-top:16px;">Edit Repair Request</h1>
    <p class="section-sub" style="text-align:left; margin-bottom:12px;">You can edit this until a technician is assigned.</p>

    <div class="repair-estimate-note">
      ℹ️ The quotation is an estimate. The final quotation will be provided after inspection.
    </div>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error" style="margin-top:20px;">
        <ul style="margin:0 0 0 20px;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>

    <form class="form-card" method="post" action="<?= BASE_URL ?>/repair-request-edit.php?id=<?= $requestId ?>" enctype="multipart/form-data" style="margin-top:20px;">
      <?= csrf_field() ?>
      <input type="hidden" name="request_id" value="<?= $requestId ?>">

      <div class="form-row">
        <div class="form-group">
          <label for="category_id">Category *</label>
          <select id="category_id" name="category_id" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= (string) $old['category_id'] === (string) $cat['id'] ? 'selected' : '' ?>><?= $cat['icon'] ?> <?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="subcategory_id">Sub Category *</label>
          <select id="subcategory_id" name="subcategory_id" required>
            <option value="">Select Sub Category</option>
            <?php foreach ($subcategories as $sub): ?>
              <option value="<?= $sub['id'] ?>" <?= (string) $old['subcategory_id'] === (string) $sub['id'] ? 'selected' : '' ?>><?= htmlspecialchars($sub['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label>Common Problems *</label>
        <div id="problemsHelp" class="small-muted" style="margin-bottom:8px; display:none;">Select category and sub category first.</div>
        <div id="problemsList" class="problems-checklist">
          <?php foreach ($problems as $p): ?>
            <label class="problem-checkbox">
              <input type="checkbox" name="problem_ids[]" value="<?= $p['id'] ?>" <?= in_array((int) $p['id'], $selectedProblemIds, true) ? 'checked' : '' ?>>
              <?= htmlspecialchars($p['title']) ?>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="form-group">
        <label for="title">Short summary *</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($old['title']) ?>" required>
      </div>

      <div class="form-group">
        <label for="description">Additional Description (optional)</label>
        <textarea id="description" name="description" rows="3"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
      </div>

      <?php if ($existingImages): ?>
        <div class="form-group">
          <label>Current Photos</label>
          <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <?php foreach ($existingImages as $img): ?><img src="<?= htmlspecialchars($img['url']) ?>" style="width:90px; height:70px; object-fit:cover; border-radius:8px;"><?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <?php if (count($existingImages) < MAX_IMAGES_PER_PRODUCT): ?>
        <div class="form-group">
          <label>Add More Photos (optional, up to <?= MAX_IMAGES_PER_PRODUCT - count($existingImages) ?> more)</label>
          <div class="upload-dropzone" id="uploadDropzone">
            <p>Drag &amp; drop photos here, or click to browse</p>
            <input type="file" name="images[]" id="imagesInput" accept="image/jpeg,image/png,image/webp" multiple hidden>
          </div>
          <div class="small-muted" id="imageCount" style="margin-top:8px;">0 photos selected</div>
          <div class="image-preview-grid" id="imagePreviewGrid"></div>
        </div>
      <?php endif; ?>

      <div class="form-group">
        <label for="address">Address *</label>
        <input type="text" id="address" name="address" value="<?= htmlspecialchars($old['address']) ?>" required>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="area">Area *</label>
          <input type="text" id="area" name="area" value="<?= htmlspecialchars($old['area']) ?>" required>
        </div>
        <div class="form-group">
          <label for="city">City *</label>
          <input type="text" id="city" name="city" value="<?= htmlspecialchars($old['city']) ?>" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="state">State *</label>
          <input type="text" id="state" name="state" value="<?= htmlspecialchars($old['state']) ?>" required>
        </div>
        <div class="form-group">
          <label for="pincode">Pincode *</label>
          <input type="text" id="pincode" name="pincode" value="<?= htmlspecialchars($old['pincode']) ?>" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="contact_number">Contact Number *</label>
          <input type="tel" id="contact_number" name="contact_number" value="<?= htmlspecialchars($old['contact_number']) ?>" required>
        </div>
        <div class="form-group">
          <label for="whatsapp_number">WhatsApp Number *</label>
          <input type="tel" id="whatsapp_number" name="whatsapp_number" value="<?= htmlspecialchars($old['whatsapp_number']) ?>" required>
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
    </form>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var categorySelect = document.getElementById('category_id');
  var subcategorySelect = document.getElementById('subcategory_id');
  var problemsList = document.getElementById('problemsList');
  var problemsHelp = document.getElementById('problemsHelp');
  var preselectSubcategory = '<?= (int) $old['subcategory_id'] ?>';
  var initialLoad = true;

  function loadSubcategories(categoryId, preselect) {
    subcategorySelect.innerHTML = '<option value="">Loading...</option>';
    if (!categoryId) {
      subcategorySelect.innerHTML = '<option value="">Select category first</option>';
      return;
    }
    fetch('<?= BASE_URL ?>/ajax-subcategories.php?category_id=' + encodeURIComponent(categoryId))
      .then(function (r) { return r.json(); })
      .then(function (data) {
        subcategorySelect.innerHTML = '<option value="">Select Sub Category</option>';
        (data.subcategories || []).forEach(function (sub) {
          var opt = document.createElement('option');
          opt.value = sub.id;
          opt.textContent = sub.name;
          if (preselect && String(sub.id) === String(preselect)) opt.selected = true;
          subcategorySelect.appendChild(opt);
        });
        if (preselect) loadProblems(preselect, true);
      });
  }

  function loadProblems(subcategoryId, keepChecked) {
    var checkedIds = keepChecked ? Array.from(problemsList.querySelectorAll('input:checked')).map(function (el) { return el.value; }) : [];
    problemsList.innerHTML = '<p class="small-muted">Loading...</p>';
    if (!subcategoryId) {
      problemsList.innerHTML = '';
      problemsHelp.style.display = 'block';
      return;
    }
    fetch('<?= BASE_URL ?>/ajax-repair-problems.php?subcategory_id=' + encodeURIComponent(subcategoryId))
      .then(function (r) { return r.json(); })
      .then(function (data) {
        var list = data.problems || [];
        problemsList.innerHTML = '';
        if (!list.length) {
          problemsList.innerHTML = '<p class="small-muted">No common problems listed for this sub category yet — just describe it below.</p>';
          problemsHelp.style.display = 'none';
          return;
        }
        problemsHelp.style.display = 'none';
        list.forEach(function (p) {
          var label = document.createElement('label');
          label.className = 'problem-checkbox';
          var checked = checkedIds.indexOf(String(p.id)) !== -1 ? 'checked' : '';
          label.innerHTML = '<input type="checkbox" name="problem_ids[]" value="' + p.id + '" ' + checked + '> ' + p.title.replace(/</g, '&lt;');
          problemsList.appendChild(label);
        });
      });
  }

  categorySelect.addEventListener('change', function () { loadSubcategories(this.value, null); });
  subcategorySelect.addEventListener('change', function () { loadProblems(this.value, false); });

  // On first load, the server already rendered the correct subcategory
  // options and checked problem checkboxes for the existing request, so
  // we don't need to re-fetch anything unless the user changes a dropdown.
  initialLoad = false;

  // ---- Photo upload preview / drag-drop (only present if slots remain) ----
  var dropzone = document.getElementById('uploadDropzone');
  var input = document.getElementById('imagesInput');
  if (dropzone && input) {
    var grid = document.getElementById('imagePreviewGrid');
    var counter = document.getElementById('imageCount');
    var maxFiles = <?= MAX_IMAGES_PER_PRODUCT - count($existingImages) ?>;
    var selected = [];

    dropzone.addEventListener('click', function (e) { if (e.target !== input) input.click(); });
    ['dragenter', 'dragover'].forEach(function (evt) {
      dropzone.addEventListener(evt, function (e) { e.preventDefault(); dropzone.classList.add('is-dragover'); });
    });
    ['dragleave', 'drop'].forEach(function (evt) {
      dropzone.addEventListener(evt, function (e) { e.preventDefault(); dropzone.classList.remove('is-dragover'); });
    });
    dropzone.addEventListener('drop', function (e) { addFiles(e.dataTransfer.files); });
    input.addEventListener('change', function (e) { addFiles(e.target.files); });

    function addFiles(fileList) {
      Array.from(fileList || []).forEach(function (file) {
        if (selected.length < maxFiles) selected.push(file);
      });
      render();
      sync();
    }

    function render() {
      grid.innerHTML = '';
      selected.forEach(function (file, index) {
        var reader = new FileReader();
        reader.onload = function (ev) {
          var cell = document.createElement('div');
          cell.className = 'image-preview';
          cell.innerHTML = '<img src="' + ev.target.result + '" alt=""><button type="button" class="remove-btn" data-i="' + index + '">&times;</button>';
          grid.appendChild(cell);
          cell.querySelector('.remove-btn').addEventListener('click', function () {
            selected.splice(index, 1); render(); sync();
          });
        };
        reader.readAsDataURL(file);
      });
      counter.textContent = selected.length + ' / ' + maxFiles + ' photos selected';
    }

    function sync() {
      var dt = new DataTransfer();
      selected.forEach(function (f) { dt.items.add(f); });
      input.files = dt.files;
    }
  }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
