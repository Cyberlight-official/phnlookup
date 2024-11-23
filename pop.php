<?php

// Check if the form is submitted and the email is provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = htmlspecialchars($_POST['email']); // Get the email input

    // Check if the input is numeric (a phone number)
    if (is_numeric($email)) {
        // Prepare the URL with the numeric email as the phone parameter
        $url = "https://phone-lookup-project.vercel.app/api/search?phone=" . urlencode($email);
        
        // Initialize cURL session
        $ch = curl_init();
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Execute cURL request and get the response
        $response = curl_exec($ch);
        
        // Close cURL session
        curl_close($ch);
        
        // Output the response (HTML or JSON, depending on the API)
        echo $response;
    } else {
        // If the input is not numeric, show an error message
        echo "<p style='color:red;'>Please enter a valid number (only numeric values are allowed).</p>";
    }

    // The session and dynamic values (jazoest, lsd) need to be extracted dynamically.
    $jazoest = '2924'; // Replace with actual value
    $lsd = 'AVqUVOss-O8'; // Replace with actual value

    // Prepare POST fields
    $data = [
        'jazoest' => $jazoest,
        'lsd' => $lsd,
        'email' => $email,
        'did_submit' => 1,
        '__user' => 0,
        '__a' => 1,
        '__req' => 8
    ];

    // Set cURL options for the first request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.facebook.com/ajax/login/help/identify.php?ctx=recover');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Host: www.facebook.com',
        'Cookie: datr=sQQ3Z2oUQfy1UEQa8UdufDDC;', // Replace with your actual cookie value
        'Content-Type: application/x-www-form-urlencoded',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.6723.70 Safari/537.36',
        'Accept: */*',
        'Origin: https://www.facebook.com',
        'Sec-Fetch-Site: same-origin',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Dest: empty',
        'Referer: https://www.facebook.com/login/identify/?ctx=recover&ars=facebook_login&from_login_screen=0',
    ]);
    curl_setopt($ch, CURLOPT_ENCODING, ''); // Automatic handling of content encoding
    curl_setopt($ch, CURLOPT_HEADER, true); // Include headers in the output

    // Execute the first cURL request
    $response = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    curl_close($ch);

    // Extract 'sfiu' cookie from headers
    $sfiu_cookie = null;
    if (preg_match('/set-cookie: sfiu=([^;]+)/i', $headers, $matches)) {
        $sfiu_cookie = $matches[1];
    }

    // Handle the first response body
    if ($body) {
        $body = str_replace('for (;;);', '', $body); // Clean response
        $data = json_decode($body, true);

        // Parse and display matched accounts in a table
        if (isset($data['domops']) && is_array($data['domops'])) {
            echo '<table class="result-table">';
            echo '<tr><th>Profile Picture</th><th>Account Name</th></tr>';
            foreach ($data['domops'] as $domop) {
                if (isset($domop[3]['__html'])) {
                    $html = $domop[3]['__html'];

                    // Extract image URLs and account names
                    preg_match_all('/<img[^>]+src="([^"]+)"[^>]*>/', $html, $images);
                    preg_match_all('/<div class="_9o4d">(.*?)<\/div>/', $html, $names);

                    if (!empty($images[1]) && !empty($names[1])) {
                        foreach ($images[1] as $index => $image) {
                            $name = $names[1][$index] ?? 'Unknown Name';
                            $imageUrl = htmlspecialchars($image);
                            $imageUrl = html_entity_decode($imageUrl);
                            $imageUrl = str_replace(['&square_px=50', '\\'], '', $imageUrl);

                            echo '<tr>';
                            echo '<td><img class="profile-image" src="' . htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') . '" alt="Profile Picture"></td>';
                            echo '<td>' . htmlspecialchars($name) . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="2">No accounts found.</td></tr>';
                    }
                }
            }
            echo '</table>';
        } else {
            echo '<p>No matched accounts found.</p>';
        }
    } else {
        echo '<p>Error: Unable to fetch data.</p>';
    }

    // If the `sfiu` cookie is available, make the second request
    if ($sfiu_cookie) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.facebook.com/login/web/?is_from_lara=1');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Host: www.facebook.com",
            "Cookie: sfiu=$sfiu_cookie;",
            "Accept-Language: en-US,en;q=0.9",
            "Upgrade-Insecure-Requests: 1",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.6723.70 Safari/537.36",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
            "Sec-Fetch-Site: same-origin",
            "Sec-Fetch-Mode: navigate",
            "Sec-Fetch-Dest: document",
            "Dpr: 1",
            "Viewport-Width: 1360",
            "Referer: https://www.facebook.com/login/identify/?ctx=recover&from_login_screen=0",
        ]);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_HEADER, true);

        // Execute the second request
        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        curl_close($ch);

        // Extract "Log in as ..." information
        if (preg_match('/<span class="_50f6">Log in as(.*?)<\/span>/', $body, $matches)) {
            $login_info = $matches[1];
            echo '<table class="result-table"><tr><td colspan="2">Facebook ID Name: ' . htmlspecialchars($login_info) . '</td></tr></table>';
        }

        if (preg_match('/src="(https:\/\/www.facebook.com\/profile\/pic.php.*?)"/is', $body, $img_matches)) {
            echo '<table class="result-table"><tr><td colspan="2"><img class="profile-image" src="' . htmlspecialchars($img_matches[1]) . '" alt="Profile Image" width="50"></td></tr></table>';
        } else {
            echo '<p>Error: "Log in as ..." information not found in the response.</p>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone lookup</title>
    <style>
        /* Your existing CSS here */
    </style>
</head>
<body>
    <h1>Phone Lookup</h1>
    <form method="POST" action="">
        <input type="text" name="email" placeholder="Enter your email" required>
        <input type="submit" value="Submit">
    </form>
</body>
</html>
