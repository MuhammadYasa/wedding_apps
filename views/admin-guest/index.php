<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $this yii\web\View */
/** @var $guests app\models\Guest[] */
/** @var $invitations app\models\Invitation[] */

$this->title = 'Kelola Tamu';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-guest-index">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-people-fill me-2"></i><?= Html::encode($this->title) ?></h1>
        <?= Html::a('<i class="bi bi-plus-circle me-1"></i> Tambah Tamu', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (empty($guests)): ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                    <p class="mt-3">Belum ada data tamu</p>
                    <?= Html::a('Tambah Tamu Pertama', ['create'], ['class' => 'btn btn-primary']) ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Telepon</th>
                                <th>Undangan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($guests as $index => $guest): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <i class="bi bi-person-fill me-1"></i>
                                        <?= Html::encode($guest->name) ?>
                                    </td>
                                    <td>
                                        <?php if ($guest->email): ?>
                                            <i class="bi bi-envelope me-1"></i>
                                            <?= Html::encode($guest->email) ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($guest->phone): ?>
                                            <i class="bi bi-telephone me-1"></i>
                                            <?= Html::encode($guest->phone) ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($guest->invitation): ?>
                                            <span class="badge bg-info">
                                                <?= Html::encode($guest->invitation->bride_name . ' & ' . $guest->invitation->groom_name) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <?php
                                            // Generate WhatsApp invitation link
                                            if ($guest->invitation) {
                                                $invitationUrl = Url::to([
                                                    '/invitation/view',
                                                    'slug' => $guest->invitation->slug,
                                                    'to' => $guest->name
                                                ], true); // true for absolute URL
                                                
                                                $whatsappMessage = "Assalamualaikum Warahmatullahi Wabarakatuh\n\n";
                                                $whatsappMessage .= "Kepada Yth. Bapak/Ibu/Saudara/i\n";
                                                $whatsappMessage .= "*" . $guest->name . "*\n\n";
                                                $whatsappMessage .= "Tanpa mengurangi rasa hormat, perkenankan kami mengundang Bapak/Ibu/Saudara/i untuk menghadiri acara pernikahan kami.\n\n";
                                                $whatsappMessage .= "Berikut link undangan digital kami:\n";
                                                $whatsappMessage .= $invitationUrl . "\n\n";
                                                $whatsappMessage .= "Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu.\n\n";
                                                $whatsappMessage .= "Terima kasih 🙏";
                                                
                                                $whatsappUrl = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $guest->phone ?: '') . '?text=' . urlencode($whatsappMessage);
                                            }
                                            ?>
                                            
                                            <?php if ($guest->invitation && $guest->phone): ?>
                                                <a href="<?= $whatsappUrl ?>" target="_blank" class="btn btn-success" title="Share via WhatsApp">
                                                    <i class="bi bi-whatsapp"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?= Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $guest->id], [
                                                'class' => 'btn btn-info',
                                                'title' => 'Lihat Detail'
                                            ]) ?>
                                            <?= Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $guest->id], [
                                                'class' => 'btn btn-warning',
                                                'title' => 'Edit'
                                            ]) ?>
                                            <?= Html::a('<i class="bi bi-trash"></i>', ['delete', 'id' => $guest->id], [
                                                'class' => 'btn btn-danger',
                                                'title' => 'Hapus',
                                                'data' => [
                                                    'confirm' => 'Apakah Anda yakin ingin menghapus tamu ini?',
                                                    'method' => 'post',
                                                ],
                                            ]) ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <p class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Total: <strong><?= count($guests) ?></strong> tamu
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
