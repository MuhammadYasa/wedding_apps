<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $invitation app\models\Invitation */
/* @var $guests app\models\Guest[] */

$this->title = 'Guests for: ' . $invitation->title;
?>
<div class="admin-guest-index container">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Export CSV', ['export', 'invitation_id' => $invitation->id], ['class' => 'btn btn-outline-secondary']) ?>
    </p>

    <h3>Generate Guests (paste list: one per line as "Name, email" or just "Name")</h3>
    <form method="post" action="<?= Url::to(['generate']) ?>">
        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
        <?= Html::hiddenInput('invitation_id', $invitation->id) ?>
        <div class="form-group">
            <textarea name="guest_lines" class="form-control" rows="6" placeholder="John Doe, john@example.com"></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Generate</button>
    </form>

    <hr>

    <h3>Existing Guests</h3>
    <table class="table table-sm">
        <thead>
            <tr><th>Name</th><th>Email</th><th>Link</th><th>Share</th><th>Viewed</th></tr>
        </thead>
        <tbody>
        <?php foreach ($guests as $g): 
            $link = Url::to(['invitation/view', 'slug' => $invitation->slug, 'token' => $g->token], true);
        ?>
            <tr>
                <td><?= Html::encode($g->name) ?></td>
                <td><?= Html::encode($g->email) ?></td>
                <td>
                    <input type="text" value="<?= Html::encode($link) ?>" readonly style="width:100%" id="link-<?= $g->id ?>">
                </td>
                <td>
                    <!-- Share via WhatsApp (opens WA Web / App). Uses JS to URL-encode message -->
                    <button class="btn btn-success btn-sm" onclick="openWhatsApp('<?= Html::encode($g->name) ?>','<?= Html::encode($link) ?>')">
                        Share via WhatsApp
                    </button>

                    <button class="btn btn-outline-secondary btn-sm" onclick="copyText('link-<?= $g->id ?>')">Copy Link</button>

                    <button class="btn btn-outline-primary btn-sm" onclick="copyMessage('<?= addslashes("Halo {$g->name},\nAnda diundang ke acara: {$invitation->title}\nBuka undangan: {$link}\nMohon konfirmasi kehadiran melalui link tersebut.\nTerima kasih.") ?>')">Copy Message</button>
                </td>
                <td><?= $g->viewed_at ? date('Y-m-d H:i', $g->viewed_at) : '-' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
/**
 * Open WhatsApp share window with encoded message.
 * For mobile, this will open WhatsApp app if available; on desktop opens WhatsApp Web.
 * Message template can be adjusted here.
 */
function openWhatsApp(name, link) {
    // Build message (customize as needed)
    var message = "Halo " + name + ",\n" +
                  "Anda diundang ke acara: <?= addslashes($invitation->title) ?>\n" +
                  "Buka undangan: " + link + "\n\n" +
                  "Mohon konfirmasi kehadiran melalui link tersebut.\nTerima kasih.";
    var encoded = encodeURIComponent(message);
    // wa.me without number opens WA with prefilled text
    var waUrl = "https://wa.me/?text=" + encoded;
    // Open in new tab/window
    window.open(waUrl, '_blank', 'noopener');
}

/**
 * Copy the value of an input element with given id
 */
function copyText(id) {
    var copyText = document.getElementById(id);
    if (!copyText) return alert('Element not found');
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    try {
        var ok = document.execCommand("copy");
        if (ok) alert("Link copied to clipboard");
        else alert("Copy failed. Please select and copy manually.");
    } catch (e) {
        alert("Copy not supported in this browser. Please select and copy manually.");
    }
}

/**
 * Copy provided message string to clipboard
 */
function copyMessage(text) {
    var el = document.createElement('textarea');
    el.value = text;
    document.body.appendChild(el);
    el.select();
    try {
        var ok = document.execCommand('copy');
        if (ok) alert('Message copied to clipboard');
        else alert('Copy failed. Please select and copy manually.');
    } catch (e) {
        alert('Copy not supported. Please select and copy manually.');
    }
    document.body.removeChild(el);
}
</script>