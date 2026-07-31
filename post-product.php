<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/image-upload.php';

if (!isUserLoggedIn()) {
    header('Location: ' . BASE_URL . '/login.php?redirect=' . urlencode('/post-product.php'));
    exit;
}

$user = currentUser();
$categories = getCategories();

$errors = [];
$old = [
    'category_id' => '', 'subcategory_id' => '', 'title' => '', 'description' => '', 'brand' => '',
    'condition_type' => 'Good', 'listing_type' => 'sell', 'price' => '', 'is_negotiable' => '',
    'address' => '', 'area' => '', 'city' => '', 'state' => '', 'pincode' => '',
    'contact_number' => $user['phone'], 'whatsapp_number' => $user['whatsapp_number'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['category_id', 'subcategory_id', 'title', 'description', 'brand', 'condition_type', 'listing_type',
              'price', 'address', 'area', 'city', 'state', 'pincode', 'contact_number', 'whatsapp_number'] as $field) {
        $old[$field] = trim($_POST[$field] ?? '');
    }
    $old['is_negotiable'] = isset($_POST['is_negotiable']) ? '1' : '';

    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Your session expired — please try submitting the form again.';
    } elseif (isHoneypotTriggered($_POST)) {
        $errors[] = 'Submission blocked. Please try again.';
    } elseif (isRateLimited('post_product')) {
        $errors[] = 'Please wait a few seconds before trying again.';
    } else {
        $category = getCategoryById($old['category_id']);
        $subcategory = $category ? getSubcategoryForCategory($old['subcategory_id'], $category['id']) : null;

        if (!$category) $errors[] = 'Please select a valid main category.';
        if ($category && !$subcategory) $errors[] = 'Please select a valid sub category.';
        if (mb_strlen($old['title']) < 5) $errors[] = 'Please enter a title of at least 5 characters.';
        if (mb_strlen($old['description']) < 20) $errors[] = 'Please write a description of at least 20 characters.';
        if (!in_array($old['condition_type'], ['New', 'Like New', 'Good', 'Fair'], true)) $errors[] = 'Please select a valid condition.';
        if (!in_array($old['listing_type'], ['sell', 'donate'], true)) $old['listing_type'] = 'sell';
        if ($old['listing_type'] === 'sell' && ($old['price'] === '' || !is_numeric($old['price']) || (float) $old['price'] < 0)) {
            $errors[] = 'Please enter a valid price, or switch to Donate.';
        }
        if ($old['address'] === '') $errors[] = 'Please enter an address.';
        if ($old['area'] === '') $errors[] = 'Please enter an area/locality.';
        if ($old['city'] === '') $errors[] = 'Please enter a city.';
        if ($old['state'] === '') $errors[] = 'Please enter a state.';
        if (!preg_match('/^[0-9]{4,10}$/', $old['pincode'])) $errors[] = 'Please enter a valid pincode.';
        if (!preg_match('/^[0-9+\-\s]{7,15}$/', $old['contact_number'])) $errors[] = 'Please enter a valid contact number.';
        if (!preg_match('/^[0-9+\-\s]{7,15}$/', $old['whatsapp_number'])) $errors[] = 'Please enter a valid WhatsApp number.';

        $uploadedImages = [];
        if (empty($errors)) {
            $uploadedImages = handleProductImageUploads($_FILES['images'] ?? ['name' => []], $errors);
        }

        if (empty($errors)) {
            $slug = uniqueProductSlug($old['title']);
            $productId = createProduct([
                'user_id'         => $user['id'],
                'category_id'     => $category['id'],
                'subcategory_id'  => $subcategory['id'],
                'title'           => $old['title'],
                'slug'            => $slug,
                'description'     => $old['description'],
                'brand'           => $old['brand'],
                'condition_type'  => $old['condition_type'],
                'listing_type'    => $old['listing_type'],
                'price'           => $old['listing_type'] === 'donate' ? null : (float) $old['price'],
                'is_negotiable'   => $old['listing_type'] === 'donate' ? false : (bool) $old['is_negotiable'],
                'address'         => $old['address'],
                'area'            => $old['area'],
                'city'            => $old['city'],
                'state'           => $old['state'],
                'pincode'         => $old['pincode'],
                'contact_number'  => $old['contact_number'],
                'whatsapp_number' => $old['whatsapp_number'],
            ]);

            insertProductImages($productId, $uploadedImages);

            header('Location: ' . BASE_URL . '/my-account.php?posted=1');
            exit;
        }
    }
}

$pageTitle = 'Sell or Donate an Item - UseAgain';
require_once __DIR__ . '/includes/header.php';
?>

