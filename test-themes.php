<?php
/**
 * Test script to verify theme functionality
 * Access via: http://localhost:8080/test-themes.php
 */

require __DIR__ . '/vendor/autoload.php';

$config = require __DIR__ . '/config/web.php';
$application = new yii\web\Application($config);

echo "<h1>Wedding App - Theme Test</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background-color: #4CAF50; color: white; }
    tr:hover { background-color: #f5f5f5; }
    .theme-link { display: inline-block; padding: 8px 15px; margin: 5px; 
                  background: #2196F3; color: white; text-decoration: none; 
                  border-radius: 5px; }
    .theme-link:hover { background: #0b7dda; }
</style>";

// Check if theme CSS files exist
echo "<h2>1. Theme CSS Files Check</h2>";
$themes = ['default', 'elegant', 'rustic', 'modern'];
$allThemesExist = true;

echo "<table>";
echo "<tr><th>Theme</th><th>CSS File</th><th>Status</th></tr>";

foreach ($themes as $theme) {
    $cssPath = __DIR__ . "/web/css/themes/{$theme}.css";
    $exists = file_exists($cssPath);
    $allThemesExist = $allThemesExist && $exists;
    
    $status = $exists ? 
        "<span class='success'>✓ Exists</span>" : 
        "<span class='error'>✗ Missing</span>";
    
    echo "<tr>";
    echo "<td><strong>" . ucfirst($theme) . "</strong></td>";
    echo "<td>web/css/themes/{$theme}.css</td>";
    echo "<td>{$status}</td>";
    echo "</tr>";
}

echo "</table>";

if ($allThemesExist) {
    echo "<p class='success'>✓ All theme CSS files are present!</p>";
} else {
    echo "<p class='error'>✗ Some theme CSS files are missing!</p>";
}

// Check database invitations
echo "<h2>2. Invitations in Database</h2>";

try {
    $invitations = \app\models\Invitation::find()->all();
    
    if (empty($invitations)) {
        echo "<p class='error'>No invitations found in database!</p>";
    } else {
        echo "<table>";
        echo "<tr><th>ID</th><th>Title</th><th>Current Theme</th><th>Preview Links</th></tr>";
        
        foreach ($invitations as $invitation) {
            echo "<tr>";
            echo "<td>{$invitation->id}</td>";
            echo "<td>{$invitation->title}</td>";
            echo "<td><strong>" . ($invitation->theme ?? 'default') . "</strong></td>";
            echo "<td>";
            
            // Generate preview links for each theme
            foreach ($themes as $theme) {
                $url = "http://localhost:8080/index.php?r=invitation/view&slug={$invitation->slug}&preview_theme={$theme}";
                echo "<a href='{$url}' target='_blank' class='theme-link'>" . ucfirst($theme) . "</a> ";
            }
            
            echo "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p class='error'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test updating themes
echo "<h2>3. Update Invitation Themes</h2>";
echo "<p class='info'>Update invitation themes to test different styles:</p>";

if (!empty($invitations)) {
    echo "<form method='post' style='margin: 20px 0;'>";
    echo "<select name='invitation_id' required style='padding: 8px; margin-right: 10px;'>";
    echo "<option value=''>Select Invitation</option>";
    
    foreach ($invitations as $inv) {
        echo "<option value='{$inv->id}'>{$inv->title}</option>";
    }
    
    echo "</select>";
    echo "<select name='theme' required style='padding: 8px; margin-right: 10px;'>";
    echo "<option value=''>Select Theme</option>";
    
    foreach ($themes as $theme) {
        echo "<option value='{$theme}'>" . ucfirst($theme) . "</option>";
    }
    
    echo "</select>";
    echo "<button type='submit' name='update_theme' style='padding: 8px 20px; background: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;'>Update Theme</button>";
    echo "</form>";
}

// Handle theme update
if (isset($_POST['update_theme'])) {
    $invitationId = (int)$_POST['invitation_id'];
    $newTheme = $_POST['theme'];
    
    if ($invitationId && in_array($newTheme, $themes)) {
        $invitation = \app\models\Invitation::findOne($invitationId);
        
        if ($invitation) {
            $oldTheme = $invitation->theme ?? 'default';
            $invitation->theme = $newTheme;
            
            if ($invitation->save(false)) {
                echo "<p class='success'>✓ Successfully updated invitation '{$invitation->title}' from '{$oldTheme}' to '{$newTheme}'!</p>";
                echo "<p><a href='http://localhost:8080/index.php?r=invitation/view&slug={$invitation->slug}' target='_blank' class='theme-link'>View Updated Invitation</a></p>";
            } else {
                echo "<p class='error'>✗ Failed to update theme!</p>";
            }
        }
    }
}

// Responsive check
echo "<h2>4. Responsive CSS Check</h2>";
$invitationCss = file_get_contents(__DIR__ . '/web/css/invitation.css');

$hasTabletMedia = strpos($invitationCss, '@media (max-width: 768px)') !== false;
$hasMobileMedia = strpos($invitationCss, '@media (max-width: 576px)') !== false;
$hasLargeMedia = strpos($invitationCss, '@media (min-width: 1200px)') !== false;

echo "<ul>";
echo "<li>" . ($hasTabletMedia ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . " Tablet responsive (@media max-width: 768px)</li>";
echo "<li>" . ($hasMobileMedia ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . " Mobile responsive (@media max-width: 576px)</li>";
echo "<li>" . ($hasLargeMedia ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . " Large screen optimization (@media min-width: 1200px)</li>";
echo "</ul>";

// Animation check
echo "<h2>5. Animation & Polish Check</h2>";
$hasAnimations = strpos($invitationCss, '@keyframes fadeInUp') !== false;
$hasTransitions = strpos($invitationCss, 'transition:') !== false || strpos($invitationCss, 'transition-') !== false;
$hasHoverEffects = strpos($invitationCss, ':hover') !== false;

echo "<ul>";
echo "<li>" . ($hasAnimations ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . " CSS animations (@keyframes)</li>";
echo "<li>" . ($hasTransitions ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . " Smooth transitions</li>";
echo "<li>" . ($hasHoverEffects ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . " Hover effects</li>";
echo "</ul>";

// Summary
echo "<h2>Summary</h2>";
$allPassed = $allThemesExist && $hasTabletMedia && $hasMobileMedia && $hasAnimations && $hasTransitions && $hasHoverEffects;

if ($allPassed) {
    echo "<p class='success' style='font-size: 1.2em;'>✓ All Day 11 UI Polish & Themes features are implemented and working!</p>";
} else {
    echo "<p class='error' style='font-size: 1.2em;'>✗ Some features need attention. Check details above.</p>";
}

echo "<hr>";
echo "<p><em>Test completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>
