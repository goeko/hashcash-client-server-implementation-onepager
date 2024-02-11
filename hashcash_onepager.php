<?php

$difficulty = 4;

function verifyHashcashToken($nonce, $data, $difficulty) {
    $prefix = str_repeat('0', $difficulty);
    $hash = hash('sha256', $data . $nonce);
    return substr($hash, 0, $difficulty) === $prefix;
}

$data = "KWdq7rKAG3WzSp40bVAnVQZtth1yahZ5";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
    $hash = isset($_POST['hash']) ? $_POST['hash'] : '';
    if (verifyHashcashToken($nonce, $data, $difficulty)) {
        $responseMessage = "Token valid, request accepted.";
    } else {
        $responseMessage = "Invalid token, request denied.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hashcash Integrated Page</title>
    <style>
        /* CSS hier einfügen */
    </style>
</head>
<body>

<h1>Hashcash Integrated Page</h1>

<?php if (isset($responseMessage)): ?>
    <p><?php echo htmlspecialchars($responseMessage); ?></p>
<?php endif; ?>

<form id="hashcashForm" action="" method="POST">
    <input type="text" name="userInput" placeholder="Enter some text" required>
    <input type="hidden" name="nonce" id="nonceField">
    <input type="hidden" name="hash" id="hashField">
    <button type="submit">Submit</button>
</form>

<script>
    setTimeout(() => {
        async function generateHashcashToken(difficulty = 4, data = 'client-data') {
            const prefix = '0'.repeat(difficulty);
            let nonce = 0;
            let hash = '';
            do {
                nonce++;
                hash = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(data + nonce))
                    .then(digest => Array.from(new Uint8Array(digest)).map(b => b.toString(16).padStart(2, '0')).join(''));
            } while (!hash.startsWith(prefix));
            return { nonce: nonce, hash: hash };
        }

        document.getElementById('hashcashForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            const token = await generateHashcashToken(<?php echo $difficulty; ?>, '<?php echo $data; ?>');
            document.getElementById('nonceField').value = token.nonce;
            document.getElementById('hashField').value = token.hash;
            this.submit();
        });
    }, Math.random() * (2400 - 1750) + 1750);
</script>

</body>
</html>
