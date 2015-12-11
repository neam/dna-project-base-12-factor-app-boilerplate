To share your work through Ngrok:

1. Set LOCAL_OFFLINE_DATA to the data profile that should be shared (necessary since ngrok subdomains are a paid-account-only feature)

2. Run:

    ngrok http 192.168.99.100:80

3. Share the url shown on the Forwarding line, for instance:

    Forwarding                    http://90875954.ngrok.io -> 192.168.99.100:80
    
The URL to share in this example is http://90875954.ngrok.io.

## First time

1. Sign up on ngrok.com

2. Follow the getting started instructions at least steps 1 and 2, so that the latest ngrok is installed and your authtoken is configured