<section style="padding-top:40px; padding-bottom:80px;">
  <div class="container" style="max-width:760px;">
    <h1 class="section-title" style="text-align:left;">Sell / Donate an Item</h1>
    <p class="section-sub" style="text-align:left; margin-bottom:30px;">List it for sale or give it away — your listing goes live instantly.</p>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <ul style="margin:0 0 0 20px;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>

    <form class="form-card" method="post" action="<?= BASE_URL ?>/post-product.php" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <div style="position:absolute; left:-9999px;" aria-hidden="true">
        <label for="<?= HONEYPOT_FIELD ?>">Leave this field empty</label>
        <input type="text" id="<?= HONEYPOT_FIELD ?>" name="<?= HONEYPOT_FIELD ?>" tabindex="-1" autocomplete="off">
      </div>

      <div class="form-row" style="margin-bottom:16px;">
        <label class="listing-toggle">
          <input type="radio" name="listing_type" value="sell" <?= $old['listing_type'] === 'sell' ? 'checked' : '' ?>> Sell
        </label>
        <label class="listing-toggle">
          <input type="radio" name="listing_type" value="donate" <?= $old['listing_type'] === 'donate' ? 'checked' : '' ?>> Donate
        </label>
      </div>

      <div class="form-group">
        <label for="title">Product Name *</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($old['title']) ?>" required>
      </div>

      <div class="form-group">
        <label for="description">Description *</label>
        <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($old['description']) ?></textarea>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="category_id">Main Category *</label>
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
            <option value="">Select category first</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="condition_type">Condition *</label>
          <select id="condition_type" name="condition_type" required>
            <?php foreach (['New', 'Like New', 'Good', 'Fair'] as $c): ?>
              <option value="<?= $c ?>" <?= $old['condition_type'] === $c ? 'selected' : '' ?>><?= $c ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="brand">Brand <span class="muted" style="font-weight:400;">(optional)</span></label>
          <input type="text" id="brand" name="brand" value="<?= htmlspecialchars($old['brand']) ?>" placeholder="e.g. Samsung, IKEA, Hero">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group" id="priceField" style="<?= $old['listing_type'] === 'donate' ? 'display:none' : '' ?>">
          <label for="price">Price (₹) *</label>
          <input type="number" id="price" name="price" min="0" step="1" value="<?= htmlspecialchars($old['price']) ?>">
        </div>
        <div class="form-group" id="negotiableField" style="<?= $old['listing_type'] === 'donate' ? 'display:none' : '' ?>">
          <label>Negotiable?</label>
          <label class="listing-toggle" style="margin-right:10px;"><input type="radio" name="is_negotiable" value="1" <?= $old['is_negotiable'] === '1' ? 'checked' : '' ?>> Yes</label>
          <label class="listing-toggle"><input type="radio" name="is_negotiable" value="" <?= $old['is_negotiable'] !== '1' ? 'checked' : '' ?>> No</label>
        </div>
      </div>

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

      <div class="form-group">
        <label>Photos * (up to <?= MAX_IMAGES_PER_PRODUCT ?>)</label>
        <div class="upload-dropzone" id="uploadDropzone">
          <p>Drag &amp; drop photos here, or click to browse</p>
          <input type="file" name="images[]" id="imagesInput" accept="image/jpeg,image/png,image/webp" multiple hidden>
        </div>
        <div class="small-muted" id="imageCount" style="margin-top:8px;">0 / <?= MAX_IMAGES_PER_PRODUCT ?> photos selected</div>
        <div class="image-preview-grid" id="imagePreviewGrid"></div>
      </div>

      <button type="submit" class="btn btn-primary btn-block" style="margin-top:10px;">Publish Listing</button>
    </form>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // ---- Category -> Subcategory (AJAX) ----
  var categorySelect = document.getElementById('category_id');
  var subcategorySelect = document.getElementById('subcategory_id');
  var preselectSubcategory = '<?= (int) $old['subcategory_id'] ?>';

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
      });
  }

  categorySelect.addEventListener('change', function () { loadSubcategories(this.value, null); });
  if (categorySelect.value) loadSubcategories(categorySelect.value, preselectSubcategory);

  // ---- Sell / Donate toggle ----
  var listingRadios = document.querySelectorAll('input[name="listing_type"]');
  var priceField = document.getElementById('priceField');
  var negotiableField = document.getElementById('negotiableField');
  listingRadios.forEach(function (r) {
    r.addEventListener('change', function () {
      var show = !(this.value === 'donate' && this.checked);
      priceField.style.display = show ? 'block' : 'none';
      if (negotiableField) negotiableField.style.display = show ? 'block' : 'none';
    });
  });

  // ---- Image upload preview / drag-drop ----
  var dropzone = document.getElementById('uploadDropzone');
  var input = document.getElementById('imagesInput');
  var grid = document.getElementById('imagePreviewGrid');
  var counter = document.getElementById('imageCount');
  var maxFiles = <?= MAX_IMAGES_PER_PRODUCT ?>;
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
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
