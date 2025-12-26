<?php

namespace app\helpers;

use Yii;
use yii\base\BaseObject;

/**
 * Email Helper for sending various wedding-related emails
 */
class EmailHelper extends BaseObject
{
    /**
     * Send invitation email to guest
     * 
     * @param \app\models\Invitation $invitation
     * @param \app\models\Guest $guest
     * @return bool
     */
    public static function sendInvitation($invitation, $guest)
    {
        try {
            $invitationUrl = \yii\helpers\Url::to(
                ['invitation/view', 'slug' => $invitation->slug, 'guest' => $guest->token],
                true
            );

            $sent = Yii::$app->mailer->compose(
                ['html' => 'invitation-html', 'text' => 'invitation-text'],
                [
                    'invitation' => $invitation,
                    'guest' => $guest,
                    'invitationUrl' => $invitationUrl,
                ]
            )
            ->setFrom([Yii::$app->params['senderEmail'] ?? 'noreply@yadevs.com' => Yii::$app->name])
            ->setTo($guest->email)
            ->setSubject('Undangan Pernikahan ' . $invitation->bride_nickname . ' & ' . $invitation->groom_nickname)
            ->send();

            if ($sent) {
                Yii::info("Invitation email sent to {$guest->email}", __METHOD__);
            } else {
                Yii::warning("Failed to send invitation email to {$guest->email}", __METHOD__);
            }

            return $sent;
        } catch (\Exception $e) {
            Yii::error("Error sending invitation email to {$guest->email}: " . $e->getMessage(), __METHOD__);
            return false;
        }
    }

    /**
     * Send RSVP confirmation email
     * 
     * @param \app\models\Rsvp $rsvp
     * @param \app\models\Invitation $invitation
     * @return bool
     */
    public static function sendRsvpConfirmation($rsvp, $invitation)
    {
        try {
            if (!$rsvp->guest_email) {
                Yii::warning("No email address for RSVP #{$rsvp->id}", __METHOD__);
                return false;
            }

            $sent = Yii::$app->mailer->compose(
                ['html' => 'rsvp-confirmation-html', 'text' => 'rsvp-confirmation-text'],
                [
                    'rsvp' => $rsvp,
                    'invitation' => $invitation,
                ]
            )
            ->setFrom([Yii::$app->params['senderEmail'] ?? 'noreply@yadevs.com' => Yii::$app->name])
            ->setTo($rsvp->guest_email)
            ->setSubject('Konfirmasi RSVP - ' . $invitation->bride_nickname . ' & ' . $invitation->groom_nickname)
            ->send();

            if ($sent) {
                Yii::info("RSVP confirmation email sent to {$rsvp->guest_email}", __METHOD__);
            } else {
                Yii::warning("Failed to send RSVP confirmation to {$rsvp->guest_email}", __METHOD__);
            }

            return $sent;
        } catch (\Exception $e) {
            Yii::error("Error sending RSVP confirmation to {$rsvp->guest_email}: " . $e->getMessage(), __METHOD__);
            return false;
        }
    }

    /**
     * Send batch invitations to multiple guests
     * 
     * @param \app\models\Invitation $invitation
     * @param \app\models\Guest[] $guests
     * @return array ['sent' => int, 'failed' => int, 'total' => int]
     */
    public static function sendBatchInvitations($invitation, $guests)
    {
        $result = ['sent' => 0, 'failed' => 0, 'total' => count($guests)];

        foreach ($guests as $guest) {
            if (!$guest->email) {
                $result['failed']++;
                continue;
            }

            if (self::sendInvitation($invitation, $guest)) {
                $result['sent']++;
            } else {
                $result['failed']++;
            }

            // Small delay to avoid rate limiting
            usleep(100000); // 0.1 second
        }

        return $result;
    }

    /**
     * Send reminder email to guests who haven't RSVP'd
     * 
     * @param \app\models\Invitation $invitation
     * @param \app\models\Guest $guest
     * @return bool
     */
    public static function sendRsvpReminder($invitation, $guest)
    {
        try {
            $invitationUrl = \yii\helpers\Url::to(
                ['invitation/view', 'slug' => $invitation->slug, 'guest' => $guest->token],
                true
            );

            $sent = Yii::$app->mailer->compose(
                ['html' => 'rsvp-reminder-html', 'text' => 'rsvp-reminder-text'],
                [
                    'invitation' => $invitation,
                    'guest' => $guest,
                    'invitationUrl' => $invitationUrl,
                ]
            )
            ->setFrom([Yii::$app->params['senderEmail'] ?? 'noreply@yadevs.com' => Yii::$app->name])
            ->setTo($guest->email)
            ->setSubject('Reminder: Konfirmasi Kehadiran - ' . $invitation->bride_nickname . ' & ' . $invitation->groom_nickname)
            ->send();

            if ($sent) {
                Yii::info("RSVP reminder sent to {$guest->email}", __METHOD__);
            }

            return $sent;
        } catch (\Exception $e) {
            Yii::error("Error sending RSVP reminder to {$guest->email}: " . $e->getMessage(), __METHOD__);
            return false;
        }
    }
}
