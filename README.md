<!-- GETTING STARTED -->
## Getting Started

### Prerequisites

- Docker
- Ruby
- Ultrahook
  ```sh 
  gem install ultrahook
  ```

### Installation

1. Clone the repo
   ```sh
   git clone git@github.com:JoeAshworth/BeastlyBot.git
   ```
2. Checkout Local (Local Development)
   ```sh
   git checkout local
   ```
3. Build Docker Image
   ```sh
   docker build -t beastlybot:local .
   ```
4. Run Docker Container
   ```sh
    docker run -p 8080:8000 beastlybot:local
   ```
5. Hit the site
   ```sh
    localhost:8080
   ```
6. Open a new terminal

7. Start Ultrahook
   ```
    ultrahook -k WjNV2nkyHAOizcKLGBp2uOrwVu3viAxc test 8080/stripe_webhooks
   ```
8. Change Webhook address to the output
   ```
    https://beastly-test.ultrahook.com
   ```

