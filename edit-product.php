<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/image-upload.php';

if (!isUserLoggedIn()) {
    header('Location: ' . BASE_URL . '/login.php?redirect=' . urlencode('/edit-product.php?id=' . (int) ($_GET['id'] ?? 0)));
    exit;
}

$user = currentUser();
$productId = (int) ($_GET['id'] ?? $_POST['product_id'] ?? 0);
$product = $productId ? getProductByIdForUser($productId, $user['id']) : null;

if (!$product) {
    header('Location: ' . BASE_URL . '/my-account.php');
    exit;
}

$categories = getCategories();
$errors = [];
$old = [
    'category_id' => $product['category_id'], 'subcategory_id' => $product['subcategory_id'],
    'title' => $product['title'], 'description' => $product['description'], 'brand' => $product['brand'] ?? '',
    'condition_type' => $product['condition_type'], 'listing_type' => $product['listing_type'],
    'price' => $product['price'], 'is_negotiable' => $product['is_negotiable'] ? '1' : '',
    'address' => $product['address'], 'area' => $product['area'], 'city' => $product['city'],
    'state' => $product['state'], 'pincode' => $product['pincode'],
    'contact_number' => $product['contact_number'], 'whatsapp_number' => $product['whatsapp_number'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['category_id', 'subcategory_id', 'title', 'description', 'brand', 'condition_type', 'listing_type',
              'price', 'address', 'area', 'city', 'state', 'pincode', 'contact_number', 'whatsapp_number'] as $field) {
        $old[$field] = trim($_POST[$field] ?? '');
    }
    $old['is_negotiable'] = isset($_POST['is_negotiable']) ? '1' : '';
    $removeIds = array_map('intval', $_POST['remove_images'] ?? []);
    $coverId = (int) ($_POST['cover_image'] ?? 0);

    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Your session expired — please try submitting the form again.';
    } elseif (isRateLimited('edit_product')) {
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

        $existingCount = countProductImages($productId) - count($removeIds);
        $newFilesCount = count(array_filter($_FILES['images']['name'] ?? [], fn($n) => $n !== ''));
        if ($existingCount + $newFilesCount < 1) {
            $errors[] = 'A listing must have at least one photo.';
        }
        if ($existingCount + $newFilesCount > MAX_IMAGES_PER_PRODUCT) {
            $errors[] = 'You can have a maximum of ' . MAX_IMAGES_PER_PRODUCT . ' photos in total.';
        }

        $newImages = [];
        if (empty($errors) && $newFilesCount > 0) {
            $newImages = handleProductImageUploads($_FILES['images'], $errors);
        }

        if (empty($errors)) {
            updateProductFields($productId, [
                'category_id' => $category['id'], 'subcategory_id' => $subcategory['id'],
                'title' => $old['title'], 'description' => $old['description'], 'brand' => $old['brand'],
                'condition_type' => $old['condition_type'], 'listing_type' => $old['listing_type'],
                'price' => $old['listing_type'] === 'donate' ? null : (float) $old['price'],
                'is_negotiable' => $old['listing_type'] === 'donate' ? false : (bool) $old['is_negotiable'],
                'address' => $old['address'], 'area' => $old['area'], 'city' => $old['city'],
                'state' => $old['state'], 'pincode' => $old['pincode'],
                'contact_number' => $old['contact_number'], 'whatsapp_number' => $old['whatsapp_number'],
            ]);

            foreach ($removeIds as $imgId) {
                deleteProductImageById($imgId);
            }
            if ($newImages) {
                addImagesToProduct($productId, $newImages);
            }
            if ($coverId) {
                setCoverImage($productId, $coverId);
            }

            // If this listing had been rejected or sent back for changes, editing it
            // resubmits it for admin review. A live/approved listing stays live — routine
            // edits shouldn't force a seller's ad offline while waiting on re-review.
            if (in_array($product['status'], ['rejected', 'changes_requested'], true)) {
                $pdo = getDbConnection();
                $pdo->prepare('UPDATE products SET status = "pending", rejection_reason = NULL WHERE id = ?')->execute([$productId]);
                createNotification('admin', null, $productId, 'product_submitted', 'Listing "' . $old['title'] . '" was edited and resubmitted for review.');
            }

            header('Location: ' . BASE_URL . '/my-account.php?updated=1#listings');
            exit;
        }
    }
}

$images = getProductImageRows($productId);
$remainingSlots = max(0, MAX_IMAGES_PER_PRODUCT - count($images));

$pageTitle = 'Edit Listing - UseAgain';
require_once __DIR__ . '/includes/header.php';
?>

