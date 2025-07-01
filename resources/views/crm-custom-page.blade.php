<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom iPOSpays Page</title>
    <link rel="stylesheet" href="{{ asset('assets/css/crm-payment/payment-loader.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        /* New loader styles */
        .payment-processing-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 200px;
            margin-top: 2rem;
        }

        .payment-loader {
            width: 80px;
            height: 80px;
            position: relative;
            margin-bottom: 1.5rem;
        }

        .payment-loader .circle {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 4px solid transparent;
            border-top-color: #4a6cf7;
            border-radius: 50%;
            animation: spin 1.5s linear infinite;
        }

        .payment-loader .circle:nth-child(2) {
            border-top-color: #34d399;
            animation-delay: 0.3s;
            width: 70%;
            height: 70%;
            top: 15%;
            left: 15%;
        }

        .payment-loader .circle:nth-child(3) {
            border-top-color: #f59e0b;
            animation-delay: 0.6s;
            width: 50%;
            height: 50%;
            top: 25%;
            left: 25%;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .payment-status {
            text-align: center;
            font-size: 1.2rem;
            color: #4a5568;
            margin-top: 1rem;
        }

        .payment-progress {
            width: 80%;
            max-width: 300px;
            height: 8px;
            background-color: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 1rem;
        }

        .payment-progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #4a6cf7, #34d399);
            animation: progress 2.5s ease-in-out infinite;
            border-radius: 4px;
        }

        @keyframes progress {
            0% { width: 0%; }
            50% { width: 100%; }
            100% { width: 0%; left: 100%; }
        }

        .payment-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #4a6cf7;
        }
    </style>
</head>

<body style="margin:0 !important">
    <div class="container">
        <h2 class="d-flex justify-content-center">Please Wait ...</h2>
        <div class="payment-processing-container loader d-none">
            <div class="payment-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                    <line x1="1" y1="10" x2="23" y2="10"></line>
                    <path d="M6 14h.01M10 14h.01"></path>
                </svg>
            </div>
            <div class="payment-loader">
                <div class="circle"></div>
                <div class="circle"></div>
                <div class="circle"></div>
            </div>
            <div class="payment-status">Processing your payment...</div>
            <div class="payment-progress">
                <div class="payment-progress-bar"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        (() => {
            let ConnectedToNoomerik = false;
            window.addEventListener('load', function() {
                window.parent.postMessage({
                    message: 'REQUEST_USER_DATA'
                }, '*');
            });
            async function verifySSOToken(ssoToken) {
                try {
                    const res = await fetch('/decrypt-sso', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            ssoToken,
                            type: 'customPage'
                        })
                    });
                    const data = await res.json();
                    if (data.status) {
                        window.location.href = "/";
                    } else {
                        $('#app').show();
                        alert(data.message || 'Token verification failed.');
                    }
                } catch (error) {
                    console.error("Error during token verification:", error);
                    alert('An error occurred while processing your request. Please try again later.');
                }
            }

            function toggleLoader(show) {
                const loaderElement = document.querySelector('.loader');
                if (show) {
                    loaderElement.classList.remove('d-none'); // Show the loader
                } else {
                    loaderElement.classList.add('d-none'); // Hide the loader
                }
            }
            window.addEventListener('message', async function(event) {
                const data = event.data;
                console.log(event);
                // Handle loader control
                if (data.message === 'REQUEST_USER_DATA_RESPONSE') {
                    toggleLoader(true);
                    await verifySSOToken(data.payload);
                    toggleLoader(false);
                }
            });
        })()
    </script>
</body>

</html>
