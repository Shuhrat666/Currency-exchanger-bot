# Copy token.example.php to token.php and add your bot token in it:
```bash
cp token.example.php token.php
```

# Run your local server port :
```bash
php -S localhost:8000
```

# Run ngrok with the same port :
```bash
ngrok http 8000
```

# Run the following command to run and start the bot:
```bash
curl -X POST "https://api.telegram.org/bot<your_token>/setWebhook?url=<your_ngrok_forwarding_link>/echo_bot.php"
```

# Start the bot :
Click or type '/start' to the bot and check ngrok htttp request. The things are going fine if the respond is '200 OK'.
Othervise, check your terminal commands, layouts and files.