<section style="padding-top:40px; padding-bottom:80px;">
  <div class="container" style="max-width:760px;">
    <h1 class="section-title" style="text-align:left;">Edit Listing</h1>
    <p class="section-sub" style="text-align:left; margin-bottom:30px;">Update your listing details, photos, or availability.</p>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <ul style="margin:0 0 0 20px;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>

    <form class="form-card" method="post" action="<?= BASE_URL ?>/edit-product.php?id=<?= $productId ?>" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="product_id" value="<?= $productId ?>">

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
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= (string) $old['category_id'] === (string) $cat['id'] ? 'selected' : '' ?>><?= $cat['icon'] ?> <?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="subcategory_id">Sub Category *</label>
          <select id="subcategory_id" name="subcategory_id" required>
            <option value="<?= $old['subcategory_id'] ?>" selected><?= htmlspecialchars(getSubcategoryById($old['subcategory_id'])['name'] ?? 'Current') ?></option>
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
          <input type="text" id="brand" name="brand" value="<?= htmlspecialchars($old['brand']) ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group" id="priceField" style="<?= $old['listing_type'] === 'donate' ? 'display:none' : '' ?>">
          <label for="price">Price (₹) *</label>
          <input type="number" id="price" name="price" min="0" step="1" value="<?= htmlspecialchars((string) $old['price']) ?>">
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
        <label>Current Photos</label>
        <div class="image-preview-grid">
          <?php foreach ($images as $img): ?>
            <div class="image-preview edit-image-cell">
              <img src="<?= htmlspecialchars($img['url']) ?>" alt="">
              <label class="cover-radio">
                <input type="radio" name="cover_image" value="<?= $img['id'] ?>" <?= $img['is_primary'] ? 'checked' : '' ?>> Cover
              </label>
              <label class="remove-checkbox">
                <input type="checkbox" name="remove_images[]" value="<?= $img['id'] ?>"> Remove
              </label>
            </div>
          <?php endforeach; ?>
        </div>
        <p class="form-note">Check "Cover" to choose which photo shows first. You can have up to <?= MAX_IMAGES_PER_PRODUCT ?> photos total (<?= $remainingSlots ?> more allowed right now).</p>
      </div>

      <?php if ($remainingSlots > 0): ?>
        <div class="form-group">
          <label>Add More Photos</label>
          <div class="upload-dropzone" id="uploadDropzone">
            <p>Drag &amp; drop photos here, or click to browse</p>
            <input type="file" name="images[]" id="imagesInput" accept="image/jpeg,image/png,image/webp" multiple hidden>
          </div>
          <div class="small-muted" id="imageCount" style="margin-top:8px;">0 photos selected</div>
          <div class="image-preview-grid" id="imagePreviewGrid" data-max="<?= $remainingSlots ?>"></div>
        </div>
      <?php endif; ?>

      <button type="submit" class="btn btn-primary btn-block" style="margin-top:10px;">Save Changes</button>
      <a href="<?= BASE_URL ?>/my-account.php" class="btn btn-outline btn-block" style="margin-top:10px;">Cancel</a>
    </form>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var categorySelect = document.getElementById('category_id');
  var subcategorySelect = document.getElementById('subcategory_id');
  var preselectSubcategory = '<?= (int) $old['subcategory_id'] ?>';

  function loadSubcategories(categoryId, preselect) {
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

  var priceField = document.getElementById('priceField');
  var negotiableField = document.getElementById('negotiableField');
  document.querySelectorAll('input[name="listing_type"]').forEach(function (r) {
    r.addEventListener('change', function () {
      var show = !(this.value === 'donate' && this.checked);
      priceField.style.display = show ? 'block' : 'none';
      if (negotiableField) negotiableField.style.display = show ? 'block' : 'none';
    });
  });

  var dropzone = document.getElementById('uploadDropzone');
  if (!dropzone) return;
  var input = document.getElementById('imagesInput');
  var grid = document.getElementById('imagePreviewGrid');
  var counter = document.getElementById('imageCount');
  var maxFiles = parseInt(grid.dataset.max, 10) || 1;
  var selected = [];

  dropzone.addEventListener('click', function (e) { if (e.target !== input) input.click(); });
  ['dragenter', 'dragover'].forEach(function (evt) { dropzone.addEventListener(evt, function (e) { e.preventDefault(); dropzone.classList.add('is-dragover'); }); });
  ['dragleave', 'drop'].forEach(function (evt) { dropzone.addEventListener(evt, function (e) { e.preventDefault(); dropzone.classList.remove('is-dragover'); }); });
  dropzone.addEventListener('drop', function (e) { addFiles(e.dataTransfer.files); });
  input.addEventListener('change', function (e) { addFiles(e.target.files); });

  function addFiles(fileList) {
    Array.from(fileList || []).forEach(function (file) { if (selected.length < maxFiles) selected.push(file); });
    render(); sync();
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
        cell.querySelector('.remove-btn').addEventListener('click', function () { selected.splice(index, 1); render(); sync(); });
      };
      reader.readAsDataURL(file);
    });
    counter.textContent = selected.length + ' new photo(s) selected';
  }
  function sync() {
    var dt = new DataTransfer();
    selected.forEach(function (f) { dt.items.add(f); });
    input.files = dt.files;
  }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
